<?php

namespace App\Helpers;

class GenderDetector
{
    /**
     * Common Persian/English names with their genders
     * @var array
     */
    private static $names = [
        // Male names - نام‌های مردانه
        'محمد' => 'male', 'احمد' => 'male', 'علی' => 'male', 'حسین' => 'male', 'رضا' => 'male',
        'حسن' => 'male', 'مهدی' => 'male', 'امیر' => 'male', 'ابراهیم' => 'male', 'ابوالفضل' => 'male',
        'سید' => 'male', 'سید محمد' => 'male', 'سید حسن' => 'male', 'سید رضا' => 'male', 'سید علی' => 'male',
        'سیدحسین' => 'male', 'سیدرضا' => 'male', 'سیدعلی' => 'male', 'سیدمهدی' => 'male', 'سیدامیر' => 'male',
        'سیدابراهیم' => 'male', 'سیدابوالفضل' => 'male', 'سیداحمد' => 'male',
        'محمدرضا' => 'male', 'احمدرضا' => 'male', 'علیرضا' => 'male', 'حسینرضا' => 'male', 'حسینعلی' => 'male',
        'رضا محمد' => 'male', 'رضا علی' => 'male', 'رضا حسین' => 'male', 'رضا احمد' => 'male', 'رضا ابراهیم' => 'male',
        'رضا امیر' => 'male', 'رضا مهدی' => 'male', 'رضا حسن' => 'male', 'رضا سید' => 'male',
        'محمد رضا' => 'male', 'محمد علی' => 'male', 'محمد حسین' => 'male', 'محمد احمد' => 'male', 'محمد ابراهیم' => 'male',
        'محمد امیر' => 'male', 'محمد مهدی' => 'male', 'محمد حسن' => 'male', 'محمد سید' => 'male',
        'احمد محمد' => 'male', 'احمد رضا' => 'male', 'احمد علی' => 'male', 'احمد حسین' => 'male', 'احمد ابراهیم' => 'male',
        'احمد امیر' => 'male', 'احمد مهدی' => 'male', 'احمد حسن' => 'male', 'احمد سید' => 'male',
        'علی محمد' => 'male', 'علی رضا' => 'male', 'علی حسین' => 'male', 'علی احمد' => 'male', 'علی ابراهیم' => 'male',
        'علی امیر' => 'male', 'علی مهدی' => 'male', 'علی حسن' => 'male', 'علی سید' => 'male',
        'حسین محمد' => 'male', 'حسین رضا' => 'male', 'حسین علی' => 'male', 'حسین احمد' => 'male', 'حسین ابراهیم' => 'male',
        'حسین امیر' => 'male', 'حسین مهدی' => 'male', 'حسین حسن' => 'male', 'حسین سید' => 'male',
        'مهدی محمد' => 'male', 'مهدی رضا' => 'male', 'مهدی علی' => 'male', 'مهدی حسین' => 'male', 'مهدی احمد' => 'male',
        'مهدی ابراهیم' => 'male', 'مهدی امیر' => 'male', 'مهدی حسن' => 'male', 'مهدی سید' => 'male',
        'امیر محمد' => 'male', 'امیر رضا' => 'male', 'امیر علی' => 'male', 'امیر حسین' => 'male', 'امیر احمد' => 'male',
        'امیر ابراهیم' => 'male', 'امیر مهدی' => 'male', 'امیر حسن' => 'male', 'امیر سید' => 'male',
        'ابراهیم محمد' => 'male', 'ابراهیم رضا' => 'male', 'ابراهیم علی' => 'male', 'ابراهیم حسین' => 'male', 'ابراهیم احمد' => 'male',
        'ابراهیم امیر' => 'male', 'ابراهیم مهدی' => 'male', 'ابراهیم حسن' => 'male', 'ابراهیم سید' => 'male',
        'ابوالفضل محمد' => 'male', 'ابوالفضل رضا' => 'male', 'ابوالفضل علی' => 'male', 'ابوالفضل حسین' => 'male', 'ابوالفضل احمد' => 'male',
        'ابوالفضل ابراهیم' => 'male', 'ابوالفضل امیر' => 'male', 'ابوالفضل مهدی' => 'male', 'ابوالفضل حسن' => 'male', 'ابوالفضل سید' => 'male',
        
        // Female names - نام‌های زنانه
        'فاطمه' => 'female', 'زهرا' => 'female', 'مریم' => 'female', 'نرگس' => 'female', 'زینب' => 'female',
        ' maryam' => 'female', 'fateme' => 'female', 'zahra' => 'female', 'maryam' => 'female', 'narges' => 'female',
        'zeynab' => 'female', 'sara' => 'female', 'elham' => 'female', 'hasti' => 'female', 'mina' => 'female',
        'sakineh' => 'female', 'hajar' => 'female', 'parisa' => 'female', 'shirin' => 'female', 'mahsa' => 'female',
        'sahar' => 'female', 'niloufar' => 'female', 'banafsheh' => 'female', 'farideh' => 'female', 'golnaz' => 'female',
        'kimia' => 'female', 'arezoo' => 'female', 'roshanak' => 'female', 'tahereh' => 'female', 'nasrin' => 'female',
        'farahnaz' => 'female', 'shahrzad' => 'female', 'katayoun' => 'female', 'shahla' => 'female', 'shahnaz' => 'female',
        'farangis' => 'female', 'shiva' => 'female', 'anahita' => 'female', 'bahar' => 'female', 'golshan' => 'female',
        'fariba' => 'female', 'shima' => 'female', 'shabnam' => 'female', 'shahrbanou' => 'female', 'shahrbano' => 'female',
        
        // English names - نام‌های انگلیسی
        'mohammad' => 'male', 'ahmad' => 'male', 'ali' => 'male', 'hosein' => 'male', 'reza' => 'male',
        'hasan' => 'male', 'mehdi' => 'male', 'amir' => 'male', 'ibrahim' => 'male', 'abolfazl' => 'male',
        'mohammadhosein' => 'male', 'mohammadreza' => 'male', 'ahmadreza' => 'male', 'alireza' => 'male', 'hoseinreza' => 'male',
        'mohammadali' => 'male', 'hoseinali' => 'male', 'rezaali' => 'male', 'rezaahmad' => 'male', 'rezaibrahim' => 'male',
        'rezaamir' => 'male', 'rezamehdi' => 'male', 'rezahasan' => 'male', 'rezasayyid' => 'male',
        'ahmadmohammad' => 'male', 'ahmadreza' => 'male', 'ahmadali' => 'male', 'ahmadhosein' => 'male', 'ahmadibrahim' => 'male',
        'ahmadamir' => 'male', 'ahmadmehdi' => 'male', 'ahmadhasan' => 'male', 'ahmadsayyid' => 'male', 'alimohammad' => 'male',
        'alireza' => 'male', 'alihosein' => 'male', 'aliahmad' => 'male', 'aliibrahim' => 'male', 'aliamir' => 'male',
        'alimehdi' => 'male', 'alihasan' => 'male', 'alisayyid' => 'male', 'hoseinmohammad' => 'male', 'hoseinreza' => 'male',
        'hoseinali' => 'male', 'hoseinahmad' => 'male', 'hoseinibrahim' => 'male', 'hoseinamir' => 'male', 'hoseinmehdi' => 'male',
        'hoseinhasan' => 'male', 'hoseinsayyid' => 'male', 'mehdimohammad' => 'male', 'mehdireza' => 'male', 'mehdiali' => 'male',
        'mehdihosein' => 'male', 'mehdiahamd' => 'male', 'mehdiibrahim' => 'male', 'mehdiamir' => 'male', 'mehdihasan' => 'male',
        'mehdisayyid' => 'male', 'amirmohammad' => 'male', 'amirreza' => 'male', 'amirali' => 'male', 'amirhosein' => 'male',
        'amirahmad' => 'male', 'amiribrahim' => 'male', 'amirmehdi' => 'male', 'amirhasan' => 'male', 'amirsayyid' => 'male',
        'ibrahimmohammad' => 'male', 'ibrahimreza' => 'male', 'ibrahimali' => 'male', 'ibrahimhosein' => 'male', 'ibrahimahmad' => 'male',
        'ibrahimamir' => 'male', 'ibrahimmehdi' => 'male', 'ibrahimhasan' => 'male', 'ibrahimsayyid' => 'male', 'abolfazlmohammad' => 'male',
        'abolfazlreza' => 'male', 'abolfazlali' => 'male', 'abolfazlhosein' => 'male', 'abolfazlahmad' => 'male', 'abolfazlibrahim' => 'male',
        'abolfazlamir' => 'male', 'abolfazlmehdi' => 'male', 'abolfazlhasan' => 'male', 'abolfazlsayyid' => 'male',
        
        'fatima' => 'female', 'fateme' => 'female', 'zahra' => 'female', 'maryam' => 'female', 'narges' => 'female',
        'zeynab' => 'female', 'sara' => 'female', 'elham' => 'female', 'hasti' => 'female', 'mina' => 'female',
        'sakineh' => 'female', 'hajar' => 'female', 'parisa' => 'female', 'shirin' => 'female', 'mahsa' => 'female',
        'sahar' => 'female', 'niloufar' => 'female', 'banafsheh' => 'female', 'farideh' => 'female', 'golnaz' => 'female',
        'kimia' => 'female', 'arezoo' => 'female', 'roshanak' => 'female', 'tahereh' => 'female', 'nasrin' => 'female',
        'farahnaz' => 'female', 'shahrzad' => 'female', 'katayoun' => 'female', 'shahla' => 'female', 'shahnaz' => 'female',
        'farangis' => 'female', 'shiva' => 'female', 'anahita' => 'female', 'bahar' => 'female', 'golshan' => 'female',
        'fariba' => 'female', 'shima' => 'female', 'shabnam' => 'female', 'shahrbanou' => 'female', 'shahrbano' => 'female'
    ];

    /**
     * Detect gender based on first name
     *
     * @param string $firstName
     * @return string|null
     */
    public static function detectGender($firstName)
    {
        if (empty($firstName)) {
            return null;
        }

        // Normalize the name (remove extra spaces)
        $normalizedName = trim($firstName);
        
        // Check for exact match (case sensitive for Persian names)
        if (isset(self::$names[$normalizedName])) {
            return self::$names[$normalizedName];
        }
        
        // Also check with lowercase for English names
        $lowercaseName = mb_strtolower($normalizedName, 'UTF-8');
        if (isset(self::$names[$lowercaseName])) {
            return self::$names[$lowercaseName];
        }
        
        // Check for partial matches (for compound names)
        foreach (self::$names as $name => $gender) {
            if (strpos($normalizedName, $name) !== false) {
                return $gender;
            }
            
            // Also check with lowercase
            if (strpos($lowercaseName, mb_strtolower($name, 'UTF-8')) !== false) {
                return $gender;
            }
        }
        
        // If no match found, return null
        return null;
    }
}