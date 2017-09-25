<?php

namespace app\api\controllers\v2;

use app\api\models\v2\Banner;

class BannerController extends BaseController
{

    /**
     * POST ecapi.banner.list
     */
    public function actionIndex()
    {
        $model = Banner::getList();

        return $this->json($model);
    }
}
