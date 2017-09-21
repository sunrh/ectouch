<?php

namespace App\Console\Commands;

use App\Api\Models\V2\License;
use yii\console\Controller;

class ActivateLicenseController extends Controller
{

    protected $signature = 'activate:license {server} {code}';

    protected $description = 'Activate License By Code';

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