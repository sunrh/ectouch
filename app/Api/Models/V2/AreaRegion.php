<?php

namespace app\api\models\v2;

class AreaRegion extends Foundation
{
    protected $connection = 'shop';

    protected $table      = 'area_region';

    public $timestamps = false;

    protected $visible = [];
}
