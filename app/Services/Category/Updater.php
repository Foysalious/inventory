<?php namespace App\Services\Category;


use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Models\Category;
use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;
use App\Traits\ModificationFields;

class Updater
{
    use ModificationFields, CdnFileManager, FileManager;
    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepositoryInterface;
    /**
     * @var CategoryPartnerRepositoryInterface
     */
    protected CategoryPartnerRepositoryInterface $partnerCategoryRepositoryInterface;
    protected $name;
    /**
     * @var Category
     */
    protected Category $category;
    protected $modifyBy, $thumb, $category_id, $updated_thumb;


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


    public function setCategory(Category $category): Updater
    {
        $this->category = $category;
        return $this;
    }

    public function setName($name): Updater
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $category_id
     * @return Updater
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
        return $this;
    }

    /**
     * @param mixed $thumb
     * @return Updater
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
        return $this;
    }

    /**
     * @return array
     */
    public function makeData()
    {
        return [
            'name' => $this->name,
            'thumb' => isset($this->updated_thumb) ? $this->updated_thumb : getCategoryDefaultThumb()
        ] + $this->modificationFields(false, true);
    }

    public function update()
    {
        if(isset($this->thumb)) $this->updated_thumb = $this->updateThumb();
        $this->setModifier($this->modifyBy);
        return $this->categoryRepositoryInterface->update($this->category, $this->makeData());
    }

    public function updateThumb()
    {
        $fileName = $this->getDeletionFileNameFromCDN($this->category, $this->category_id, 'thumb');
        $this->deleteImageFromCDN($fileName);
        list($file, $fileName) = [$this->thumb, $this->uniqueFileName($this->thumb, '_' . getFileName($this->thumb) . '_category_thumb')];
        return $this->saveFileToCDN($file, substr(getCategoryThumbFolder(), strlen(config('s3.url'))), $fileName);
    }
}
