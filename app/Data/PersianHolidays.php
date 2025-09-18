<?php

namespace App\Data;

class PersianHolidays
{
    /**
     * Get fixed holidays that occur on the same date every year
     *
     * @param int $year
     * @return array
     */
    public static function getFixedHolidays($year)
    {
        return [
            // Nowruz (New Year) - 4 days
            "{$year}-1-1" => 'عید نوروز',
            "{$year}-1-2" => 'عید نوروز',
            "{$year}-1-3" => 'عید نوروز',
            "{$year}-1-4" => 'عید نوروز',
            
            // Sizdah Bedar
            "{$year}-1-13" => 'جشن سیزده به در',
            
            // Islamic Revolution Day
            "{$year}-11-22" => 'پیروزی انقلاب اسلامی',
            
            // Oil Nationalization Day
            "{$year}-12-29" => 'روز ملی شدن صنعت نفت',
        ];
    }
    
    /**
     * Get religious holidays that have fixed dates in the Persian calendar
     *
     * @param int $year
     * @return array
     */
    public static function getReligiousHolidays($year)
    {
        return [
            // Martyrdom of Fatima al-Masuma
            "{$year}-1-15" => 'شهادت حضرت فاطمه زهرا (س)',
            
            // Birthday of Imam Ali
            "{$year}-1-13" => 'زادروز حضرت علی (ع)', // Same as Sizdah Bedar, but also a religious occasion
            
            // Martyrdom of Imam Reza
            "{$year}-2-30" => 'شهادت حضرت رضا (ع)', // Last day of Ordibehesht
            
            // Birthday of Prophet Muhammad and Imam Sadiq
            "{$year}-3-17" => 'میلاد حضرت محمد و حضرت صادق (ع)',
            
            // Martyrdom of Imam Ali
            "{$year}-4-19" => 'شهادت حضرت علی (ع)',
            
            // Martyrdom of Imam Sadiq
            "{$year}-5-5" => 'شهادت حضرت صادق (ع)',
            
            // Martyrdom of Imam Kazim
            "{$year}-6-3" => 'شهادت حضرت کاظم (ع)',
            
            // Birthday of Imam Reza
            "{$year}-6-11" => 'زادروز حضرت رضا (ع)',
            
            // Martyrdom of Imam Askeri
            "{$year}-7-11" => 'شهادت حضرت عسکری (ع)',
            
            // Birthday of Imam Mahdi
            "{$year}-8-15" => 'زادروز حضرت معصومه (س) و جشن پیمان نو',
            
            // Martyrdom of Imam Mahdi's mother
            "{$year}-9-21" => 'شهادت حضرت معصومه (س)',
            
            // Birthday of Imam Mahdi
            "{$year}-10-15" => 'ولادت حضرت مهدی (عج)',
            
            // Martyrdom of Imam Hassan Askari
            "{$year}-11-25" => 'شهادت حضرت حسن عسکری (ع)',
        ];
    }
    
    /**
     * Get governmental holidays
     *
     * @param int $year
     * @return array
     */
    public static function getGovernmentalHolidays($year)
    {
        return [
            // Islamic Republic Day
            "{$year}-1-12" => 'روز جمهوری اسلامی',
            
            // Army Day
            "{$year}-2-17" => 'روز ارتش',
            
            // Police Day
            "{$year}-3-20" => 'روز نیروی انتظامی',
            
            // Cleric Day
            "{$year}-4-7" => 'روز قوه قضائیه',
            
            // Security Forces Day
            "{$year}-5-14" => 'روز نیروی دریایی',
            
            // Export Day
            "{$year}-5-30" => 'روز صنعت و معدن',
            
            // Health Day
            "{$year}-6-27" => 'روز تجلیل از اسرا و مفقودان',
            
            // Literacy Day
            "{$year}-7-1" => 'روز آموزش و پرورش',
            
            // Housing Day
            "{$year}-7-13" => 'روز بزرگداشت علامه مجلسی',
            
            // Insurance Day
            "{$year}-7-24" => 'روز بیمه',
            
            // Statistics Day
            "{$year}-8-13" => 'روز ملی کارآفرینی',
            
            // Charitable Works Day
            "{$year}-9-7" => 'روز نیکوکاری و کمک‌های خیریه',
            
            // Quran Day
            "{$year}-9-17" => 'روز قرآن و تلاوت',
            
            // Culture and Arts Day
            "{$year}-9-25" => 'روز پژوهش',
            
            // Rights Day
            "{$year}-9-27" => 'روز وحدت حوزه و دانشگاه',
            
            // Press Day
            "{$year}-11-5" => 'روز بزرگداشت خواجه نصیرالدین طوسی',
            
            // Book Day
            "{$year}-11-21" => 'روز کتاب و کتابخوانی',
            
            // Teacher Day
            "{$year}-11-25" => 'روز ملی معلم', // Same as Martyrdom of Imam Hassan Askari
        ];
    }
    
    /**
     * Get all holidays for a specific year
     *
     * @param int $year
     * @return array
     */
    public static function getAllHolidays($year)
    {
        return array_merge(
            self::getFixedHolidays($year),
            self::getReligiousHolidays($year),
            self::getGovernmentalHolidays($year)
        );
    }
    
    /**
     * Check if a specific date is a holiday
     *
     * @param string $date Date in Y-n-j format (e.g., "1404-6-24")
     * @return string|false Holiday name or false if not a holiday
     */
    public static function isHoliday($date)
    {
        $holidays = self::getAllHolidays(date('Y'));
        return $holidays[$date] ?? false;
    }
}