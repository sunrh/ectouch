<?php

namespace App\Api\Controllers;

use yii\web\Controller;

/**
 * Default controller for the `api` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->asJson(['error' => 0, 'message' => 'ectouch api servers.']);
    }
}
