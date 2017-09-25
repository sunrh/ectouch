<?php

namespace app\custom\controllers;

use app\http\controllers\IndexController as BaseController;

class IndexController extends BaseController
{
    public function actionIndex()
    {
        return 'Hello Developer.';
    }
}
