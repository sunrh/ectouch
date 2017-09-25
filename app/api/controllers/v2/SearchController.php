<?php

namespace app\api\controllers\v2;

use app\api\models\v2\Keywords;

class SearchController extends BaseController
{
    //POST  ecapi.search.keyword.list
    public function actionIndex()
    {
        return $this->json(Keywords::getHot());
    }
}
