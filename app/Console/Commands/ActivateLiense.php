<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ActivateLiense extends Command
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
        $server = $this->argument('server');
        $code = $this->argument('code');

        $res = curl_request("{$server}/v1/licenses/activate", 'POST', ['code' => $code]);

        if (isset($res['success']) && $res['success'] == false) {
            echo '授权失败';
            exit;
        }

        if ($model = License::first()) {
            $model->delete();
        }

        License::create($res['data']);

        $this->call('cache:clear');

        echo "授权成功\n\n";
    }
}
