<?php

namespace App\Api\Models\V2;

use App\Api\Libraries\Token;

class OrderInfo extends Foundation
{
    protected $connection = 'shop';
    protected $table      = 'order_info';
    public $timestamps = false;
}
