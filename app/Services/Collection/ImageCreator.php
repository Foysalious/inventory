<?php


namespace App\Services\Collection;


use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;

class ImageCreator
{
    use FileManager, CdnFileManager;

    private function prepareCollectionImage($file, $name)
    {
        return [$file, $this->uniqueFileName($file, $name)];
    }

    public function saveImages($thumb, $banner, $app_thumb, $app_banner) : array
    {
        $collection_images = [];

        if(($thumb)) {
            list($file, $fileName) = $this->prepareCollectionImage($thumb, '_' . getFileName($thumb) . '_collection_thumb');
            $collection_images['thumb_link'] = $this->saveFileToCDN($file, getCollectionDefaultThumbFolder(), $fileName);
        }

        if(($banner)) {
            list($file, $fileName) = $this->prepareCollectionImage($banner, '_' . getFileName($banner) . '_collection_banner');
            $collection_images['banner_link'] = $this->saveFileToCDN($file, getCollectionDefaultBannerFolder(), $fileName);
        }

        if(($app_thumb)) {
            list($file, $fileName) = $this->prepareCollectionImage($app_thumb, '_' . getFileName($app_thumb) . '_collection_app_thumb');
            $collection_images['app_thumb_link'] = $this->saveFileToCDN($file, getCollectionDefaultAppThumbFolder(), $fileName);
        }

        if(($app_banner)) {
            list($file, $fileName) = $this->prepareCollectionImage($app_banner, '_' . getFileName($app_banner) . '_collection_app_banner');
            $collection_images['app_banner_link'] = $this->saveFileToCDN($file, getCollectionDefaultThumbFolder(), $fileName);
        }

        return json_encode($collection_images);
    }
}
