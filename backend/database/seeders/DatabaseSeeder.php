<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Luan Rodrigues',
            'email' => 'mail@mail.com',
            'password' => Hash::make('senha123')
        ]);

        $this->call([
            TemplateSeeder::class,
            SectorSeeder::class,
        ]);
    }
}
