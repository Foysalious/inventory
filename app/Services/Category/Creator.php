<?php namespace App\Services\Category;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;
use App\Traits\ModificationFields;

class Creator
{
    use ModificationFields, FileManager, CdnFileManager;

    protected CategoryRepositoryInterface $categoryRepositoryInterface;
    protected CategoryPartnerRepositoryInterface $partnerCategoryRepositoryInterface;
    protected string $categoryName, $thumb_link;
    protected $thumb;
    protected int $partnerId;
    protected int|null $parentId;
    protected $modifyBy;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, CategoryPartnerRepositoryInterface $partnerCategoryRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->partnerCategoryRepositoryInterface = $partnerCategoryRepositoryInterface;
    }

    public function setModifyBy($modify_by)
    {
        $this->modifyBy = $modify_by;
        return $this;
    }

    public function setPartner($partner_id)
    {
       $this->partnerId =  $partner_id;
       return $this;
    }

    public function setName($category_name)
    {
        $this->categoryName = $category_name;
        return $this;
    }

    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
        return $this;
    }

    /**
     * @param int|null $parentId
     * @return Creator
     */
    public function setParentId(?int $parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }


    public function create()
    {
        $this->setModifier($this->modifyBy);
        if(isset($this->thumb)) $this->thumb_link = $this->makeThumb();
        if ($this->parentId !== null) {
            $sub_category = $this->createSubCategory($this->parentId);
            return  $this->createPartnerSubCategory($this->partnerId, $sub_category->id);
        } else {
            $master_category = $this->createMasterCategory();
            $sub_category = $this->createSubCategory($master_category->id);
            return  $this->createPartnerCategory($this->partnerId, $master_category->id, $sub_category->id);
        }

    }

    public function makeThumb()
    {
        list($file, $fileName) = [$this->thumb, $this->uniqueFileName($this->thumb, '_' . getFileName($this->thumb) . '_category_thumb')];
        return $this->saveFileToCDN($file, substr(getCategoryThumbFolder(), strlen(config('s3.url'))), $fileName);
    }

    public function createMasterCategory()
    {
        $master_category_data = [
            'parent_id' => null,
            'name' => $this->categoryName,
            'publication_status' => 1,
            'is_published_for_sheba' => 0,
            'thumb' => isset($this->thumb_link) ? $this->thumb_link : getCategoryDefaultThumb()
        ] + $this->modificationFields(true, false);

       return  $this->categoryRepositoryInterface->create($master_category_data);
    }

    public function createSubCategory($master_category_id)
    {
        $sub_category_data = [
            'parent_id' => $master_category_id,
            'name' => ($this->parentId === null) ? 'Sub None Category' : $this->categoryName,
            'publication_status' => 1,
            'is_published_for_sheba' => 0,
            'thumb' => isset($this->thumb_link) ? $this->thumb_link : getCategoryDefaultThumb()
        ] + $this->modificationFields(true, false);
        return  $this->categoryRepositoryInterface->create($sub_category_data);

    }


    public function createPartnerCategory($partner_id, $master_category_id, $sub_category_id)
    {
        $sub_category_data = [
            [
                'partner_id' => $partner_id,
                'category_id' => $master_category_id,
            ] + $this->modificationFields(true, false),
            [
                'partner_id' => $partner_id,
                'category_id' => $sub_category_id,
            ] + $this->modificationFields(true, false)

        ];

        return $this->partnerCategoryRepositoryInterface->insert($sub_category_data);

    }

    public function createPartnerSubCategory($partner_id, $sub_category_id)
    {
        $sub_category_data = [
                'partner_id' => $partner_id,
                'category_id' => $sub_category_id,
            ] + $this->modificationFields(true, false)
        ;
        return $this->partnerCategoryRepositoryInterface->insert($sub_category_data);
    }

}
