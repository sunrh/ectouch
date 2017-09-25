<?php

namespace app\api\models\v2;

use app\api\libraries\Token;

class OrderInfo extends Foundation
{
    protected $connection = 'shop';
    protected $table      = 'order_info';
    public $timestamps = false;
}
