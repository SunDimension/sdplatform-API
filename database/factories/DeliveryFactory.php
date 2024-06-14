<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Carrier;
use App\Models\Customer;
use App\Models\Delivery;

class DeliveryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Delivery::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'sales_order_number' => $this->faker->word(),
            'delivery_order_number' => $this->faker->word(),
            'delivery_date' => $this->faker->dateTime(),
            'carrier_id' => Carrier::factory(),
            'notes' => $this->faker->word(),
        ];
    }
}
