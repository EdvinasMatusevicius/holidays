<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Repositories\HolidayRepository;
use App\Services\HolidayDataServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HolidaysController extends Controller
{
    private $holidayRepository;
    private $holidayService;

    public function __construct(HolidayRepository $holidayRepository,HolidayDataServices $holidayService)
    {
        $this->holidayRepository = $holidayRepository;
        $this->holidayService = $holidayService;
    }
    public function getCountryHolidays(Request $request){
        try {
            $holidaysInDb = $this->holidayRepository->getHolidaysFromDb($request->year,$request->countryCode,$request->region);
            $currentDayStatus = $this->holidayService->getCurrentDayStatus($request->countryCode,$request->region);
            if($holidaysInDb){
                $holidaysInDb['currentDay'] = $currentDayStatus;
                return (new ApiResponse)->success($holidaysInDb);
            }
            $holidaysFromApi = $this->holidayService->getYearHolidays($request->year,$request->countryCode,$request->region);
            if(!isset($holidaysFromApi['error'])){
                $parcedHolidayData = $this->holidayService->parseHolidayData($holidaysFromApi,$request->countryCode,$request->region);
                $this->holidayRepository->saveHolidaysToDb($request->year,$request->countryCode,$parcedHolidayData,$request->region);
                $parcedHolidayData['currentDay'] = $currentDayStatus;

                return (new ApiResponse)->success($parcedHolidayData);
            }
            return (new ApiResponse)->exception('Invalid request parameters');
        } catch (Exception $exception) {
            return (new ApiResponse)->exception($exception->getMessage());
        }
    }
}
