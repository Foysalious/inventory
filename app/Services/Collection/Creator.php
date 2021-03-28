<?php


namespace App\Services\Collection;


use App\Interfaces\CollectionRepositoryInterface;
use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;
use App\Traits\ModificationFields;
use App\Services\Collection\ImageCreator;

class Creator
{
    use ModificationFields, FileManager, CdnFileManager;

    protected $collection_image_links = array();

    protected $collectionRepositoryInterface;

    protected $image_creator;

    protected $name, $description, $partner_id, $is_published, $thumb, $banner, $app_thumb, $app_banner, $modify_by;


    private $data = [];

    public function __construct(CollectionRepositoryInterface $collectionRepositoryInterface, ImageCreator $image_creator)
    {
        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
        $this->image_creator = $image_creator;
    }

    /**
     * @param mixed $data
     * @return Creator
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param mixed $name
     * @return Creator
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $description
     * @return Creator
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param mixed $partner_id
     * @return Creator
     */
    public function setPartnerId($partner_id)
    {
        $this->partner_id = $partner_id;
        return $this;
    }

    /**
     * @param mixed $is_published
     * @return Creator
     */
    public function setIsPublished($is_published)
    {
        $this->is_published = $is_published;
        return $this;
    }

    /**
     * @param mixed $thumb
     * @return Creator
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
        return $this;
    }

    /**
     * @param mixed $banner
     * @return Creator
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * @param mixed $app_thumb
     * @return Creator
     */
    public function setAppThumb($app_thumb)
    {
        $this->app_thumb = $app_thumb;
        return $this;
    }

    /**
     * @param mixed $app_banner
     * @return Creator
     */
    public function setAppBanner($app_banner)
    {
        $this->app_banner = $app_banner;
        return $this;
    }


    public function create()
    {
        $this->collection_image_links = $this->image_creator->saveImages($this->thumb, $this->banner, $this->app_thumb, $this->app_banner);
        return $this->collectionRepositoryInterface->insert($this->makeDataForInsert());
    }

    public function makeDataForInsert() : array
    {
        /*
         * config('s3.url') will give us the S3 basic url and
         * getCollectionDefaultThumb() will give the rest of the URL after s3.url -> basic url.
         */
        return [
            'name' => $this->name,
            'description' => $this->description,
            'thumb' => $this->collection_image_links['thumb_link'] ?? config('s3.url').getCollectionDefaultThumb(),
            'banner' => $this->collection_image_links['banner_link'] ?? config('s3.url').getCollectionDefaultBanner(),
            'app_thumb' => $this->collection_image_links['app_thumb_link'] ?? config('s3.url').getCollectionDefaultAppThumb(),
            'app_banner' => $this->collection_image_links['app_banner_link'] ?? config('s3.url').getCollectionDefaultAppBanner(),
            'partner_id' => $this->partner_id,
            'is_published' => $this->is_published
        ] + $this->modificationFields(true, false);
    }

}
