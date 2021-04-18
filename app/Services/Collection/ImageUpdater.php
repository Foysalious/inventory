<?php


namespace App\Services\Collection;


use App\Constants\ImageConstants;
use App\Interfaces\CollectionRepositoryInterface;
use App\Repositories\CollectionRepository;
use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;

class ImageUpdater
{
    use FileManager, CdnFileManager;

    protected $collection_repo, $collectionRepositoryInterface;

    public function __construct(CollectionRepository $collectionRepository, CollectionRepositoryInterface $collectionRepositoryInterface)
    {
        $this->collection_repo = $collectionRepository;
        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
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
            $mainFileName = $this->getMainFileName($fileName);

            if($mainFileName != 'default.jpg') $this->deleteFileFromCDN(substr($fileName, strlen(config('s3.url'))));
        }
    }

    public function deleteSingleCollectionImage($partner_id, $collection_id, $column_name)
    {
        $fileName = $this->collectionRepositoryInterface->getDeletionFileNameCollectionImageFromCDN($partner_id, $collection_id, $column_name);
        $mainFileName = $this->getMainFileName($fileName);

        if($mainFileName != 'default.jpg')
            $this->deleteFileFromCDN(substr($fileName, strlen(config('s3.url'))));
    }

    public function getMainFileName($fileName)
    {
        $storagePath = config('s3.url');
        $mainFileNameWithPath = substr($fileName, strlen($storagePath));
        return array_slice(explode('/', $mainFileNameWithPath), -1)[0];
    }

    public function updateImages($request, $partner_id, $collection_id, $thumb, $banner, $app_thumb, $app_banner)
    {
        $collection_images = [];
        $requestArray = $request->all();

        if(isset($thumb) && ($thumb != null )) {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'thumb');
            list($file, $fileName) = $this->updateCollectionImage($thumb, '_' . getFileName($thumb) . '_collection_thumb');
            $collection_images['thumb_link'] = $this->saveFileToCDN($file, getCollectionDefaultThumbFolder(), $fileName);
        }
        elseif(array_key_exists('thumb', $requestArray) && ($thumb) == null)
        {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'thumb');
            $collection_images['thumb_link'] = config('s3.url').getCollectionDefaultThumb();
        }

        if(isset($banner) && ($banner != null )) {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'banner');
            list($file, $fileName) = $this->updateCollectionImage($banner, '_' . getFileName($banner) . '_collection_banner');
            $collection_images['banner_link'] = $this->saveFileToCDN($file, getCollectionDefaultBannerFolder(), $fileName);
        }

        elseif(array_key_exists('banner', $requestArray) && ($banner) == null)
        {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'banner');
            $collection_images['banner_link'] = config('s3.url').getCollectionDefaultBanner();
        }

        if(isset($app_thumb) && ($app_thumb != null)) {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'app_thumb');
            list($file, $fileName) = $this->updateCollectionImage($app_thumb, '_' . getFileName($app_thumb) . '_collection_app_thumb');
            $collection_images['app_thumb_link'] = $this->saveFileToCDN($file, getCollectionDefaultAppThumbFolder(), $fileName);
        }

        elseif(array_key_exists('app_thumb', $requestArray) && ($app_thumb) == null)
        {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'app_thumb');
            $collection_images['app_thumb_link'] = config('s3.url').getCollectionDefaultAppThumb();
        }

        if(isset($app_banner) && ($app_banner != null)) {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'app_banner');
            list($file, $fileName) = $this->updateCollectionImage($app_banner, '_' . getFileName($app_banner) . '_collection_app_banner');
            $collection_images['app_banner_link'] = $this->saveFileToCDN($file, getCollectionDefaultThumbFolder(), $fileName);
        }

        elseif(array_key_exists('app_banner', $requestArray) && ($app_banner) == null)
        {
            $this->deleteSingleCollectionImage($partner_id, $collection_id, 'app_banner');
            $collection_images['app_banner_link'] = config('s3.url').getCollectionDefaultAppBanner();
        }

        return $collection_images;
    }
}
