<?php

namespace App\Api\Controllers\V2;

use App\Api\Models\Invoice;

class InvoiceController extends BaseController
{

    /**
    * POST ecapi.invoice.type.list
    */
    public function actionType()
    {
        $data = Invoice::getTypeList();
        return $this->json($data);
    }

    /**
    * POST ecapi.invoice.content.list
    */
    public function actionContent()
    {
        $data = Invoice::getContentList();
        return $this->json($data);
    }

    /**
    * POST ecapi.invoice.status.get
    */
    public function actionStatus()
    {
        $data = Invoice::getStatus();
        return $this->json($data);
    }
}
