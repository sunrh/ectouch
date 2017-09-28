<?php

namespace App\Api\Controllers\V2;

use App\Api\Models\Article;
use App\Api\Models\ArticleCategory;
use Yii;

class ArticleController extends BaseController
{

    /**
     * POST ecapi.article.list ($id, $page, $page_size)
     */
    public function actionIndex()
    {
        $rules = [
            [['id', 'page', 'per_page'], 'required'],
            ['id', 'integer'],
            ['page', 'integer', 'min' => 1],
            ['per_page', 'integer', 'min' => 1]
        ];

        if ($error = $this->validateInput($rules)) {
            return $error;
        }

        $model = ArticleCategory::getList($this->validated);

        return $this->json($model);
    }

    /**
     * GET ecapi.article.show
     */
    public function actionShow()
    {
        $id = Yii::$app->request->post('id');
        $model = Article::getArticle($id);
        return $this->json($model);
    }
}
