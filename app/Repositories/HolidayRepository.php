<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

class HolidayRepository{

    public function getYearHolidays(string $year,string $countryCode,?string $regionCode = null){
        $apiUrl = "https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForYear&year=$year&country=$countryCode&holidayType=public_holiday";
        $regionCode ? $apiUrl .= "&region=$regionCode" : '';
        return Http::get($apiUrl)->json();
    }
    public function saveHolidaysToDb(string $year,string $countryCode,$data,?string $region = null){

    }
    public function checkIfHolidaysInDb(string $year,string $countryCode,?string $region = null){
        // should return holidays data id
    }
    public function getHolidaysFromDb($id){

    }
}