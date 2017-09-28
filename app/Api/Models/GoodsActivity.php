<?php

namespace App\Api\Models;

use DB;

class GoodsActivity extends Foundation
{
    protected $connection = 'shop';

    protected $table      = 'goods_activity';

    public $timestamps = false;

    protected $visible = ['promo', 'name'];

    protected $appends = ['promo', 'name'];

    protected $guarded = [];
}
