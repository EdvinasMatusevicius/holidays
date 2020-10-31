<?php

namespace App\Repositories;

use App\Models\Holiday;
use Illuminate\Support\Facades\Http;

class HolidayRepository{

    public function getYearHolidays(string $year,string $countryCode,?string $regionCode = null){
        $apiUrl = "https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForYear&year=$year&country=$countryCode&holidayType=public_holiday";
        $regionCode ? $apiUrl .= "&region=$regionCode" : '';
        return Http::get($apiUrl)->json();
    }
    public function saveHolidaysToDb(string $year,string $countryCode,$data,?string $region = null){
        $holidayData = new Holiday;
        $holidayData->year = $year;
        $holidayData->countryCode = $countryCode;
        $holidayData->data = $data;
        $region ? $holidayData->region = $region : null;
        $holidayData->save();
    }
    public function checkIfHolidaysInDb(string $year,string $countryCode,?string $region = null){
        // should return holidays data id
    }
    public function getHolidaysFromDb($id){

    }
}