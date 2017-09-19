ECTouch v2
============================

ECTouch 是上海商创网络科技有限公司推出的一款开源免费移动商城网店系统，旨在帮助企业和个人快速构建手机移动商城并减少二次开发带来的成本。

采用稳定的 HMVC 框架开发，执行效率、扩展性、稳定性值得信赖。MVC 是一种将应用程序的逻辑层和表现层进行分离的方法，分层有助于管理复杂的应用程序。

另外 ECTouch 也为商家提供的丰富 API，涵盖 ECTouch 各个核心业务流程，基于这些内容可开发各类应用，解决店铺管理、营销推广、数据分析等方面的问题，以实现WAP站点和客户端及单页应用等多种形式的应用接入。如果您是富有企业信息系统开发经验的传统软件厂商，您还可以基于 ECTouch API 为商家提供包括但不限于 BI、ERP、DRP、CRM、SCM  等。


目录结构
-------------------

      app/                应用核心目录
      bootstrap/          包含启动文件
      config/             包含配置文件
      database/           包含数据迁移
      public/             包含入口脚本和Web资源
      resources/          包含资源文件
      routes/             包含路由配置
      storage/            包含缓存存储
      tests/              包含各种类型的测试程序
      vendor/             包含第三方依赖包



依赖要求
------------

项目运行的最低配置要求您的Web服务器支持PHP 5.6.4。


安装
------------

### Install via Composer

如果您还没有安装 [Composer](http://getcomposer.org/)，您可以按照以下的说明安装它
[getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix)。

您可以使用下面的命令来安装 ECTouch 程序：

~~~
php composer global require "fxp/composer-asset-plugin:^1.3.1"
php composer create-project --prefer-dist --stability=dev ectouch/ectouch ectouch
~~~

现在，您应该能够通过下面的URL访问应用程序，假设 `ectouch` 目录直接在 Web 根目录下。

~~~
http://localhost/ectouch/public/
~~~


### Install from an Archive File

从 [ectouch.cn](http://www.ectouch.cn/download/) 下载压缩包并解压后，将解压后的目录命名为 ectouch 后放到 Web 根目录下。

设置 `config/config.php` 文件中的 validation key 参数，使得您项目的 Cookie 得到应有的保护。

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

您应该能够通过下面的URL访问应用程序:

~~~
http://localhost/ectouch/public/
~~~


配置
-------------

### Database

编辑数据库配置文件 `config/database.php`，示例：

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=ectouch',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```


贡献
-------------

感谢您对 ECTouch 项目的关注，我们非常愿意开发者参与贡献，希望积极 fork & PR ！


安全漏洞
-------------

如果你在 ECTouch 的网站上发现了安全漏洞，请给我们发一封电子邮件。所有的安全漏洞都将及时得到解决。


许可
-------------

ECTouch 遵循 GPL v3 开源协议。
