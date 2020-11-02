<?php

namespace App\Repositories;

use App\Models\Country;

class CountriesRepository{

    public function getCountriesFromDb(){
        return Country::all();
    }
}