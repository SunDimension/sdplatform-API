<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;

class PermissionRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PermissionRole::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'permission_id' => Permission::factory(),
            'role_id' => Role::factory(),
        ];
    }
}
