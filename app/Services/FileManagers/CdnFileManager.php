<?php namespace App\Services\FileManagers;


use Illuminate\Support\Facades\Storage;

trait CdnFileManager
{
    protected function saveImageToCDN($file, $folder, $filename)
    {
        return $this->putFileToCDNAndGetPath((string)$file, $folder, $filename);
    }

    private function putFileToCDNAndGetPath($file, $folder, $filename, $access_level = "public")
    {
        $filename = clean($filename, '_', ['.', '-']);
        $filename = $folder . $filename;
        $cdn = $this->getCDN();
        if ($access_level == "private") {
            $cdn->put($filename, $file);
        } else {
            $cdn->put($filename, $file, 'public');
        }
        return config('s3.url') . $filename;
    }

    private function getCDN()
    {
        return Storage::disk('s3');
    }

    protected function saveFileToCDN($file, $folder, $filename)
    {
        return $this->putFileToCDNAndGetPath(file_get_contents($file), $folder, $filename);
    }

    protected function savePrivateFileToCDN($file, $folder, $filename)
    {
        return $this->putFileToCDNAndGetPath(file_get_contents($file), $folder, $filename, 'private');
    }

    protected function deleteImageFromCDN($filename)
    {
        $this->deleteFileFromCDN($filename);
    }

    protected function
    deleteFileFromCDN($filename)
    {
        $filename = 'images/categories_images/thumbs/1616047868_phpe7ldol_collection_thumb.png';
        $this->getCDN()->delete($filename);
    }
}
