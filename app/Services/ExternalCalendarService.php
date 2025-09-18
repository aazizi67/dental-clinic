<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ExternalCalendarService
{
    private $baseUrl;
    private $timeout;
    
    public function __construct()
    {
        // Use configured base URL for calendar API
        $this->baseUrl = Config::get('calendar.external.base_url', 'https://pnldev.com/api/calender');
        $this->timeout = Config::get('calendar.external.timeout', 30);
    }
    
    /**
     * Get calendar events for a specific month using external API
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getCalendarEvents($year, $month)
    {
        try {
            // Cache the results for 1 hour to reduce API calls
            $cacheKey = "external_calendar_events_{$year}_{$month}";
            $cacheDuration = Config::get('calendar.local.cache_duration', 3600);
            
            return Cache::remember($cacheKey, $cacheDuration, function() use ($year, $month) {
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
            Log::error('External Calendar Service Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get events for a specific day using external API
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
            $cacheKey = "external_day_events_{$year}_{$month}_{$day}";
            $cacheDuration = Config::get('calendar.local.cache_duration', 3600);
            
            return Cache::remember($cacheKey, $cacheDuration, function() use ($year, $month, $day) {
                $events = [];
                $holidays = [];
                
                // Make API request
                $response = Http::timeout($this->timeout)->get("{$this->baseUrl}?year={$year}&month={$month}&day={$day}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['status']) && $data['status'] === true && isset($data['result'])) {
                        $result = $data['result'];
                        
                        // Add holiday information if available
                        if (isset($result['holiday']) && $result['holiday'] === true) {
                            $events[] = [
                                'title' => 'تعطیل',
                                'is_holiday' => true,
                                'is_religious' => false,
                                'description' => 'روز تعطیل رسمی'
                            ];
                            $holidays[] = 'تعطیل';
                        }
                        
                        // Add events if available
                        if (isset($result['event']) && is_array($result['event'])) {
                            foreach ($result['event'] as $event) {
                                $events[] = [
                                    'title' => $event,
                                    'is_holiday' => isset($result['holiday']) && $result['holiday'] === true,
                                    'is_religious' => true, // Most Persian events are religious
                                    'description' => 'مناسبت ملی'
                                ];
                                
                                if (isset($result['holiday']) && $result['holiday'] === true) {
                                    $holidays[] = $event;
                                }
                            }
                        }
                    }
                }
                
                return [
                    'events' => $events,
                    'holidays' => $holidays
                ];
            });
        } catch (\Exception $e) {
            Log::error('External Calendar Day Service Error: ' . $e->getMessage());
            return [
                'events' => [],
                'holidays' => []
            ];
        }
    }
    
    /**
     * Get all holidays for a specific year using external API
     *
     * @param int $year
     * @return array
     */
    public function getHolidays($year)
    {
        try {
            // Cache the results for 1 day to reduce API calls
            $cacheKey = "external_holidays_{$year}";
            $cacheDuration = 86400; // 1 day
            
            return Cache::remember($cacheKey, $cacheDuration, function() use ($year) {
                $holidays = [];
                
                // Make API request for the entire year
                $response = Http::timeout($this->timeout)->get("{$this->baseUrl}?year={$year}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['status']) && $data['status'] === true && isset($data['result'])) {
                        $result = $data['result'];
                        
                        // Process each month
                        foreach ($result as $month => $days) {
                            // Process each day in the month
                            foreach ($days as $day => $dayData) {
                                if (isset($dayData['holiday']) && $dayData['holiday'] === true) {
                                    $dateKey = "{$year}-{$month}-{$day}";
                                    $holidays[$dateKey] = isset($dayData['event']) && is_array($dayData['event']) 
                                        ? implode(', ', $dayData['event']) 
                                        : 'تعطیل';
                                }
                            }
                        }
                    }
                }
                
                return $holidays;
            });
        } catch (\Exception $e) {
            Log::error('External Holidays Service Error: ' . $e->getMessage());
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
     * Check if a Jalali year is a leap year
     */
    private function isLeapYear($year)
    {
        // Simple leap year calculation for Jalali calendar
        return (($year + 38) * 31) % 128 < 31;
    }
}