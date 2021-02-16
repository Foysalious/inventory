<?php

if( !function_exists('getCollectionDefaultThumbFolder')) {

    /**
     * Get Collection default Thumb folder Path.
     *
     * @param bool $with_base_url
     * @return string
     */

    function getCollectionDefaultThumbFolder( $with_base_url = false ) : string {

        $url = '';
        if ($with_base_url) $url = env('S3_URL');

        return $url . 'images/categories_images/thumbs/';

    }
}


if( !function_exists('getCollectionDefaultThumb')) {

    /**
     * Get Collection default Thumb file name.
     *
     * @return string
     */
    function getCollectionDefaultThumb() : string {

        return getCollectionDefaultThumbFolder( true ) . 'default.jpg';

    }
}


if( !function_exists('getCollectionDefaultAppThumbFolder')) {

    /**
     * Get Collection default App Thumb folder Path.
     *
     * @param bool $with_base_url
     * @return string
     */

    function getCollectionDefaultAppThumbFolder( $with_base_url = false ) : string {

        $url = '';
        if ($with_base_url) $url = env('S3_URL');

        return $url . 'images/categories_images/app_thumbs/';

    }
}


if( !function_exists('getCollectionDefaultAppThumb')) {

    /**
     * Get Collection default App Thumb file name.
     *
     * @return string
     */
    function getCollectionDefaultAppThumb() : string {

        return getCollectionDefaultAppThumbFolder( true ) . 'default.jpg';

    }
}


if( !function_exists('getCollectionDefaultBannerFolder')) {

    /**
     * Get Collection default Banner folder Path.
     *
     * @param bool $with_base_url
     * @return string
     */

    function getCollectionDefaultBannerFolder( $with_base_url = false ) : string {

        $url = '';
        if ($with_base_url) $url = env('S3_URL');

        return $url . 'images/categories_images/banner/';

    }
}


if( !function_exists('getCollectionDefaultBanner')) {

    /**
     * Get Collection default Banner file name.
     *
     * @return string
     */
    function getCollectionDefaultBanner() : string {

        return getCollectionDefaultBannerFolder( true ) . 'default.jpg';

    }
}


if( !function_exists('getCollectionDefaultAppBannerFolder')) {

    /**
     * Get Collection default App Banner folder Path.
     *
     * @param bool $with_base_url
     * @return string
     */

    function getCollectionDefaultAppBannerFolder( $with_base_url = false ) : string {

        $url = '';
        if ($with_base_url) $url = env('S3_URL');

        return $url . 'images/categories_images/banner/';

    }
}


if( !function_exists('getCollectionDefaultAppBanner')) {

    /**
     * Get Collection default App Banner file name.
     *
     * @return string
     */
    function getCollectionDefaultAppBanner() : string {

        return getCollectionDefaultAppBannerFolder( true ) . 'default.jpg';

    }
}
