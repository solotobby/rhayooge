<?php

namespace Database\Factories;

use App\Models\BusinessExecutive;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BusinessExecutive>
 */
class BusinessExecutiveFactory extends Factory
{
    protected $model = BusinessExecutive::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+234'.fake()->numerify('80########'),
            'code' => BusinessExecutive::generateUniqueCode($name),
            'invite_token' => BusinessExecutive::generateInviteToken(),
            'status' => 'active',
            'default_commission_rate' => 10,
            'bank_name' => 'Access Bank',
            'bank_account_number' => fake()->numerify('##########'),
            'bank_account_name' => $name,
            'invited_at' => now(),
            'last_active_at' => now(),
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => 'suspended',
        ]);
    }
}
