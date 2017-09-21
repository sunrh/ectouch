<?php

namespace App\Api\Models\V2;

class AreaRegion extends Foundation
{
    protected $connection = 'shop';

    protected $table      = 'area_region';

    public $timestamps = false;

    protected $visible = [];
}
