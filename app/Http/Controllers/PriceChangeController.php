<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceChangeStoreRequest;
use App\Http\Requests\PriceChangeUpdateRequest;
use App\Http\Resources\PriceChangeCollection;
use App\Http\Resources\PriceChangeResource;
use App\Models\PriceChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PriceChangeController extends Controller
{
    public function index(Request $request)
    {
        $priceChanges = PriceChange::all();

        return new PriceChangeCollection($priceChanges);
    }

    public function pending(Request $request)
    {
        $priceChanges = PriceChange::where('branch_id', auth()->user()->branch_id)->get();
        return new PriceChangeCollection($priceChanges);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'comment' => ['nullable'],
            'status' => ['required', 'string'],
            'id' => ['required']
        ]);
        $priceChanges = PriceChange::findOrFail($validated['id']);
        $priceChanges->comment = $validated['comment'];
        $priceChanges->status = $validated['status'];
        $priceChanges->approved_by = auth()->user()->id;
        $priceChanges->approval_date = now();
        $priceChanges->save();

        if ($priceChanges->status == 'Approved') {
            DB::beginTransaction();

            try {
                // Step 1: Create a temporary table
                DB::statement("
                    CREATE TEMPORARY TABLE if not exists extracted_data (
                        store_id INT,
                        product_id int,
                        new_selling_price  DECIMAL(10, 2)
                    );
                ");

                // Step 2: Insert data into the temporary table using the recursive CTE
                $data = DB::select("
                    WITH RECURSIVE seq AS (
                        SELECT 0 AS n
                        UNION ALL
                        SELECT n + 1
                        FROM seq
                        WHERE n < (SELECT MAX(JSON_LENGTH(details)) - 1 FROM price_changes WHERE id = ?)
                    )
                    SELECT 
                         ri.store_id AS store_id,
                        JSON_UNQUOTE(JSON_EXTRACT(ri.details, CONCAT('$[', seq.n, '].product_id'))) AS product_id,
                        JSON_UNQUOTE(JSON_EXTRACT(ri.details, CONCAT('$[', seq.n, '].new_selling_price'))) AS new_selling_price
                    FROM 
                        price_changes ri
                    JOIN 
                        seq
                    ON 
                        seq.n < JSON_LENGTH(ri.details)
                    WHERE 
                        ri.id = ?;
                ", [$validated['id'],$validated['id']]);

                Log::debug($data);

                foreach ($data as $row) {
                    DB::table('extracted_data')->insert([
                        'store_id' => $row->store_id,
                        'product_id' => $row->product_id,
                        'new_selling_price' => $row->new_selling_price,
                    ]);
                }
        
                // Step 3: Update the store_item table using the temporary table
                DB::statement("
                    UPDATE store_items si
                    JOIN extracted_data ed
                    ON si.store_id = ed.store_id AND si.create_item_id = ed.product_id
                    SET si.selling_price = ed.new_selling_price;
                ");

                // Step 4: Drop the temporary table
                DB::statement("DROP TEMPORARY TABLE extracted_data;");

                DB::commit();
                return response()->json(['message' => 'Selling prices updated successfully']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return new PriceChangeResource($priceChanges);
    }

    public function store(PriceChangeStoreRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->user()->id;
        $priceChange = PriceChange::create($data);

        return new PriceChangeResource($priceChange);
    }

    public function show(Request $request, PriceChange $priceChange)
    {
        return new PriceChangeResource($priceChange);
    }

    public function update(PriceChangeUpdateRequest $request, PriceChange $priceChange)
    {
        $priceChange->update($request->validated());

        return new PriceChangeResource($priceChange);
    }

    public function destroy(Request $request, PriceChange $priceChange)
    {
        $priceChange->delete();

        return response()->noContent();
    }
}
