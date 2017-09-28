<?php

namespace App\Api\Models;

class Cert extends Foundation
{
    protected $connection = 'shop';
    protected $table      = 'cert';
    public $timestamps = true;
}
