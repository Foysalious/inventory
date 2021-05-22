<?php namespace App\Services\Category;

use Illuminate\Auth\Access\AuthorizationException;

class Authorization
{
    private $category;
    private $partnerId;
    private $type;
    private $categoryPartner;

    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function setCategoryPartner($categoryPartner)
    {
        $this->categoryPartner = $categoryPartner;
        return $this;
    }

    /**
     * @throws AuthorizationException
     */
    public function canUpdateOrDeleteThisCategory()
    {
        $this->categoryPartner =  $this->category->categoryPartner()->where('partner_id', $this->partnerId)->first();
        if($this->category->is_published_for_sheba || $this->categoryPartner->is_default)
             throw new AuthorizationException("Not allowed to perform this action", 403);
        $partner_category =  $this->category->categoryPartner->where('partner_id',$this->partnerId)->first();
        if(!$partner_category)
             throw new AuthorizationException("Not allowed to perform this action", 403);
        return true;
    }
}
