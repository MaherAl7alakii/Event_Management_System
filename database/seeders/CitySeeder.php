<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Damascus' => ['Mazzeh', 'Kafr Sousa', 'Barzeh'],
            'Rif Damascus' => ['Douma', 'Harasta', 'Darayya'],
            'Aleppo' => ['Al-Shaar', 'Al-Hamdaniya', 'Salah al-Din'],
            'Homs' => ['Al-Waer', 'Karm al-Zeitoun', 'Inshaat'],
            'Hama' => ['Al-Hader', 'Al-Salamiyah', 'Mhardeh'],
            'Latakia' => ['Jableh', 'Qardaha', 'Al-Haffah'],
            'Tartus' => ['Baniyas', 'Safita', 'Dreikish'],
            'Idlib' => ['Maarrat al-Numan', 'Saraqib', 'Ariha'],
            'Raqqa' => ['Al-Thawrah', 'Al-Karamah', 'Tal Abyad'],
            'Deir ez-Zor' => ['Al-Mayadin', 'Al-Bukamal', 'Al-Quriyah'],
            'Hasakah' => ['Qamishli', 'Al-Malikiyah', 'Ras al-Ayn'],
            'Daraa' => ['Nawa', 'Izra', 'Busra al-Sham'],
            'Quneitra' => ['Khan Arnabeh', 'Baath City', 'Jaba'],
            'As-Suwayda' => ['Shahba', 'Salkhad', 'Qanawat'],
        ];

        foreach ($data as $governorateName => $cities) {

            $governorate = Governorate::where('name', $governorateName)->first();

            if (!$governorate) {
                continue;
            }

            foreach ($cities as $cityName) {
                City::updateOrCreate(
                    [
                        'name' => $cityName,
                        'governorate_id' => $governorate->id
                    ]
                );
            }
        }
    }
}
