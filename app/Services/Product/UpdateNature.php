<?php namespace App\Services\Product;

use App\Helper\ConstGetter;

class UpdateNature
{
    use ConstGetter;

    const NON_VARIANT = 'non_variant';
    const VARIANTS_ADD = 'variants_add';
    const VARIANTS_DISCARD = 'variants_discard';
    const OPTIONS_UPDATED = 'options_updated';
    const VALUES_UPDATED  = 'values_updated';
    const VALUE_ADD = 'value_add';
    const VALUE_DELETE = 'value_delete';

}
