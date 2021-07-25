<?php namespace App\Services\DataMigration\migrations;

use App\Models\Unit;

class AddUnitsDataInUnitsTable extends DataMigrationBase implements DataMigrationInterface
{
    /**
     * Run the migrations.
     *
     * @return void | string
     */
    public function handle()
    {
        $units = $this->getData();
        Unit::insert($units);
        dump('units data migrated successfully');
    }

    private function getData()
    {
        return [
            [
                'name_bn' => 'ফুট',
                'name_en' => 'ft'
            ],
            [
                'name_bn' => 'স্কয়ার ফিট',
                'name_en' => 'sft'
            ],
            [
                'name_bn' => 'স্কয়ার মিটার',
                'name_en' => 'sq.m'
            ],
            [
                'name_bn' => 'কেজি',
                'name_en' => 'kg'
            ],
            [
                'name_bn' => 'পিস',
                'name_en' => 'piece'
            ],
            [
                'name_bn' => 'কিমি',
                'name_en' => 'km'
            ],
            [
                'name_bn' => 'লিটার',
                'name_en' => 'litre'
            ],
            [
                'name_bn' => 'মিটার',
                'name_en' => 'meter'
            ],
            [
                'name_bn' => 'ডজন',
                'name_en' => 'dozname_en'
            ],
            [
                'name_bn' => 'ডজন',
                'name_en' => 'dozname_en'
            ],
            [
                'name_bn' => 'ইঞ্চি',
                'name_en' => 'inch'
            ],
            [
                'name_bn' => 'বস্তা',
                'name_en' => 'bosta'
            ],
            [
                'name_bn' => 'টি',
                'name_en' => 'unit'
            ],
            [
                'name_bn' => 'সেট',
                'name_en' => 'set'
            ],
            [
                'name_bn' => 'কার্টন',
                'name_en' => 'carton'
            ],
            [
                'name_bn' => 'গজ',
                'name_en' => 'gauze'
            ]
        ];
    }
}
