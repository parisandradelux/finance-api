<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $categories = [
            ['name' => 'Salario', 'type' => 'income', 'color' => '#4CAF50'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#2196F3'],
            ['name' => 'Supermercado', 'type' => 'expense', 'color' => '#F44336'],
            ['name' => 'Transporte', 'type' => 'expense', 'color' => '#FF9800'],
            ['name' => 'Entretenimiento', 'type' => 'expense', 'color' => '#9C27B0'],
        ];

        foreach ($categories as $cat) {
            $user->categories()->create($cat);
        }

        $categories = $user->categories()->get();

        for ($i = 0; $i < 20; $i++) {

            $category = $categories->random();

            $user->transactions()->create([
                'category_id' => $category->id,
                'amount' => rand(100, 5000) / 10,
                'description' => "Transacción de prueba #$i",
                'transaction_date' => now()->subDays(rand(0, 60))->format('Y-m-d'),
            ]);
        }
    }
}
