<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PersianCalendarService
{
    private $holidayApiUrl;

    public function __construct()
    {
        $this->holidayApiUrl = 'https://holidayapi.ir';
    }

    /**
     * Get calendar events for a specific month
     */
    public function getMonthEvents($year, $month)
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
                    
                    if (!empty($dayEvents)) {
                        $events[$dateKey] = $dayEvents;
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
     */
    public function getDayEvents($year, $month, $day)
    {
        try {
            // Cache the results for 1 hour to reduce API calls
            $cacheKey = "day_events_{$year}_{$month}_{$day}";
            
            return Cache::remember($cacheKey, 3600, function() use ($year, $month, $day) {
                $response = Http::get("{$this->holidayApiUrl}/jalali/{$year}/{$month}/{$day}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    $events = [];
                    
                    if (isset($data['events']) && is_array($data['events'])) {
                        foreach ($data['events'] as $event) {
                            $events[] = [
                                'title' => $event['description'] ?? 'مناسبت',
                                'is_holiday' => $event['is_holiday'] ?? false,
                                'is_religious' => $event['is_religious'] ?? false,
                                'additional_description' => $event['additional_description'] ?? ''
                            ];
                        }
                    }
                    
                    return $events;
                }
                
                return [];
            });
        } catch (\Exception $e) {
            Log::error('Calendar Day API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get holidays for a specific year
     */
    public function getHolidays($year)
    {
        try {
            // Cache the results for 24 hours to reduce API calls
            $cacheKey = "holidays_{$year}";
            
            return Cache::remember($cacheKey, 86400, function() use ($year) {
                $holidays = [];
                
                // Get number of months in a year
                for ($month = 1; $month <= 12; $month++) {
                    $daysInMonth = $this->getDaysInMonth($year, $month);
                    
                    // Check each day in the month
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $dayEvents = $this->getDayEvents($year, $month, $day);
                        
                        foreach ($dayEvents as $event) {
                            if (!empty($event['is_holiday'])) {
                                $dateKey = "{$year}-{$month}-{$day}";
                                $holidays[$dateKey] = true;
                                break; // One holiday per day is enough
                            }
                        }
                    }
                }
                
                return $holidays;
            });
        } catch (\Exception $e) {
            Log::error('Holidays API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get holidays for a specific day
     */
    public function getDayHolidays($year, $month, $day)
    {
        $dayEvents = $this->getDayEvents($year, $month, $day);
        $holidays = [];
        
        foreach ($dayEvents as $event) {
            if (!empty($event['is_holiday'])) {
                $holidays[] = $event['title'] ?? 'تعطیل';
            }
        }
        
        return $holidays;
    }

    /**
     * Get number of days in a Jalali month
     */
    public function getDaysInMonth($year, $month)
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
    public function isLeapYear($year)
    {
        // Simplified leap year calculation for Jalali calendar
        return (($year + 38) * 31) % 128 < 31;
    }

    /**
     * Save a note or reminder for a specific date
     */
    public function saveNote($date, $title, $description, $time, $isReminder)
    {
        // In a real application, you would save this to a database
        // For now, we'll use the cache as a temporary storage
        $notes = Cache::get('calendar_notes', []);
        
        $note = [
            'id' => time() . rand(1000, 9999), // Simple unique ID
            'date' => $date,
            'title' => $title,
            'description' => $description,
            'time' => $time,
            'is_reminder' => $isReminder,
            'created_at' => now()->toISOString()
        ];
        
        $notes[] = $note;
        Cache::put('calendar_notes', $notes, 86400 * 30); // Cache for 30 days
        
        return $note;
    }

    /**
     * Get notes for a specific date
     */
    public function getNotes($date)
    {
        $notes = Cache::get('calendar_notes', []);
        return array_filter($notes, function($note) use ($date) {
            return $note['date'] === $date;
        });
    }

    /**
     * Delete a note
     */
    public function deleteNote($id)
    {
        $notes = Cache::get('calendar_notes', []);
        $notes = array_filter($notes, function($note) use ($id) {
            return $note['id'] != $id;
        });
        Cache::put('calendar_notes', $notes, 86400 * 30); // Cache for 30 days
    }

    /**
     * Get all notes
     */
    public function getAllNotes()
    {
        return Cache::get('calendar_notes', []);
    }
}