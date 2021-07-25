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
        foreach ($units as $each) {
            Unit::create($this->withCreateModificationField($each));
        }
        dump('units data migrated successfully');
    }

    private function getData()
    {
        return [
            'ft' => [
                'name_bn' => 'ফুট',
                'name_en' => 'ft'
            ],
            'sft' => [
                'name_bn' => 'স্কয়ার ফিট',
                'name_en' => 'sft'
            ],
            'sq.m' => [
                'name_bn' => 'স্কয়ার মিটার',
                'name_en' => 'sq.m'
            ],
            'kg' => [
                'name_bn' => 'কেজি',
                'name_en' => 'kg'
            ],
            'piece' => [
                'name_bn' => 'পিস',
                'name_en' => 'piece'
            ],
            'km' => [
                'name_bn' => 'কিমি',
                'name_en' => 'km'
            ],
            'litre' => [
                'name_bn' => 'লিটার',
                'name_en' => 'litre'
            ],
            'meter' => [
                'name_bn' => 'মিটার',
                'name_en' => 'meter'
            ],
            'dozname_en' => [
                'name_bn' => 'ডজন',
                'name_en' => 'dozname_en'
            ],
            'dozon' => [
                'name_bn' => 'ডজন',
                'name_en' => 'dozname_en'
            ],
            'inch' => [
                'name_bn' => 'ইঞ্চি',
                'name_en' => 'inch'
            ],
            'bosta' => [
                'name_bn' => 'বস্তা',
                'name_en' => 'bosta'
            ],
            'unit' => [
                'name_bn' => 'টি',
                'name_en' => 'unit'
            ],
            'set' => [
                'name_bn' => 'সেট',
                'name_en' => 'set'
            ],
            'carton' => [
                'name_bn' => 'কার্টন',
                'name_en' => 'carton'
            ],
            'gauze' => [
                'name_bn' => 'গজ',
                'name_en' => 'gauze'
            ]
        ];
    }
}
