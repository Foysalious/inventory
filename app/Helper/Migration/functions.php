<?php

if (!function_exists('commonColumns')) {

    /**
     * Migration common columns.
     *
     * @param $table
     */
    function commonColumns($table)
    {
        $table->string('created_by_name')->nullable();
        $table->string('updated_by_name')->nullable();
        $table->timestamps();
    }
}
if (!function_exists('dropCommonColumns')) {

    /**
     * Migration common columns.
     *
     * @param $table
     */
    function dropCommonColumns($table)
    {
        $table->dropColumn('created_by_name');
        $table->dropColumn('updated_by_name');
        $table->dropColumn('created_at');
        $table->dropColumn('updated_at');
    }
}
if (!function_exists('storeColumns')) {

    /**
     * Migration store columns only.
     *
     * @param $table
     */
    function storeColumns($table)
    {
        $table->string('created_by_name')->nullable();
        $table->timestamp('created_at')->nullable();
    }
}


if (!function_exists('getCategoryDefaultThumb')) {

    /**
     * Get Category default Thumb file name.
     *
     * @return string
     */
    function getCategoryDefaultThumb()
    {
        return getCategoryThumbFolder() . 'default.jpg';
    }
}


if (!function_exists('getCategoryThumbFolder')) {

    /**
     * Get Category Thumb Folder.
     *
     * @return string
     */
    function getCategoryThumbFolder(): string
    {
        $url = config('s3.url');
        return $url . 'images/pos/categories/thumbs/';
    }
}

if (!function_exists('getCategoryDefaultBanner')) {

    /**
     * Get Category default Thumb file name.
     *
     * @return string
     */
    function getCategoryDefaultBanner()
    {
        return getCategoryBannerFolder() . 'default.jpg';
    }
}

if (!function_exists('getCategoryBannerFolder')) {

    /**
     * Get Category Thumb Folder.
     *
     * @return string
     */
    function getCategoryBannerFolder(): string
    {
        $url = config('s3.url');
        return $url . 'images/pos/categories/banners/';
    }
}
