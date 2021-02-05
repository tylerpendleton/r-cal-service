<?php

namespace App\Http\Services\Calendar;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CalendarService
{
    /**
     * Returns a collection of Calendar Busy times.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public static function getCalendarBusyTimes(Carbon $startDate, Carbon $endDate)
    {
        $timezone = self::getCalendarTimezone();
        $period = CarbonPeriod::since($startDate->startOfHour()->tz($timezone))->hours(1)->until($endDate->tz($timezone));
        $hoursBusy = [8, 9, 12, 14, 16];

        $dates = [];

        foreach ($period as $date) {
            $isBusy = array_search($date->hour, $hoursBusy);

            if ($isBusy > -1) {
                $dates[] = [
                    'start_date' => $date,
                    'end_date' => $date->copy()->addHour(),
                ];
            }
        }

        return $dates;
    }

    /**
     * Returns a collection of Calendar Free times.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $busyTimes
     * @return array
     */
    public static function getCalendarFreeTimes(
        Carbon $startDate,
        Carbon $endDate,
        array $busyTimes
    ): array
    {
        // The period we are searching in
        $period = CarbonPeriod::since($startDate->startOfHour()->tz(self::getCalendarTimezone()))
            ->hours(1)
            ->until($endDate->startOfHour()->tz(self::getCalendarTimezone()));

        // Experts are available 8:00 am to 8:00 pm, but not 8:00 - 9:00 pm, period is already in expert(calendar) timezone
        $period->addFilter(function($date) {
            return in_array($date->hour, self::getWorkingHours());
        }, 'workingHours');

        // get the busy hours as an array of hours
        $busyHours = [];
        foreach ($busyTimes as $busyTime) {
            $busyHours[] = Carbon::parse($busyTime['start_date'])->hour;
        }

        $freeTimes = [];
        foreach($period as $date) {
            // Filter out busy hours
            if (in_array($date->hour, $busyHours)) {
                continue;
            }

            $freeTimes[] = [
                'start_date' => $date,
                'end_date' => $date->copy()->addHour(),
            ];
        }

        return $freeTimes;
    }

    /**
     * Returns the Calendar Timezone setting.
     *
     * @return string
     */
    public static function getCalendarTimezone(): string
    {
        return 'America/Los_Angeles';
    }

    /**
     * Return an array of work hours - could be expanded to get these as carbon dates with expert timezone
     *
     * @return array
     */
    public static function getWorkingHours(): array
    {
        return range(8, 19);
    }
}
