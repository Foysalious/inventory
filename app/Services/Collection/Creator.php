<?php


namespace App\Services\Collection;


use App\Interfaces\CollectionRepositoryInterface;
use App\Traits\ModificationFields;
use Illuminate\Http\UploadedFile;

class Creator
{
    use ModificationFields;

    protected $collectionRepositoryInterface;

    protected $name, $description, $partner_id, $is_published, $thumb, $banner, $app_thumb, $app_banner, $modify_by, $sharding_id;

    /**
     * @param mixed $sharding_id
     * @return Creator
     */


    private $data = [];

    public function __construct(CollectionRepositoryInterface $collectionRepositoryInterface)
    {
        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
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

    /**
     * @param mixed $modify_by
     * @return Creator
     */
    public function setModifyBy($modify_by)
    {
        $this->modify_by = $modify_by;
        return $this;
    }

    public function setShardingId($sharding_id)
    {
        $this->sharding_id = $sharding_id;
        return $this;
    }

    public function create()
    {
        $this->setModifier($this->modify_by);
        return $this->collectionRepositoryInterface->insert($this->makeDataForInsert());
    }

    public function makeDataForInsert() : array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'thumb' => $this->thumb,
            'banner' => $this->banner,
            'app_thumb' => $this->app_thumb,
            'app_banner' => $this->app_banner,
            'partner_id' => $this->partner_id,
            'is_published' => $this->is_published,
            'sharding_id' => $this->sharding_id
        ] + $this->modificationFields(true, false);
    }

}
