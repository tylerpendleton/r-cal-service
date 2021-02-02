<?php

use App\Services\Calendar\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/busy', function () {
    $startDate = Carbon::now()->startOfDay();
    $endDate = Carbon::now()->endOfDay();
    $busyTimes = CalendarService::getCalendarBusyTimes($startDate, $endDate);

    return $busyTimes;
});

Route::get('/free', function () {
    return ['error' => 'Unable to process request'];
});
