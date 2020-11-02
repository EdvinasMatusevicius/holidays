<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Repositories\CountriesRepository;
use Exception;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    private $countriesRepository;

    public function __construct(CountriesRepository $countriesRepository)
    {
        $this->countriesRepository = $countriesRepository;
    }

    public function getAllCountries(){
        try {
            $allCountries = $this->countriesRepository->getCountriesFromDb();
            return (new ApiResponse)->success($allCountries);
        } catch (Exception $exception) {
            return (new ApiResponse)->exception($exception->getMessage());
        }
    }
}
