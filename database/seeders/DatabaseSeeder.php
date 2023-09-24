<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'verify' => 1,
            'name' => 'Morty',
            'email' => 'admin@morty.net',
            'password' => Hash::make('00225588'),
        ]);
        \App\Models\User::factory()->create([
            'verify' => 1,
            'name' => 'Omar',
            'email' => 'admin@omar.net',
            'password' => Hash::make('12345678'),
        ]);

        \App\Models\User::factory(20)->create();

        \App\Models\Admin::factory()->create([
            'user_id' => 1,
            'owner' => 1,
        ]);
        \App\Models\Admin::factory()->create([
            'user_id' => 2,
            'owner' => 1,
        ]);

        \App\Models\Post::factory(40)->create();

        \App\Models\PostComment::factory(150)->create();
    }
}
