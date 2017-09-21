ECTouch TODO
============================

Alifuwu
Bargain
Chat
Checkin
Coupon
Crowdfunding
Discover
Drp
Eggfrenzy
Oauth
Paying
Pintuan
Roulette
Scratchcard
Sharkitoff
Virtualcode
Wechat


模板路由替换
------

将

> (['|"])(\w+?)\.php\?act=(.*?)(['|"])

替换

> $1index.php?c=$2&a=$3$4

手动搜索

> act=

查看是否替换完整，注意文件名称以 ./ 开头 的格式。

使用相同的规则替换 js 目录下的文件


(['|"])(\w+?)\.php(['|"]), (['|"])(.*?)(['|"])(.*?)POST

$1index.php?c=$2$3, $4$5$6$7POST


(['|"])(\w+?)\.php(['|"]), (['|"])act=(.*?)(['|"])(.*?)GET

$1index.php?c=$2$3, $4a=$5$6$7GET


flow.php?step=

index.php?c=flow&a=



(['|"])(\w+?)\.php(['|"])

$1index.php?c=$2$3



while \(\$(.*?)=(.*?)\(\$(.*?)\)

foreach (\$$3 as \$$1




index.php\?c=(.*?)&a=(.*?)(["|'|&])

index.php?r=$1/$2$3

