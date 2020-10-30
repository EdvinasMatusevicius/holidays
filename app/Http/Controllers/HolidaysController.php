<?php

namespace App\Http\Controllers;

use App\Repositories\HolidayRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HolidaysController extends Controller
{
    private $holidayRepository;

    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }
    public function getCountryHolidays(Request $request){
        try {
            $holidaysExistsInDb = $this->holidayRepository->checkIfHolidaysInDb($request->year,$request->countryCode,$request->region);
            if($holidaysExistsInDb){
                $holidaysFromDb = $this->getHolidaysFromDb($holidaysExistsInDb);
                //api response
            }
            $holidaysFromApi = $this->holidayRepository->getYearHolidays($request->year,$request->countryCode,$request->region);
            $this->holidayRepository->saveHolidaysToDb($request->year,$request->countryCode,$holidaysFromApi,$request->region);
            //api response
        } catch (Exception $exception) {
            //api response
        }
    }
}
