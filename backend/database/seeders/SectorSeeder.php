<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sector::factory()
            ->count(10)
            ->sequence(fn (Sequence $sequence): array => [
                'order' => $sequence->index + 1,
            ])
            ->create(['template_id' => 1]);
    }
}
