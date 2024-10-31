<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ReceiveOrder;
use App\Models\Store;
use App\Models\User;

class ReceiveOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReceiveOrder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'purchase_order_number' => $this->faker->word(),
            'receive_date' => $this->faker->dateTime(),
            'store_id' => Store::factory(),
            'vendor_id' => $this->faker->word(),
            'status' => $this->faker->word(),
            'created_by' => User::factory(),
            'modified_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
