<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'filename' => $this->faker->word . '.pdf',
            'filepath' => '/files/test.pdf',
            'type' => 'pdf',
            'category' => $this->faker->randomElement(['images', 'documents']),
            'created_at' => now(),
        ];
    }
}
