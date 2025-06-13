<?php

// database/factories/UserFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $role = $this->faker->randomElement(['admin', 'dosen', 'mahasiswa']);
        $nim = $role === 'mahasiswa' ? $this->faker->unique()->numerify('20##########') : null;

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->userName() . '@gmail.com',
            'nim' => $nim,
            'role' => $role,
            'password' => Hash::make('password'), // default password
            'remember_token' => Str::random(10),
        ];
    }
}

