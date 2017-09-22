<?php

Route::namespace('App\Modules\Installer\Controllers')->prefix('install')->group(function () {
    Route::get('/', 'IndexController@index');
});
