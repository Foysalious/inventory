<?php namespace App\Services\Category;

use App\Models\Category;
use App\Models\CategoryPartner;
use Illuminate\Auth\Access\AuthorizationException;

class Authorization
{

    private Category $category;
    private int $partnerId;

    public function setPartnerId(int $partnerId): Authorization
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    public function setCategory(Category $category): Authorization
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @throws AuthorizationException
     */
    public function canUpdateOrDeleteThisCategory(): bool
    {
        /** @var CategoryPartner $category_partner */
        $category_partner =  $this->category->categoryPartner()->where('partner_id',$this->partnerId)->first();
        if(!$category_partner)
            throw new AuthorizationException("Not allowed to perform this action", 403);
        if($this->category->isPublishedForSheba() || $category_partner->isDefault())
             throw new AuthorizationException("Not allowed to perform this action", 403);
        return true;
    }
}
