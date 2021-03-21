<?php namespace App\Services\Warranty;
use App\Helper\ConstGetter;
use App\Services\BaseService;

class Units extends BaseService
{
    use ConstGetter;

    const DAY = 'day';
    const WEEK = 'week';
    const MONTH = 'month';
    const YEAR = 'year';

    public function getAllWarrantyUnits($request)
    {
        try {
            $warranty_units     = [];
            $all_warranty_units = config('pos.warranty_unit');
            foreach ($all_warranty_units as $key => $unit) {
                array_push($warranty_units, $unit);
            }
            return $this->success("Successful", ['warranty_units' => $all_warranty_units]);
        }
        catch(\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }
}
