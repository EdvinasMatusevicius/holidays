<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HolidaysController extends Controller
{
    public function getCountryHolidays(Request $request){
        try {
            $yearHolidays = Http::get("https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForYear&year=$request->year&country=$request->countryCode&holidayType=public_holiday")->json();
            dd($yearHolidays);
        } catch (Exception $exception) {
            
        }
    }
}
