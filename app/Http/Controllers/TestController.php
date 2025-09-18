<?php

namespace App\Http\Controllers;

use App\Services\ExternalCalendarService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $externalCalendarService;
    
    public function __construct(ExternalCalendarService $externalCalendarService)
    {
        $this->externalCalendarService = $externalCalendarService;
    }
    
    public function testExternalCalendar()
    {
        try {
            // Test getting events for a specific day
            $events = $this->externalCalendarService->getDayEvents(1404, 6, 24); // Shahrivar 24, 1404
            
            // Test getting holidays for a year
            $holidays = $this->externalCalendarService->getHolidays(1404);
            
            return response()->json([
                'day_events' => $events,
                'year_holidays' => array_slice($holidays, 0, 10), // First 10 holidays
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
    
    public function testCalendarSwitch()
    {
        // Test both services
        $localService = app(\App\Services\LocalCalendarService::class);
        $externalService = app(\App\Services\ExternalCalendarService::class);
        
        $year = 1404;
        $month = 6; // Shahrivar
        
        $localEvents = $localService->getDayEvents($year, $month, 24);
        $externalEvents = $externalService->getDayEvents($year, $month, 24);
        
        return response()->json([
            'local_service' => $localEvents,
            'external_service' => $externalEvents,
            'comparison' => [
                'local_events_count' => count($localEvents['events']),
                'external_events_count' => count($externalEvents['events'])
            ]
        ]);
    }
}