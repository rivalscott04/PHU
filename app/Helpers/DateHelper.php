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
        
        // Jika format adalah d-m-Y, gunakan format khusus untuk bulan Indonesia
        if ($format === 'd-m-Y') {
            $bulanIndonesia = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            
            $day = $carbon->format('d');
            $month = $bulanIndonesia[$carbon->format('m')];
            $year = $carbon->format('Y');
            
            return "{$day} {$month} {$year}";
        }
        
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
    
    public static function formatIndonesiaWithMonth($date)
    {
        if (!$date) return '';
        
        $carbon = Carbon::parse($date);
        
        $bulanIndonesia = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        $day = $carbon->format('d');
        $month = $bulanIndonesia[$carbon->format('m')];
        $year = $carbon->format('Y');
        
        return "{$day} {$month} {$year}";
    }
} 