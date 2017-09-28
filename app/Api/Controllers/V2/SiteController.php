<?php

namespace App\Api\Controllers\V2;

use App\Api\Models\Ad;
use App\Api\Models\Goods;

class SiteController extends BaseController
{
    //POST  ecapi.site.get
    public function actionIndex()
    {
        $rules = [
            [['page', 'per_page'], 'required'],
            [['page', 'per_page'], 'integer', 'min'=>1]
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }
        $id = 265;
        $goodsList = Goods::getBestGoodsList($this->validated);
        $banner = Ad::getBanner($id);
        return $this->json(['banner'=>$banner, 'goodsList'=>$goodsList]);
    }
}
