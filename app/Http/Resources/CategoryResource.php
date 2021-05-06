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
            'sub_categories' => $this->getSubCategory($this->children),
        ];
    }

    private function getSubCategory($sub_categories)
    {
        $filtered_data = collect([]);
        $sub_categories->each(function ($each) use (&$filtered_data){
            $filtered_data [] =  $each->only('id', 'parent_id', 'name','thumb', 'banner', 'app_thumb', 'app_banner');
        });
        return $filtered_data;
    }


}
