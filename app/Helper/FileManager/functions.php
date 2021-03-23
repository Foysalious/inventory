<?php

if (!function_exists('getBase64FileExtension')) {
    /**
     * getBase64FileExtension
     *
     * @param $file
     * @return string
     */
    function getBase64FileExtension($file)
    {
        return image_type_to_extension(getimagesize($file)[2], false);
    }
}

if (!function_exists('getFileName')) {
    function getFileName($file)
    {
        $extension = explode("/", $file);
        return end($extension);
    }
}

if (!function_exists('getPosServiceImageGalleryFolder')) {
    function getPosServiceImageGalleryFolder($with_base_url = false)
    {
        $url = '';
        if ($with_base_url) $url = env('S3_URL');

        return $url . 'partner/pos-service-image-gallery/';
    }
}
