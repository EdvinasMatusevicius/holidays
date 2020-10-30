<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $countriesList = Http::get('https://kayaposoft.com/enrico/json/v2.0/?action=getSupportedCountries');
        foreach($countriesList->json() as $country){
            $seedData = [];
            foreach($country as $key=>$data){
                $seedData[$key]=json_encode($data);
            }
            Country::create($seedData);
        }
    }
}
