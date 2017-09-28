<?php

namespace App\Api\Controllers\V2;

use App\Api\Models\Configs;

class ConfigController extends BaseController
{
    public function actionIndex()
    {
        $data = Configs::getList();
        return $this->json($data);
    }
}
