<?php

$helper_files = [
    "app/Sheba/Helpers/Migration/functions.php",
];

foreach ($helper_files as $file) {
    $file = dirname(dirname(__DIR__)) . "/" . $file;
    if (file_exists($file))
        require $file;
}
