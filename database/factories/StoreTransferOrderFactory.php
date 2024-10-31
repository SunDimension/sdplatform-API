<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ApprovalStage;
use App\Models\Branch;
use App\Models\Store;
use App\Models\StoreTransferOrder;
use App\Models\User;

class StoreTransferOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StoreTransferOrder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_number' => $this->faker->word(),
            'transfer_date' => $this->faker->dateTime(),
            'source_branch_id' => Branch::factory(),
            'source_store_id' => Store::factory(),
            'destination_branch_id' => Branch::factory(),
            'destination_store_id' => Store::factory(),
            'approval_stage_id' => ApprovalStage::factory(),
            'source_status' => $this->faker->word(),
            'source_date_approved' => $this->faker->dateTime(),
            'destination_status' => $this->faker->word(),
            'destination_date_approved' => $this->faker->dateTime(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
