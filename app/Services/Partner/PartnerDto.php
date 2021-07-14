<?php namespace App\Services\Partner;

use Spatie\DataTransferObject\DataTransferObject;

class PartnerDto extends DataTransferObject
{
    public ?string $sub_domain;
    public ?float $vat_percentage;
    public int $id;
}
