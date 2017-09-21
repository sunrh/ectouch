<?php

namespace App\Api\Controllers\V2;

use App\Api\Models\V2\Keywords;

class SearchController extends BaseController
{
    //POST  ecapi.search.keyword.list
    public function actionIndex()
    {
        return $this->json(Keywords::getHot());
    }
}
