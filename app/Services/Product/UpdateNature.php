<?php namespace App\Services\Product;

use App\Helper\ConstGetter;

class UpdateNature
{
    use ConstGetter;

    const OPTIONS_CHANGED = 'options_changed';
    const VALUE_ADD  = 'value_add';
    const VALUE_DELETE = 'value_delete';

}
