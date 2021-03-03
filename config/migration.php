<?php

return [
    'starting_ids' => [
        'categories' => (int) env('CATEGORIES_STARTING_ID', 10001),
        'category_partner' => (int) env('CATEGORY_PARTNER_STARTING_ID', 100001),
        'products' => (int) env('PRODUCTS_STARTING_ID', 1000001),
        'product_update_logs' => (int) env('PRODUCT_UPDATE_LOGS_STARTING_ID', 500001),
        'product_images' => (int) env('PRODUCT_IMAGES_STARTING_ID', 1000001),
        'partner_pos_settings' => (int) env('PARTNER_POS_SETTINGS_STARTING_ID', 500001),
        'discounts' => (int) env('DISCOUNTS_STARTING_ID', 100001)
    ]
];
