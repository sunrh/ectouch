<?php

return [
    // 应用运行模式
    'RUN_MODE' => 0,

    // 商城URL
    'SHOP_URL' => 'http://localhost/',

    // 微信小程序
    'WX_MINI_APPID' => 'wx',
    'WX_MINI_SECRET' => 'wx',

    // 注册协议地址
    'TERMS_URL' => 'http://localhost/article.php?cat_id=-1',
    'ABOUT_URL' => 'http://localhost/article.php?cat_id=-2',

    // Token授权加密key
    'TOKEN_SECRET' => '12345678901234567890123456789000',
    'TOKEN_ALG' => 'HS256',
    'TOKEN_TTL' => '43200',
    'TOKEN_REFRESH' => false,
    'TOKEN_REFRESH_TTL' => '1440',
    'TOKEN_VER' => '1.0.0',

    // 短信验证信息模版
    'SMS_TEMPLATE' => '#CODE#，短信验证码有效期30分钟，请尽快进行验证。'
];
