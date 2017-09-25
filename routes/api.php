<?php

return [
    'v2' => [
        // Other
        'ecapi.article.show' => 'article/show',

        'ecapi.notice.show' => 'notice/show',

        'ecapi.order.notify.<code:\S+>' => 'order/notify',

        'ecapi.product.intro.<id:\d+>' => 'goods/intro',

        'ecapi.product.share.<id:\d+>' => 'goods/share',

        'ecapi.auth.web' => 'user/web-oauth',

        'ecapi.auth.web.callback/<vendor:\d+>' => 'user/web-callback',

        // Guest
        'ecapi.access.dns' => 'access/dns',

        'ecapi.access.batch' => 'access/batch',

        'ecapi.category.list' => 'goods/category',

        'ecapi.category.all.list' => 'goods/all-category',

        'ecapi.product.list' => 'goods/index',

        'ecapi.search.product.list' => 'goods/search',

        'ecapi.review.product.list' => 'goods/review',

        'ecapi.review.product.subtotal' => 'goods/subtotal',

        'ecapi.recommend.product.list' => 'goods/recommend-list',

        'ecapi.product.accessory.list' => 'goods/accessory-list',

        'ecapi.product.get' => 'goods/info',

        'ecapi.auth.weixin.mplogin' => 'user/weixin-mini-program-login',

        'ecapi.auth.signin' => 'user/signin',

        'ecapi.auth.social' => 'user/auth',

        'ecapi.auth.default.signup' => 'user/signup-by-email',

        'ecapi.auth.mobile.signup' => 'user/signup-by-mobile',

        'ecapi.user.profile.fields' => 'user/fields',

        'ecapi.auth.mobile.verify' => 'user/verify-mobile',

        'ecapi.auth.mobile.send' => 'user/send-code',

        'ecapi.auth.mobile.reset' => 'user/reset-password-by-mobile',

        'ecapi.auth.default.reset' => 'user/reset-password-by-email',

        'ecapi.cardpage.get' => 'card-page/view',

        'ecapi.cardpage.preview' => 'card-page/preview',

        'ecapi.config.get' => 'config/index',

        'ecapi.article.list' => 'article/index',

        'ecapi.brand.list' => 'brand/index',

        'ecapi.search.keyword.list' => 'search/index',

        'ecapi.region.list' => 'region/index',

        'ecapi.invoice.type.list' => 'invoice/type',

        'ecapi.invoice.content.list' => 'invoice/content',

        'ecapi.invoice.status.get' => 'invoice/status',

        'ecapi.notice.list' => 'notice/index',

        'ecapi.banner.list' => 'banner/index',

        'ecapi.version.check' => 'version/check',

        'ecapi.recommend.brand.list' => 'brand/recommend',

        'ecapi.message.system.list' => 'message/system',

        'ecapi.message.count' => 'message/unread',

        'ecapi.site.get' => 'site/index',

        'ecapi.splash.list' => 'splash/index',

        'ecapi.splash.preview' => 'splash/view',

        'ecapi.theme.list' => 'theme/index',

        'ecapi.theme.preview' => 'theme/view',

        'ecapi.search.category.list' => 'goods/category-search',

        'ecapi.order.reason.list' => 'order/reason-list',

        'ecapi.search.shop.list' => 'shop/search',

        'ecapi.recommend.shop.list' => 'shop/recommand',

        'ecapi.shop.list' => 'shop/index',

        'ecapi.shop.get' => 'shop/info',

        'ecapi.areacode.list' => 'area-code/index',

        // Authorization
        'ecapi.user.profile.get' => 'user/profile',

        'ecapi.user.profile.update' => 'user/update-profile',

        'ecapi.user.password.update' => 'user/update-password',

        'ecapi.order.list' => 'order/index',

        'ecapi.order.get' => 'order/view',

        'ecapi.order.confirm' => 'order/confirm',

        'ecapi.order.cancel' => 'order/cancel',

        'ecapi.order.price' => 'order/price',

        'ecapi.product.like' => 'goods/set-like',

        'ecapi.product.unlike' => 'goods/set-unlike',

        'ecapi.product.liked.list' => 'goods/liked-list',

        'ecapi.order.review' => 'order/review',

        'ecapi.order.subtotal' => 'order/subtotal',

        'ecapi.payment.types.list' => 'order/payment-list',

        'ecapi.payment.pay' => 'order/pay',

        'ecapi.shipping.vendor.list' => 'shipping/index',

        'ecapi.shipping.status.get' => 'shipping/info',

        'ecapi.shipping.select.shipping' => 'cart/select-shipping',

        'ecapi.consignee.list' => 'consignee/index',

        'ecapi.consignee.update' => 'consignee/modify',

        'ecapi.consignee.add' => 'consignee/add',

        'ecapi.consignee.delete' => 'consignee/remove',

        'ecapi.consignee.setDefault' => 'consignee/set-default',

        'ecapi.score.get' => 'score/view',

        'ecapi.score.history.list' => 'score/history',

        'ecapi.cashgift.list' => 'cash-gift/index',

        'ecapi.cashgift.available' => 'cash-gift/available',

        'ecapi.push.update' => 'message/update-deviceId',

        'ecapi.cart.add' => 'cart/add',

        'ecapi.cart.clear' => 'cart/clear',

        'ecapi.cart.delete' => 'cart/delete',

        'ecapi.cart.get' => 'cart/index',

        'ecapi.cart.update' => 'cart/update',

        'ecapi.cart.checkout' => 'cart/checkout',

        'ecapi.cart.promos' => 'cart/promos',

        'ecapi.product.purchase' => 'goods/purchase',

        'ecapi.product.validate' => 'goods/check-product',

        'ecapi.message.order.list' => 'message/order',

        'ecapi.shop.watch' => 'shop/watch',

        'ecapi.shop.unwatch' => 'shop/unwatch',

        'ecapi.shop.watching.list' => 'shop/watching-list',

        'ecapi.coupon.list' => 'coupon/index',

        'ecapi.coupon.available' => 'coupon/available',

        'ecapi.cart.flow' => 'cart/flow',

        'ecapi.goods.property.total' => 'goods/property-total',

    ],
];
