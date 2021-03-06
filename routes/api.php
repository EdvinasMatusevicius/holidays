<?php

use App\Http\Controllers\CountriesController;
use App\Http\Controllers\HolidaysController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/holidays/{countryCode}/{year}/{region?}',[HolidaysController::class,'getCountryHolidays']);

Route::get('/countries',[CountriesController::class,'getAllCountries']);
