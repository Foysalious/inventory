<?php

$helper_files = [
    "app/Sheba/Helpers/Migration/functions.php",
    "app/Sheba/Helpers/String/functions.php",
];

foreach ($helper_files as $file) {
    $file = dirname(dirname(__DIR__)) . "/" . $file;
    if (file_exists($file))
        require $file;
}
