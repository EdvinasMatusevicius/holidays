<?php

namespace App\Repositories;

use App\Models\Holiday;

class HolidayRepository{

    public function saveHolidaysToDb(string $year,string $countryCode,$data,?string $region = null){
        $holidayData = new Holiday;
        $holidayData->year = $year;
        $holidayData->countryCode = $countryCode;
        $holidayData->data = json_encode($data);
        $region ? $holidayData->region = $region : null;
        $holidayData->save();
    }
    public function getHolidaysFromDb(string $year,string $countryCode,?string $region = null){
        $holidayDbData = Holiday::where([
            ['year','=',$year],
            ['countryCode','=',$countryCode],
            ['region','=',$region],
        ])->pluck('data');
        if($holidayDbData->isEmpty()){
            return false;
        }
        return json_decode($holidayDbData[0],true);
    }
}