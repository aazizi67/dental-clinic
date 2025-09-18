<?php

namespace App\Helpers;

use Hekmatinasser\Verta\Verta;

class PersianDateHelper
{
    /**
     * Convert Gregorian date to Jalali (Persian) date
     *
     * @param string $gregorianDate Gregorian date in Y-m-d format
     * @param string $format Output format (default: 'Y/m/d')
     * @return string Persian date in specified format
     */
    public static function toPersian($gregorianDate, $format = 'Y/m/d')
    {
        if (!$gregorianDate) {
            return '';
        }

        try {
            // Parse the Gregorian date
            $date = \DateTime::createFromFormat('Y-m-d', $gregorianDate);
            if (!$date) {
                return $gregorianDate; // Return original if parsing fails
            }

            // Extract components
            $year = (int) $date->format('Y');
            $month = (int) $date->format('m');
            $day = (int) $date->format('d');

            // Create a Verta instance from the Gregorian date
            $verta = Verta::createGregorianDate($year, $month, $day);
            
            // Format the date - Verta automatically converts to Jalali
            return $verta->format($format);
        } catch (\Exception $e) {
            // If parsing fails, return the original date
            return $gregorianDate;
        }
    }

    /**
     * Convert English digits to Persian digits
     *
     * @param string|int $number
     * @return string
     */
    public static function toPersianDigits($number)
    {
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return str_replace($englishDigits, $persianDigits, (string) $number);
    }

    /**
     * Get Persian month name
     *
     * @param int $monthNumber
     * @return string
     */
    public static function getPersianMonthName($monthNumber)
    {
        $months = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند'
        ];
        
        return $months[$monthNumber] ?? '';
    }
}