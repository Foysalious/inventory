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
    const NAME = 'name';
    const CATEGORY_ID = 'category_id';
    const WARRANTY_UNIT = 'warranty_unit';
    const WARRANTY = 'warranty';
    const APP_THUMB = 'app_thumb';
    const SUB_CATEGORY_ID = 'sub_category_id';

    public static function getFieldsDisplayableNameInBangla() : array
    {
        return [
            self::STOCK => ['en' => 'Inventory', 'bn' => 'ইনভেন্টোরিঃ'],
            self::UNIT => ['en' => 'Unit', 'bn' => 'একক'],
            self::PRICE => ['en' => 'Price', 'bn' => 'ক্রয়মূল্যঃ'],
            self::VAT => ['en' => 'Vat', 'bn' => 'ভ্যাটঃ'],
            self::NAME => ['en' => 'Name', 'bn' => 'নাম'],
            self::CATEGORY_ID => ['en' => 'Category', 'bn' => 'ক্যাটাগরি'],
            self::WARRANTY_UNIT => ['en' => 'Warranty Unit', 'bn' => 'ওয়ারেন্টি একক'],
            self::WARRANTY => ['en' => 'Warranty', 'bn' => 'ওয়ারেন্টি '],
            self::APP_THUMB => ['en' => 'App Thumb', 'bn' => 'ছবি'],
            self::SUB_CATEGORY_ID => ['en' => 'Sub Category', 'bn' => 'সাব ক্যাটাগরি']
        ];
    }

    public static function fields()
    {
        return array_values((new ReflectionClass(__CLASS__))->getConstants());
    }
}
