<?php namespace App\Services\Product;

use App\Helper\ConstGetter;

class UpdateNature
{
    use ConstGetter;

    const OPTIONS_CHANGED = 'options_changed';
    const VALUE_ADD_DELETE  = 'value_add_delete';
    const VALUE_ADD = 'value_add'; //only_value_added
    const VALUE_DELETE = 'value_delete';  //only value deleted

}
