<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Rebuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $app_path = app_path();

        // 更新 Libraries 文件
        $list = glob($app_path . '/libraries/*');
        foreach ($list as $vo) {
            $name = str_replace('cls_', '', basename($vo, '.php'));
            rename($vo, dirname($vo) . '/' . camel_case($name, true) . '.php');
        }

        // 更新 Helpers 文件
        $frontend = glob($app_path . '/helpers/*');
        $backend = glob($app_path . '/modules/*/helpers/*');
        $list = array_merge($frontend, $backend);
        foreach ($list as $vo) {
            $name = str_replace('lib_', '', basename($vo, '.php'));
            rename($vo, dirname($vo) . '/' . $name . '.php');
        }

        // 更新 Controller 文件
        $frontend = glob($app_path . '/http/controllers/*');
        $backend = glob($app_path . '/modules/*/controllers/*');
        $list = array_merge($frontend, $backend);
        foreach ($list as $vo) {
            $name = basename($vo, '.php');
            if ($name != 'Controller') {
                rename($vo, dirname($vo) . '/' . camel_case($name, true) . 'Controller.php');
            }
        }
    }

    public function actionMigration()
    {
        $database_path = database_path();

        $list = glob($database_path . '/migrations/*');

        foreach ($list as $vo) {
            $content = file_get_contents($vo);
            $content = str_replace('ecs_', '', $content);
            file_put_contents($vo, $content);

            $name = str_replace('ecs_', '', basename($vo, '.php'));
            rename($vo, dirname($vo) . '/' . $name . '.php');
        }
    }
}
