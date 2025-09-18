<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PersianCalendarService;

class CalendarController extends Controller
{
    protected $calendarService;

    public function __construct(PersianCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Display the calendar view
     */
    public function index()
    {
        return view('calendar.index');
    }

    /**
     * Get calendar events for a specific month
     */
    public function getMonthEvents($year, $month)
    {
        try {
            $events = $this->calendarService->getMonthEvents($year, $month);
            $holidays = $this->calendarService->getHolidays($year);
            
            return response()->json([
                'events' => $events,
                'holidays' => $holidays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'events' => [],
                'holidays' => []
            ]);
        }
    }

    /**
     * Get events for a specific day
     */
    public function getDayEvents($year, $month, $day)
    {
        try {
            $events = $this->calendarService->getDayEvents($year, $month, $day);
            $holidays = $this->calendarService->getDayHolidays($year, $month, $day);
            
            return response()->json([
                'events' => $events,
                'holidays' => $holidays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'events' => [],
                'holidays' => []
            ]);
        }
    }

    /**
     * Save a note or reminder for a specific date
     */
    public function saveNote(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time' => 'nullable|string|regex:/^[0-2][0-9]:[0-5][0-9]$/',
            'is_reminder' => 'boolean'
        ]);

        try {
            $note = $this->calendarService->saveNote(
                $request->date,
                $request->title,
                $request->description,
                $request->time,
                $request->is_reminder
            );

            return response()->json([
                'success' => true,
                'message' => 'یادداشت با موفقیت ذخیره شد',
                'note' => $note
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ذخیره یادداشت'
            ], 500);
        }
    }

    /**
     * Get notes for a specific date
     */
    public function getNotes($date)
    {
        try {
            $notes = $this->calendarService->getNotes($date);
            return response()->json($notes);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Delete a note
     */
    public function deleteNote(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        try {
            $this->calendarService->deleteNote($request->id);
            return response()->json([
                'success' => true,
                'message' => 'یادداشت با موفقیت حذف شد'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف یادداشت'
            ], 500);
        }
    }
}