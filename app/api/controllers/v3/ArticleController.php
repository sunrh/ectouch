<?php

namespace app\api\controllers\v3;

use app\api\requests\Articles;
use app\services\ArticleService;
use yii\base\Module;
use yii\web\Controller;

/**
 * Article controller for the `api` module
 */
class ArticleController extends Controller
{
    public $enableCsrfValidation = false;

    private $article;

    public function __construct($id, Module $module, array $config = [], ArticleService $article)
    {
        $this->article = $article;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        $model = new Articles();

        // populate model attributes with user inputs
        $model->load(\Yii::$app->request->post());
        // which is equivalent to the following:
        // $model->attributes = \Yii::$app->request->post('ContactForm');

        if ($model->validate()) {
            // all inputs are valid
            return 'yes';
        } else {
            // validation failed: $errors is an array containing error messages
            $errors = $model->errors;
            return $this->asJson($errors);
        }


        $res = $this->article->all();
        return $this->asJson(['error' => 0, 'data' => $res]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDetail($id)
    {
        $res = $this->article->detail($id);
        return $this->asJson(['error' => 0, 'data' => $res]);
    }
}
