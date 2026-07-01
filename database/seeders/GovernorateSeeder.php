<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            'Damascus',
            'Rif Damascus',
            'Aleppo',
            'Homs',
            'Hama',
            'Daraa',
            'Deir ez-Zor',
            'Quneitra',
            'Latakia',
            'Tartus',
            'Idlib',
            'Raqqa',
            'Hasakah',
            'As-Suwayda'
        ];

        foreach ($governorates as $name) {
            Governorate::updateOrCreate(
                ['name' => $name]
            );
        }
    }
}
