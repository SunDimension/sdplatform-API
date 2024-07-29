<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\Warehouse;

class WarehouseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Warehouse::class;
    protected static $templates = [['Warehouse 1', 1, 'Address', '','Joseph','a@a.com','o8080809']];
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        //$template = $this->template;
        $template =  $this->faker->unique()->randomElement(self::$templates);
        return [
            'name' => $template[0],
            'branch_id' => $template[1],
            'warehouse_address' => $template[2],
            'zipcode' => $template[3],
            'contact_person' => $template[3],
            'email' => $template[4],
            'phone' => $template[1],
        ];
    }
}
