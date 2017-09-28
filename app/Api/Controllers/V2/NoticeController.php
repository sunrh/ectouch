<?php

namespace App\Api\Controllers\V2;

use App\Api\Models\Card;
use App\Api\Models\Notice;
use Yii;

class NoticeController extends BaseController
{

    /**
     * POST ecapi.notice.list
     */
    public function actionIndex()
    {
        $rules = [
            // 'page' => 'required|integer|min:1',
            // 'per_page' => 'required|integer|min:1',

            [['page', 'per_page'], 'required'],
            [['page', 'per_page'], 'integer', 'min' => 1],
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $model = Notice::getList($this->validated);

        return $this->json($model);
    }

    /**
     * POST ecapi.notice.show
     */
    public function actionShow()
    {
        $res = Notice::getNotice(Yii::$app->request->post('id'));
        $this->json($res);
    }
}
