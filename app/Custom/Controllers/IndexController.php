<?php

namespace App\Custom\Controllers;

use App\Http\Controllers\IndexController as BaseController;

class IndexController extends BaseController
{
    public function actionIndex()
    {
        return 'Hello Developer.';
    }
}
