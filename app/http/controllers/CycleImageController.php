<?php

namespace app\http\controllers;

define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

/**
 * 轮播图片程序
 * Class CycleImageController
 * @package app\http\controllers
 */
class CycleImageController extends Controller
{
    public function actionIndex()
    {
        header('Content-Type: application/xml; charset=' . CHARSET);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Thu, 27 Mar 1975 07:38:00 GMT');
        header('Last-Modified: ' . date('r'));
        header('Pragma: no-cache');

        $cycle_image = storage_path('app/public/' . DATA_DIR . '/cycle_image.xml');
        if (file_exists($cycle_image)) {
            echo file_get_contents($cycle_image);
        } else {
            echo '<?xml version="1.0" encoding="' . CHARSET . '"?><bcaster><item item_url="images/200609/05.jpg" link="http://www.ectouch.cn" /></bcaster>';
        }
    }
}
