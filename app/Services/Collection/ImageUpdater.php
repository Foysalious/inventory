<?php


namespace App\Services\Collection;


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

    private function deleteCollectionImageFromCDN($partner_id, $collection_id, $column_name = '')
    {
        $fileName = $this->collection_repo->getDeletionFileNameCollectionImageFromCDN($partner_id, $collection_id, $column_name);
        $this->deleteFileFromCDN($fileName);
    }

    public function updateImages($partner_id, $collection_id, $thumb, $banner, $app_thumb, $app_banner)
    {
        $collection_images = [];

        if(($thumb)) {
            $this->deleteCollectionImageFromCDN($partner_id, $collection_id, 'thumb');
            list($file, $fileName) = $this->updateCollectionImage($thumb, '_' . getFileName($thumb) . '_collection_thumb');
            $collection_images['thumb_link'] = $this->saveFileToCDN($file, getCollectionDefaultThumbFolder(), $fileName);
        }

        if(($banner)) {
            $this->deleteCollectionImageFromCDN($partner_id, $collection_id, 'banner');
            list($file, $fileName) = $this->updateCollectionImage($banner, '_' . getFileName($banner) . '_collection_banner');
            $collection_images['banner_link'] = $this->saveFileToCDN($file, getCollectionDefaultBannerFolder(), $fileName);
        }

        if(($app_thumb)) {
            $this->deleteCollectionImageFromCDN($partner_id, $collection_id, 'app_thumb');
            list($file, $fileName) = $this->updateCollectionImage($app_thumb, '_' . getFileName($app_thumb) . '_collection_app_thumb');
            $collection_images['app_thumb_link'] = $this->saveFileToCDN($file, getCollectionDefaultAppThumbFolder(), $fileName);
        }

        if(($app_banner)) {
            $this->deleteCollectionImageFromCDN($partner_id, $collection_id, 'app_banner');
            list($file, $fileName) = $this->updateCollectionImage($app_banner, '_' . getFileName($app_banner) . '_collection_app_banner');
            $collection_images['app_banner_link'] = $this->saveFileToCDN($file, getCollectionDefaultThumbFolder(), $fileName);
        }

        return $collection_images;
    }
}
