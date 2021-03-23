<?php


namespace App\Services\Collection;


use App\Constants\ImageConstants;
use App\Repositories\CollectionRepository;
use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;

class ImageUpdater
{
    use FileManager, CdnFileManager;

    protected $collection_repo;

    public function __construct(CollectionRepository $collectionRepository)
    {
        $this->collection_repo = $collectionRepository;
    }

    private function updateCollectionImage($file, $name)
    {
        return [$file, $this->uniqueFileName($file, $name)];
    }

    public function deleteAllCollectionImages($partner_id, $collection_id)
    {
        foreach (ImageConstants::COLLECTION_IMAGE_COLUMNS as $column_name)
        {
            $fileName = $this->collection_repo->getDeletionFileNameCollectionImageFromCDN($partner_id, $collection_id, $column_name);
            if(isset($fileName))
            {
                $storagePath = config('s3.url');
                $this->deleteFileFromCDN(substr($fileName, strlen($storagePath)));
            }
        }
    }

    public function deleteSingleCollectionImage($partner_id, $collection_id, $column_name)
    {
        $fileName = $this->collection_repo->getDeletionFileNameCollectionImageFromCDN($partner_id, $collection_id, $column_name);
        if(isset($fileName))
        {
            $storagePath = config('s3.url');
            $this->deleteFileFromCDN(substr($fileName, strlen($storagePath)));
        }
    }

    public function updateImages($partner_id, $collection_id, $thumb, $banner, $app_thumb, $app_banner)
    {
        $collection_images = [];

        if(isset($thumb)) {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'thumb');
            list($file, $fileName) = $this->updateCollectionImage($thumb, '_' . getFileName($thumb) . '_collection_thumb');
            $collection_images['thumb_link'] = $this->saveFileToCDN($file, getCollectionDefaultThumbFolder(), $fileName);
        }

        if(isset($banner)) {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'banner');
            list($file, $fileName) = $this->updateCollectionImage($banner, '_' . getFileName($banner) . '_collection_banner');
            $collection_images['banner_link'] = $this->saveFileToCDN($file, getCollectionDefaultBannerFolder(), $fileName);
        }

        if(isset($app_thumb)) {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'app_thumb');
            list($file, $fileName) = $this->updateCollectionImage($app_thumb, '_' . getFileName($app_thumb) . '_collection_app_thumb');
            $collection_images['app_thumb_link'] = $this->saveFileToCDN($file, getCollectionDefaultAppThumbFolder(), $fileName);
        }

        if(isset($app_banner)) {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'app_banner');
            list($file, $fileName) = $this->updateCollectionImage($app_banner, '_' . getFileName($app_banner) . '_collection_app_banner');
            $collection_images['app_banner_link'] = $this->saveFileToCDN($file, getCollectionDefaultThumbFolder(), $fileName);
        }

        return $collection_images;
    }
}
