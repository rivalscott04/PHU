<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function formatIndonesia($date, $format = 'd F Y')
    {
        if (!$date) return '';
        
        $carbon = Carbon::parse($date);
        
        // Set locale ke Indonesia
        $carbon->locale('id');
        
        return $carbon->translatedFormat($format);
    }
    
    public static function formatIndonesiaWithTime($date)
    {
        return self::formatIndonesia($date, 'd F Y H:i');
    }
    
    public static function formatIndonesiaShort($date)
    {
        return self::formatIndonesia($date, 'd/m/Y');
    }
} 