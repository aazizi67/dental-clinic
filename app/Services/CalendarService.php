<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CalendarService
{
    private $baseUrl;
    
    public function __construct()
    {
        // Use holidayapi.ir as the base URL
        $this->baseUrl = 'https://holidayapi.ir';
    }
    
    /**
     * Get calendar events for a specific month
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getCalendarEvents($year, $month)
    {
        try {
            // Cache the results for 1 hour to reduce API calls
            $cacheKey = "calendar_events_{$year}_{$month}";
            
            return Cache::remember($cacheKey, 3600, function() use ($year, $month) {
                $events = [];
                
                // Get number of days in the month
                $daysInMonth = $this->getDaysInMonth($year, $month);
                
                // Fetch events for each day in the month
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $dayEvents = $this->getDayEvents($year, $month, $day);
                    $dateKey = "{$year}-{$month}-{$day}";
                    
                    if (!empty($dayEvents['events'])) {
                        $events[$dateKey] = $dayEvents['events'];
                    }
                }
                
                return $events;
            });
        } catch (\Exception $e) {
            Log::error('Calendar API Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get events for a specific day
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return array
     */
    public function getDayEvents($year, $month, $day)
    {
        try {
            // Cache the results for 1 hour to reduce API calls
            $cacheKey = "day_events_{$year}_{$month}_{$day}";
            
            return Cache::remember($cacheKey, 3600, function() use ($year, $month, $day) {
                $response = Http::get("{$this->baseUrl}/jalali/{$year}/{$month}/{$day}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    $events = [];
                    $holidays = [];
                    
                    if (isset($data['events']) && is_array($data['events'])) {
                        foreach ($data['events'] as $event) {
                            $events[] = [
                                'title' => $event['description'] ?? 'مناسبت',
                                'is_holiday' => $event['is_holiday'] ?? false,
                                'is_religious' => $event['is_religious'] ?? false,
                                'additional_description' => $event['additional_description'] ?? ''
                            ];
                            
                            if (!empty($event['is_holiday'])) {
                                $holidays[] = $event['description'] ?? 'تعطیل';
                            }
                        }
                    }
                    
                    return [
                        'events' => $events,
                        'holidays' => $holidays
                    ];
                }
                
                return [
                    'events' => [],
                    'holidays' => []
                ];
            });
        } catch (\Exception $e) {
            Log::error('Calendar Day API Error: ' . $e->getMessage());
            return [
                'events' => [],
                'holidays' => []
            ];
        }
    }
    
    /**
     * Get holidays for a specific year
     *
     * @param int $year
     * @return array
     */
    public function getHolidays($year)
    {
        try {
            // Cache the results for 24 hours to reduce API calls
            $cacheKey = "holidays_{$year}";
            
            return Cache::remember($cacheKey, 86400, function() use ($year) {
                $holidays = [];
                
                // For simplicity, we'll just return the default holidays
                // In a real implementation, you might want to fetch all holidays for the year
                return $this->getDefaultHolidays($year);
            });
        } catch (\Exception $e) {
            Log::error('Holidays API Error: ' . $e->getMessage());
            return $this->getDefaultHolidays($year);
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
     * Check if a Jalali year is a leap year
     */
    private function isLeapYear($year)
    {
        // Simplified leap year calculation for Jalali calendar
        return (($year + 38) * 31) % 128 < 31;
    }
    
    /**
     * Default holidays for when API is not available
     */
    private function getDefaultHolidays($year)
    {
        // Common Iranian holidays
        $holidays = [
            // New Year
            "{$year}-1-1" => true,
            "{$year}-1-2" => true,
            "{$year}-1-3" => true,
            "{$year}-1-4" => true,
            
            // Sizdah Bedar
            "{$year}-1-13" => true,
            
            // Islamic Revolution Day
            "{$year}-11-22" => true,
            
            // Oil Nationalization Day
            "{$year}-12-29" => true,
        ];
        
        return $holidays;
    }
}