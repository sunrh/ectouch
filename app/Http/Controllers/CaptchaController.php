<?php

namespace App\Http\Controllers;

use App\Libraries\Captcha;

define('INIT_NO_SMARTY', true);

/**
 * 生成验证码
 * Class CaptchaController
 * @package App\Http\Controllers
 */
class CaptchaController extends Controller
{
    public function actionIndex()
    {
        $img = new Captcha(public_path('data/captcha/'), $GLOBALS['_CFG']['captcha_width'], $GLOBALS['_CFG']['captcha_height']);
        @ob_end_clean(); //清除之前出现的多余输入
        if (isset($_REQUEST['is_login'])) {
            $img->session_word = 'captcha_login';
        }
        $img->generate_image();
    }
}
