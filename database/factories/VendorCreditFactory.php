<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Vendor;
use App\Models\VendorCredit;
use App\Models\Warehouse;

class VendorCreditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VendorCredit::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'warehouse_id' => Warehouse::factory(),
            'credit_number' => $this->faker->word(),
            'purchase_order_number' => $this->faker->word(),
            'vendor_credit_date' => $this->faker->date(),
        ];
    }
}
