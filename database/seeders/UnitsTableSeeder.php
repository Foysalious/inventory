<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::insert([
           ['name_en' => 'ft', 'name_bn' => 'ফুট'],
           ['name_en' => 'sft', 'name_bn' => 'স্কয়ার ফিট'],
           ['name_en' => 'sq.m', 'name_bn' => 'স্কয়ার মিটার'],
           ['name_en' => 'kg', 'name_bn' => 'কেজি'],
           ['name_en' => 'piece', 'name_bn' => 'পিস'],
           ['name_en' => 'km', 'name_bn' => 'কিমি'],
           ['name_en' => 'litre', 'name_bn' => 'লিটার'],
           ['name_en' => 'meter', 'name_bn' => 'মিটার'],
           ['name_en' => 'dozen', 'name_bn' => 'ডজন'],
           ['name_en' => 'dozen', 'name_bn' => 'ডজন'],
           ['name_en' => 'inch', 'name_bn' => 'ইঞ্চি'],
           ['name_en' => 'bosta', 'name_bn' => 'বস্তা'],
           ['name_en' => 'unit', 'name_bn' => 'টি'],
           ['name_en' => 'set', 'name_bn' => 'সেট'],
           ['name_en' => 'carton', 'name_bn' => 'কার্টন'],
        ]);
    }
}
