<?php

namespace App\Services;

use App\Data\PersianHolidays;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Log;

class LocalCalendarService
{
    /**
     * Get calendar events for a specific month using local data
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getCalendarEvents($year, $month)
    {
        try {
            $events = [];
            
            // Get number of days in the month
            $daysInMonth = $this->getDaysInMonth($year, $month);
            
            // Get all holidays for the year
            $allHolidays = PersianHolidays::getAllHolidays($year);
            
            // Add events for each day in the month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dateKey = "{$year}-{$month}-{$day}";
                
                // Check if this day is a Friday (weekly holiday)
                $vertaDate = Verta::createJalaliDate($year, $month, $day);
                if ($vertaDate->isFriday()) {
                    $events[$dateKey] = [
                        [
                            'title' => 'تعطیل',
                            'is_holiday' => true,
                            'is_religious' => false,
                            'description' => 'روز تعطیل رسمی'
                        ]
                    ];
                }
                // Check if this day is a special holiday
                else if (isset($allHolidays[$dateKey])) {
                    $events[$dateKey] = [
                        [
                            'title' => $allHolidays[$dateKey],
                            'is_holiday' => true,
                            'is_religious' => true, // Most Persian holidays are religious
                            'description' => 'مناسبت ملی'
                        ]
                    ];
                }
            }
            
            return $events;
        } catch (\Exception $e) {
            Log::error('Local Calendar Service Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get events for a specific day using local data
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return array
     */
    public function getDayEvents($year, $month, $day)
    {
        try {
            $events = [];
            $holidays = [];
            
            $dateKey = "{$year}-{$month}-{$day}";
            
            // Check if this day is a Friday (weekly holiday)
            $vertaDate = Verta::createJalaliDate($year, $month, $day);
            if ($vertaDate->isFriday()) {
                $events[] = [
                    'title' => 'تعطیل',
                    'is_holiday' => true,
                    'is_religious' => false,
                    'description' => 'روز تعطیل رسمی'
                ];
                $holidays[] = 'تعطیل';
            }
            // Check if this day is a special holiday
            else {
                $allHolidays = PersianHolidays::getAllHolidays($year);
                if (isset($allHolidays[$dateKey])) {
                    $events[] = [
                        'title' => $allHolidays[$dateKey],
                        'is_holiday' => true,
                        'is_religious' => true,
                        'description' => 'مناسبت ملی'
                    ];
                    $holidays[] = $allHolidays[$dateKey];
                }
            }
            
            return [
                'events' => $events,
                'holidays' => $holidays
            ];
        } catch (\Exception $e) {
            Log::error('Local Calendar Day Service Error: ' . $e->getMessage());
            return [
                'events' => [],
                'holidays' => []
            ];
        }
    }
    
    /**
     * Get all holidays for a specific year using local data
     *
     * @param int $year
     * @return array
     */
    public function getHolidays($year)
    {
        try {
            return PersianHolidays::getAllHolidays($year);
        } catch (\Exception $e) {
            Log::error('Local Holidays Service Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get number of days in a Jalali month
     */
    private function getDaysInMonth($year, $month)
    {
        // Standard days in Jalali months
        $daysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
        
        // For leap years, the last month (Esfand) has 30 days
        if ($this->isLeapYear($year) && $month === 12) {
            return 30;
        }
        
        return $daysInMonth[$month - 1] ?? 31;
    }
    
    /**
     * Check if a Jalali year is a leap year using Verta
     */
    private function isLeapYear($year)
    {
        try {
            return Verta::isLeapYear($year);
        } catch (\Exception $e) {
            // Fallback calculation
            return (($year + 38) * 31) % 128 < 31;
        }
    }
}