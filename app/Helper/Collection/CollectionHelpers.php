<?php

if (!function_exists('getBase64FileExtension')) {
    /**
     * getBase64FileExtension
     *
     * @param $file
     * @return string
     */
    function getBase64FileExtension($file): string
    {
        return image_type_to_extension(getimagesize($file)[2], false);
    }
}

if( !function_exists('getCollectionDefaultThumbFolder')) {

    /**
     * Get Collection default Thumb folder Path.
     *
     * @param bool $with_base_url
     * @return string
     */
    function getCollectionDefaultThumbFolder() : string {
        return config('filesystems.disks.s3.default_image_path').'/images/collections/thumbs/';
    }
}


if( !function_exists('getCollectionDefaultThumb')) {

    /**
     * Get Collection default Thumb file name.
     *
     * @return string
     */
    function getCollectionDefaultThumb() : string {

        return getCollectionDefaultThumbFolder() . 'default.jpg';

    }
}


if( !function_exists('getCollectionDefaultAppThumbFolder')) {

    /**
     * Get Collection default App Thumb folder Path.
     *
     * @param bool $with_base_url
     * @return string
     */

    function getCollectionDefaultAppThumbFolder() : string {
        return config('filesystems.disks.s3.default_image_path').'/images/collections/app_thumbs/';
    }
}


if( !function_exists('getCollectionDefaultAppThumb')) {

    /**
     * Get Collection default App Thumb file name.
     *
     * @return string
     */
    function getCollectionDefaultAppThumb() : string {

        return getCollectionDefaultAppThumbFolder() . 'default.jpg';

    }
}


if( !function_exists('getCollectionDefaultBannerFolder')) {

    /**
     * Get Collection default Banner folder Path.
     *
     * @param bool $with_base_url
     * @return string
     */

    function getCollectionDefaultBannerFolder() : string {
        return config('filesystems.disks.s3.default_image_path').'/images/collections/banner/';
    }
}


if( !function_exists('getCollectionDefaultBanner')) {

    /**
     * Get Collection default Banner file name.
     *
     * @return string
     */
    function getCollectionDefaultBanner() : string {

        return getCollectionDefaultBannerFolder() . 'default.jpg';

    }
}


if( !function_exists('getCollectionDefaultAppBannerFolder')) {

    /**
     * Get Collection default App Banner folder Path.
     *
     * @param bool $with_base_url
     * @return string
     */

    function getCollectionDefaultAppBannerFolder() : string {
        return config('filesystems.disks.s3.default_image_path').'/images/collections/banner/';
    }
}


if( !function_exists('getCollectionDefaultAppBanner')) {

    /**
     * Get Collection default App Banner file name.
     *
     * @return string
     */
    function getCollectionDefaultAppBanner() : string {

        return getCollectionDefaultAppBannerFolder() . 'default.jpg';

    }
}
