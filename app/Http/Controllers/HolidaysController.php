<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
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
            $holidaysInDb = $this->holidayRepository->getHolidaysFromDb($request->year,$request->countryCode,$request->region);
            if($holidaysInDb){
                return (new ApiResponse)->success($holidaysInDb);
            }
            $holidaysFromApi = $this->holidayRepository->getYearHolidays($request->year,$request->countryCode,$request->region);
            if(!isset($holidaysFromApi['error'])){
                $this->holidayRepository->saveHolidaysToDb($request->year,$request->countryCode,$holidaysFromApi,$request->region);
                return (new ApiResponse)->success($holidaysFromApi);
            }
            return (new ApiResponse)->exception('Invalid request parameters');
        } catch (Exception $exception) {
            return (new ApiResponse)->exception($exception->getMessage());
        }
    }
}
