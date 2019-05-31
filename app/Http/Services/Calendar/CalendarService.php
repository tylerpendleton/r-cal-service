<?php

namespace App\Services\Calendar;

use Carbon\Carbon;
use Carbon\CarbonPeriod;


class CalendarService
{
    /**
     * Returns a collection of Calendar Busy times.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return void
     */
    public static function getCalendarBusyTimes(Carbon $startDate, Carbon $endDate)
    {
        $timezone = CalendarService::getCalendarTimezone(); 
        $startDate = $startDate->minute < 30 ? $startDate->startOfHour() : $startDate->minute(30);
        $period = CarbonPeriod::since($startDate->tz($timezone))->hours(1)->until($endDate->tz($timezone));
        $hoursBusy = [8,9,12,14,16];

        $dates = [];

        foreach ($period as $date) {
            
            $isBusy = array_search($date->hour, $hoursBusy);
    
            if($isBusy > -1) {
                $dates[] = collect([
                    'start_date' => $date,
                    'end_date' => $date->addHour()
                ]);
            }
        }

        return collect($dates);
    }

    /**
     * Returns the Calendar Timezone setting.
     *
     * @return String
     */
    public static function getCalendarTimezone(){
        return 'America/Los_Angeles';
    }
}
