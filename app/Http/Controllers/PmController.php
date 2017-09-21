<?php

namespace App\Http\Controllers;

/**
 * Class PmController
 * @package App\Http\Controllers
 */
class PmController extends Controller
{
    public function actionIndex()
    {
        if (empty(session('user_id')) || $GLOBALS['_CFG']['integrate_code'] == 'ecshop') {
            ecs_header('Location:./');
        }

        uc_call("uc_pm_location", array(session('user_id')));
    }
}
