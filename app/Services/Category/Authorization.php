<?php namespace App\Services\Category;

use Illuminate\Auth\Access\AuthorizationException;

class Authorization
{
    private $category;
    private $partner;
    private $type;
    private $categoryPartner;

    public function setPartner($partner)
    {
        $this->partner = $partner;
        return $this;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setCategoryPartner($categoryPartner)
    {
        $this->categoryPartner = $categoryPartner;
        return $this;
    }

    public function check()
    {
        if($this->category->is_published_for_sheba || $this->categoryPartner->is_default)
             throw new AuthorizationException("Not allowed to ". $this->type . " this category", 403);
        $partner_category =  $this->category->categoryPartner->where('partner_id',$this->partner)->first();
        if(!$partner_category)
             throw new AuthorizationException("This category does not belong to this partner", 403);
        return true;
    }
}
