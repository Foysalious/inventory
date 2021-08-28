<?php

use Carbon\Carbon;

if (!function_exists('convertNumbersToBangla')) {
    /**
     * @param float $number
     * @param bool $formatted
     * @param int $decimal
     * @return string
     */
    function convertNumbersToBangla(float $number, $formatted = true, $decimal = 2)
    {
        return en2bnNumber($formatted ? number_format($number, $decimal) : "$number");
    }
}

if (!function_exists('en2bnNumber')) {
    /**
     * @param  $number
     * @return string
     */
    function en2bnNumber($number)
    {
        $search_array  = [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", ".", "," ];
        $replace_array = [ "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০", ".", "," ];
        return str_replace($search_array, $replace_array, $number);
    }
}

if (!function_exists('convertTimezone')) {

    /**
     * @param Carbon|null $datetime
     * @param string $timezone
     * @return Carbon|null
     */
    function convertTimezone(?Carbon $datetime, string $timezone = 'Asia/Dhaka'): ?Carbon
    {
        if (!$datetime) {
            return null;
        }
        return $datetime->timezone($timezone);

    }
}
