<?php namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class CategorySubResource extends JsonResource
{
    private $partnerId;
    public function __construct($resource, $partnerId) {
        parent::__construct($resource);
        $this->partnerId = $partnerId;
    }

    public function toArray($request)
    {

        $partner_id = $this->partner_id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sub_cat' => $this->children

        ];
    }


}
