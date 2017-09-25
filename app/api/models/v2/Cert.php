<?php

namespace app\api\models\v2;

class Cert extends Foundation
{
    protected $connection = 'shop';
    protected $table      = 'cert';
    public $timestamps = true;
}
