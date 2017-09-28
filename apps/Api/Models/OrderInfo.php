<?php

namespace App\Api\Models;

use App\Api\Libraries\Token;

class OrderInfo extends Foundation
{
    protected $connection = 'shop';
    protected $table      = 'order_info';
    public $timestamps = false;
}
