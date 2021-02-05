<?php

namespace Tests\Unit;

use App\Http\Services\Calendar\CalendarService;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class CalendarServiceTest
 */
class CalendarServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('Jan 1, 2021 12:00pm'));
    }
    /**
     * @dataProvider getCalendarFreeTimesProvider
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $busyTimes
     * @param array $expectedTimes
     */
    public function testGetCalendarFreeTimes(
        Carbon $startDate,
        Carbon $endDate,
        array $busyTimes,
        array $expectedTimes
    )
    {
        $responseDates = CalendarService::getCalendarFreeTimes($startDate, $endDate, $busyTimes);

        foreach ($responseDates as $index => $date) {
            $this->assertEquals($expectedTimes[$index]['start_date'], $date['start_date']);
            $this->assertEquals($expectedTimes[$index]['end_date'], $date['end_date']);
        }
    }

    /**
     * @return array[]
     */
    public function getCalendarFreeTimesProvider(): array
    {
        Carbon::setTestNow(Carbon::parse('Jan 1, 2021 12:00pm'));
        return [
            // Test default expectations as written
            // Expert is busy = to the return of CalendarService::getCalendarBusyTimes()
            [
                'startDate' => Carbon::now()->startOfDay(),
                'endDate' => Carbon::now()->endOfDay(),
                'busyTimes' => $this->getTestTimesArray([8, 9, 12, 14, 16], CalendarService::getCalendarTimezone()),
                'expectedTimes' => $this->getTestTimesArray([-4, -3, -2, 13, 14, 16, 18, 19, 21], 'America/New_York')
            ],

            // Test user in New York sets their search window from 10am to 7pm(limit) local time
            // Expert is busy at 8am, 9am, 1pm, and 2pm
            // Expected NY Free times are 1pm, 2pm, 3pm, and 6pm
            [
                'startDate' => Carbon::now()->timezone('America/New_York')->setHour(10),
                'endDate' => Carbon::now()->timezone('America/New_York')->setHour(18),
                'busyTimes' => $this->getTestTimesArray([8, 9, 13, 14], CalendarService::getCalendarTimezone()),
                'expectedTimes' => $this->getTestTimesArray([13, 14, 15, 18, 19], 'America/New_York'),
            ],

            // Test user in Denver sets their search window from 6am to 2pm(limit) local time
            // Expert is busy at 8am
            // Expected Denver Free times are 10am, 11am, 12pm, and 1pm
            [
                'startDate' => Carbon::now()->timezone('America/Denver')->setHour(6),
                'endDate' => Carbon::now()->timezone('America/Denver')->setHour(13),
                'busyTimes' => $this->getTestTimesArray([8], CalendarService::getCalendarTimezone()),
                'expectedTimes' => $this->getTestTimesArray([10, 11, 12, 13], 'America/Denver'),
            ],
        ];
    }

    /**
     * @param array $hours
     * @param string $timezone
     * @return array
     */
    private function getTestTimesArray(array $hours, string $timezone): array
    {
        $times = [];
        foreach ($hours as $hour) {
            $times[] = [
                'start_date' => Carbon::now()->tz($timezone)->setHour($hour)->startOfHour(),
                'end_date' => Carbon::now()->tz($timezone)->setHour($hour + 1)->startOfHour(),
            ];
        }

        return $times;
    }

}
