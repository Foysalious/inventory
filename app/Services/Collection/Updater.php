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


    private $data = [];

    protected Collection $collection;

    public function __construct(CollectionRepositoryInterface $collectionRepositoryInterface)
    {
        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
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
        $this->setModifier($this->modify_by);
        return $this->collectionRepositoryInterface->update($this->collection, $this->makeDataForUpdate());
    }

    public function makeDataForUpdate() : array
    {
        return [
                'name' => $this->name,
                'description' => $this->description,
                'thumb' => $this->thumb,
                'banner' => $this->banner,
                'app_thumb' => $this->app_thumb,
                'app_banner' => $this->app_banner,
                'partner_id' => $this->partner_id,
                'is_published' => $this->is_published
            ] + $this->modificationFields(false, true);
    }
}
