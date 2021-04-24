<?php namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    private $partnerId;


    public function toArray($request)
    {
        $total_services = 0;
        $partner_id = $this->partner_id;
        $this->children()->get()->each(function ($child) use ($partner_id, &$total_services) {
            $total_services += $child->products()->where('partner_id', $partner_id)->count();
        });
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_published_for_sheba' => $this->is_published_for_sheba,
            'total_items' => $total_services,
            'icon'=>$this->app_thumb,
            'thumb' => $this->thumb,
            'banner'=> $this->banner,
        ];
    }


}
