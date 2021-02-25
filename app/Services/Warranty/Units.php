<?php namespace App\Services\Warranty;
use App\Helper\ConstGetter;

class Units
{
    use ConstGetter;

    const DAY = ['bn' => 'দিন', 'en' => 'day'];
    const WEEK = ['bn' => 'সপ্তাহ', 'en' => 'week'];
    const MONTH = ['bn' => 'মাস', 'en' => 'month'];
    const YEAR = ['bn' => 'বছর', 'en' => 'year'];

}
