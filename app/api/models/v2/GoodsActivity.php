<?php

namespace app\api\models\v2;

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
