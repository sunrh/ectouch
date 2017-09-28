<?php

namespace App\Api\Controllers\V2;

use App\Api\Models\AreaCode;

class AreaCodeController extends BaseController
{
    /**
     * POST ecapi.areacode.list
     */
    public function actionIndex()
    {
        $model = AreaCode::getList();

        return $this->json($model);
    }
}
