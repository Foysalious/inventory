<?php

namespace App\Services\Product\Constants\Log;
use App\Helper\ConstGetter;
use ReflectionClass;

class FieldType
{
    use ConstGetter;

    const STOCK = 'stock';
    const UNIT = 'unit';
    const PRICE = 'price';
    const VAT = 'vat';

    public static function getFieldsDisplayableNameInBangla() : array
    {
        return [
            self::STOCK => ['en' => 'Inventory', 'bn' => 'ইনভেন্টোরিঃ'],
            self::UNIT => ['en' => 'Unit', 'bn' => 'একক'],
            self::PRICE => ['en' => 'Price', 'bn' => 'ক্রয়মূল্যঃ'],
            self::VAT => ['en' => 'Vat', 'bn' => 'ভ্যাটঃ']
        ];
    }

    public static function fields()
    {
        return array_values((new ReflectionClass(__CLASS__))->getConstants());
    }
}
