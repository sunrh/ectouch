<?php

namespace App\Api\Models\V2;

class Cert extends Foundation
{
    protected $connection = 'shop';
    protected $table      = 'cert';
    public $timestamps = true;
}
