<?php

return [
    'starting_ids' => [
        'categories' => (int) env('CATEGORIES_STARTING_ID', 10001),
        'products' => (int) env('PRODUCTS_STARTING_ID', 1000001)
    ]
];
