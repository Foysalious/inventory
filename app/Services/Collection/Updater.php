<?php


namespace App\Services\Collection;

use App\Interfaces\CollectionRepositoryInterface;
use App\Models\Collection;
use App\Traits\ModificationFields;

class Updater
{
    use ModificationFields;

    protected $collectionRepositoryInterface;

    protected $name, $description, $partner_id, $is_published, $thumb, $banner, $app_thumb, $app_banner, $modify_by, $sharding_id;

    protected $collection_id;

    protected $collection_updated_image_links = array();

    protected $collection_image_updater;

    private $data = [];

    protected Collection $collection;

    public function __construct(CollectionRepositoryInterface $collectionRepositoryInterface, ImageUpdater $updater)
    {
        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
        $this->collection_image_updater = $updater;
    }

    /**
     * @param mixed $collection_id
     * @return Updater
     */
    public function setCollectionId($collection_id)
    {
        $this->collection_id = $collection_id;
        return $this;
    }

    /**
     * @param Collection $collection
     * @return Updater
     */
    public function setCollection(Collection $collection): Updater
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @param mixed $data
     * @return Updater
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param mixed $name
     * @return Updater
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $description
     * @return Updater
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param mixed $partner_id
     * @return Updater
     */
    public function setPartnerId($partner_id)
    {
        $this->partner_id = $partner_id;
        return $this;
    }

    /**
     * @param mixed $is_published
     * @return Updater
     */
    public function setIsPublished($is_published)
    {
        $this->is_published = $is_published;
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
     * @param mixed $banner
     * @return Updater
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * @param mixed $app_thumb
     * @return Updater
     */
    public function setAppThumb($app_thumb)
    {
        $this->app_thumb = $app_thumb;
        return $this;
    }

    /**
     * @param mixed $app_banner
     * @return Updater
     */
    public function setAppBanner($app_banner)
    {
        $this->app_banner = $app_banner;
        return $this;
    }

    /**
     * @param mixed $modify_by
     * @return Updater
     */
    public function setModifyBy($modify_by)
    {
        $this->modify_by = $modify_by;
        return $this;
    }

    /**
     * @param mixed $sharding_id
     * @return Updater
     */
    public function setShardingId($sharding_id)
    {
        $this->sharding_id = $sharding_id;
        return $this;
    }

    public function update()
    {
        $this->collection_updated_image_links = $this->collection_image_updater->updateImages($this->partner_id, $this->collection_id, $this->thumb, $this->banner, $this->app_thumb, $this->app_banner);
        return $this->collectionRepositoryInterface->update($this->collection, $this->makeDataForUpdate());
    }

    public function makeDataForUpdate() : array
    {
        $data = [];
        if(isset($this->name)) $data['name'] = $this->name;
        if(isset($this->description)) $data['description'] = $this->description;
        if(isset($this->thumb)) $data['thumb'] = $this->collection_updated_image_links['thumb_link'];
        if(isset($this->banner)) $data['banner'] = $this->collection_updated_image_links['banner_link'];
        if(isset($this->app_thumb)) $data['app_thumb'] = $this->collection_updated_image_links['app_thumb_link'];
        if(isset($this->app_banner)) $data['app_banner'] = $this->collection_updated_image_links['app_banner_link'];
        if(isset($this->partner_id)) $data['partner_id'] = $this->partner_id;
        if(isset($this->is_published)) $data['is_published'] = $this->is_published;
        return $data + $this->modificationFields(false, true);
    }
}
