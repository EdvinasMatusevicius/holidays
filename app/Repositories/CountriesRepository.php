<?php

namespace App\Repositories;

use App\Models\Country;

class CountriesRepository{

    public function getCountriesFromDb(){
        $allCountries = Country::all();
        return json_decode($allCountries);
    }
}