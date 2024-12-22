<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topEuropeanCities = [
            'London', 'Paris', 'Berlin', 'Madrid', 'Rome',
            'Vienna', 'Barcelona', 'Munich', 'Milan', 'Prague',
            'Budapest', 'Warsaw', 'Dublin', 'Amsterdam', 'Brussels',
            'Stockholm', 'Copenhagen', 'Lisbon', 'Helsinki', 'Oslo'
        ];

        foreach ($topEuropeanCities as $city) {
            Location::create(['name' => $city]);
        }
    }
}
