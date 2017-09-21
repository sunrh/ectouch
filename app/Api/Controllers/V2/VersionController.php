<?php

namespace App\Api\Controllers\V2;

use App\Api\Models\V2\Version;

class VersionController extends BaseController
{
    /**
     * POST ecapi.version.check
     */
    public function actionCheck()
    {
        $data = Version::checkVersion();
        return $this->json($data);
    }
}
