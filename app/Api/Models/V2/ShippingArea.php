<?php

namespace App\Api\Models\V2;

class ShippingArea extends Foundation
{
    protected $connection = 'shop';

    protected $table      = 'shipping_area';

    public $timestamps = false;
}
