<?php

namespace app\api\controllers\v2;

use app\api\models\v2\Configs;

class ConfigController extends BaseController
{
    public function actionIndex()
    {
        $data = Configs::getList();
        return $this->json($data);
    }
}
