<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class HolidayDataServices {

    public function getYearHolidays(string $year,string $countryCode,?string $regionCode = null){
        $apiUrl = "https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForYear&year=$year&country=$countryCode&holidayType=public_holiday";
        $regionCode ? $apiUrl .= "&region=$regionCode" : '';
        return Http::get($apiUrl)->json();
    }
    public function parseHolidayData(array $data,string $countryCode,?string $region = null){
        $totalPublicHolidays = array_reduce($data, "countPublicHolidays", 0);
    }
    private function groupByMonth(array $data){

    }
    private function countPublicHolidays($days,$holiday){
        if($holiday['holidayType']=='"public_holiday"'){
            $days +=1;
        }
        return $days;
    }
    private function getCurrentDayStatus(string $countryCode,?string $region = null){
        date('D-m-y');
    }
    private function calcMaxFreeDaysInRow(array $data){

    }
    private function isDayWorkDay($day,$month,$year,$countryCode,$region){
        $apiUrl = "https://kayaposoft.com/enrico/json/v2.0/?action=isWorkDay&date=$day-$month-$year&country=$countryCode";
        $region ? $apiUrl .= "&region=$region" : '';
        return Http::get($apiUrl)->json();
    }
    private function isDayHoliday($day,$month,$year,$countryCode,$region){
        $apiUrl = "https://kayaposoft.com/enrico/json/v2.0?action=isPublicHoliday&date=$day-$month-$year&country=$countryCode";
        $region ? $apiUrl .= "&region=$region" : '';
        return Http::get($apiUrl)->json();
    }
}