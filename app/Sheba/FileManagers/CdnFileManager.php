<?php


namespace App\Sheba\FileManagers;


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
        return config('filesystems.disks.s3.default_image_path') . $filename;
    }

    private function getCDN()
    {
        return Storage::disk('s3');
    }
}
