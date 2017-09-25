<?php

namespace app\api\requests;

use yii\base\Model;

/**
 * ArticlesRequest is the model behind the contact form.
 */
class Articles extends Model
{

    public $name;
    public $email;
    public $subject;
    public $body;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }

}
