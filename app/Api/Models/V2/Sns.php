<?php

namespace App\Api\Models\V2;

class Sns extends Foundation
{
    protected $connection = 'shop';
    protected $table      = 'sns';
    protected $primaryKey = 'user_id';
    public $timestamps = true;
}
