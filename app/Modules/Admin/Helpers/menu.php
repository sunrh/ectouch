<?php

// 控制台
$modules['00_menu_dashboard']['01_dashboard_welcome'] = 'index.php?act=main';
//$modules['00_menu_dashboard']['02_favorite'] = 'index.php?act=main';
//$modules['00_menu_dashboard']['03_notepad'] = 'index.php?act=main';
//$modules['00_menu_dashboard']['04_calc'] = 'index.php?act=calculator';


$modules['01_system']['01_shop_config'] = 'shop_config.php?act=list_edit';
//$modules['01_system']['shop_authorized'] = 'license.php?act=list_edit';
$modules['01_system']['02_payment_list'] = 'payment.php?act=list';
$modules['01_system']['03_shipping_list'] = 'shipping.php?act=list';
$modules['01_system']['04_mail_settings'] = 'shop_config.php?act=mail_settings';
$modules['01_system']['05_area_list'] = 'area_manage.php?act=list';
//$modules['01_system']['06_plugins'] = 'plugins.php?act=list';
//$modules['01_system']['07_cron_schcron'] = 'cron.php?act=list';
$modules['01_system']['08_friendlink_list'] = 'friend_link.php?act=list';
//$modules['01_system']['sitemap'] = 'sitemap.php';
//$modules['01_system']['check_file_priv'] = 'check_file_priv.php?act=check';
$modules['01_system']['captcha_manage'] = 'captcha_manage.php?act=main';
$modules['01_system']['ucenter_setup'] = 'integrate.php?act=setup&code=ucenter';
$modules['01_system']['flashplay'] = 'flashplay.php?act=list';
$modules['01_system']['navigator'] = 'navigator.php?act=list';
//$modules['01_system']['file_check'] = 'filecheck.php';
//$modules['01_system']['fckfile_manage'] = 'fckfile_manage.php?act=list';
$modules['01_system']['021_reg_fields'] = 'reg_fields.php?act=list';


$modules['02_cat_and_goods']['01_goods_list'] = 'goods.php?act=list';         // 商品列表
$modules['02_cat_and_goods']['02_goods_add'] = 'goods.php?act=add';          // 添加商品
$modules['02_cat_and_goods']['03_category_list'] = 'category.php?act=list';
$modules['02_cat_and_goods']['05_comment_manage'] = 'comment_manage.php?act=list';
$modules['02_cat_and_goods']['06_goods_brand_list'] = 'brand.php?act=list';
$modules['02_cat_and_goods']['08_goods_type'] = 'goods_type.php?act=manage';
$modules['02_cat_and_goods']['11_goods_trash'] = 'goods.php?act=trash';        // 商品回收站
//$modules['02_cat_and_goods']['12_batch_pic'] = 'picture_batch.php';
//$modules['02_cat_and_goods']['13_batch_add'] = 'goods_batch.php?act=add';    // 商品批量上传
//$modules['02_cat_and_goods']['14_goods_export'] = 'goods_export.php?act=goods_export';
//$modules['02_cat_and_goods']['15_batch_edit'] = 'goods_batch.php?act=select'; // 商品批量修改
//$modules['02_cat_and_goods']['16_goods_script'] = 'gen_goods_script.php?act=setup';
$modules['02_cat_and_goods']['17_tag_manage'] = 'tag_manage.php?act=list';
$modules['02_cat_and_goods']['50_virtual_card_list'] = 'goods.php?act=list&extension_code=virtual_card';
$modules['02_cat_and_goods']['51_virtual_card_add'] = 'goods.php?act=add&extension_code=virtual_card';
$modules['02_cat_and_goods']['52_virtual_card_change'] = 'virtual_card.php?act=change';
//$modules['02_cat_and_goods']['goods_auto'] = 'goods_auto.php?act=list';


$modules['03_order']['02_order_list'] = 'order.php?act=list';
$modules['03_order']['03_order_query'] = 'order.php?act=order_query';
$modules['03_order']['04_merge_order'] = 'order.php?act=merge';
$modules['03_order']['05_edit_order_print'] = 'order.php?act=templates';
$modules['03_order']['06_undispose_booking'] = 'goods_booking.php?act=list_all';
//$modules['03_order']['07_repay_application']        = 'repay.php?act=list_all';
$modules['03_order']['08_add_order'] = 'order.php?act=add';
$modules['03_order']['09_delivery_order'] = 'order.php?act=delivery_list';
$modules['03_order']['10_back_order'] = 'order.php?act=back_list';


$modules['04_members']['03_users_list'] = 'users.php?act=list';
$modules['04_members']['04_users_add'] = 'users.php?act=add';
$modules['04_members']['05_user_rank_list'] = 'user_rank.php?act=list';
//$modules['04_members']['06_list_integrate'] = 'integrate.php?act=list';
$modules['04_members']['08_unreply_msg'] = 'user_msg.php?act=list_all';
$modules['04_members']['09_user_account'] = 'user_account.php?act=list';
$modules['04_members']['10_user_account_manage'] = 'user_account_manage.php?act=list';


$modules['05_promotion']['02_snatch_list'] = 'snatch.php?act=list';
$modules['05_promotion']['04_bonustype_list'] = 'bonus.php?act=list';
$modules['05_promotion']['06_pack_list'] = 'pack.php?act=list';
$modules['05_promotion']['07_card_list'] = 'card.php?act=list';
$modules['05_promotion']['08_group_buy'] = 'group_buy.php?act=list';
$modules['05_promotion']['09_topic'] = 'topic.php?act=list';
$modules['05_promotion']['10_auction'] = 'auction.php?act=list';
$modules['05_promotion']['12_favourable'] = 'favourable.php?act=list';
$modules['05_promotion']['13_wholesale'] = 'wholesale.php?act=list';
$modules['05_promotion']['14_package_list'] = 'package.php?act=list';
//$modules['05_promotion']['ebao_commend']            = 'ebao_commend.php?act=list';
$modules['05_promotion']['15_exchange_goods'] = 'exchange_goods.php?act=list';
$modules['05_promotion']['16_crowd_funding'] = 'crowd_funding.php?act=list';
$modules['05_promotion']['17_group_booking'] = 'group_booking.php?act=list';


$modules['06_stats']['flow_stats'] = 'flow_stats.php?act=view';
$modules['06_stats']['searchengine_stats'] = 'searchengine_stats.php?act=view';
$modules['06_stats']['z_clicks_stats'] = 'adsense.php?act=list';
$modules['06_stats']['report_guest'] = 'guest_stats.php?act=list';
$modules['06_stats']['report_order'] = 'order_stats.php?act=list';
$modules['06_stats']['report_sell'] = 'sale_general.php?act=list';
$modules['06_stats']['sale_list'] = 'sale_list.php?act=list';
$modules['06_stats']['sell_stats'] = 'sale_order.php?act=goods_num';
$modules['06_stats']['report_users'] = 'users_order.php?act=order_num';
$modules['06_stats']['visit_buy_per'] = 'visit_sold.php?act=list';


$modules['07_content']['03_article_list'] = 'article.php?act=list';
$modules['07_content']['02_articlecat_list'] = 'articlecat.php?act=list';
//$modules['07_content']['vote_list'] = 'vote.php?act=list';
//$modules['07_content']['article_auto'] = 'article_auto.php?act=list';
//$modules['07_content']['shop_help'] = 'shophelp.php?act=list_cat';
//$modules['07_content']['shop_info'] = 'shopinfo.php?act=list';
$modules['07_content']['ad_position'] = 'ad_position.php?act=list';
$modules['07_content']['ad_list'] = 'ads.php?act=list';


$modules['08_rec']['affiliate'] = 'affiliate.php?act=list';
$modules['08_rec']['affiliate_ck'] = 'affiliate_ck.php?act=list';
$modules['08_rec']['01_drp_config']                      = 'drp.php?act=config';
$modules['08_rec']['02_drp_audit']                       = 'drp.php?act=users_audit';
$modules['08_rec']['02_drp_users']                       = 'drp.php?act=users';
$modules['08_rec']['03_drp']                             = 'drp.php?act=list';
$modules['08_rec']['04_order_list']                      = 'drp.php?act=order_list';
$modules['08_rec']['07_ranking']                         = 'drp.php?act=ranking';
$modules['08_rec']['08_drp_log']                         = 'drp.php?act=drp_log';


$modules['09_priv_admin']['admin_logs'] = 'admin_logs.php?act=list';
$modules['09_priv_admin']['admin_list'] = 'privilege.php?act=list';
$modules['09_priv_admin']['admin_role'] = 'role.php?act=list';
//$modules['09_priv_admin']['agency_list'] = 'agency.php?act=list';
//$modules['09_priv_admin']['suppliers_list'] = 'suppliers.php?act=list'; // 供货商


$modules['10_template']['02_template_select'] = 'template.php?act=list';
//$modules['10_template']['03_template_setup'] = 'template.php?act=setup';
//$modules['10_template']['04_template_library'] = 'template.php?act=library';
//$modules['10_template']['05_edit_languages'] = 'edit_languages.php?act=list';
//$modules['10_template']['06_template_backup'] = 'template.php?act=backup_setting';
$modules['10_template']['mail_template_manage'] = 'mail_template.php?act=list';


$modules['11_backup']['02_db_manage'] = 'database.php?act=backup';
$modules['11_backup']['03_db_optimize'] = 'database.php?act=optimize';
$modules['11_backup']['04_sql_query'] = 'sql.php?act=main';
//$modules['11_backup']['05_synchronous']             = 'integrate.php?act=sync';
//$modules['11_backup']['convert'] = 'convert.php?act=main';


//$modules['12_others']['02_sms_my_info']                = 'sms.php?act=display_my_info';
//$modules['12_others']['03_sms_send'] = 'sms.php?act=display_send_ui';
//$modules['12_others']['04_sms_sign'] = 'sms.php?act=sms_sign';
//$modules['12_others']['04_sms_charge']                 = 'sms.php?act=display_charge_ui';
//$modules['12_others']['05_sms_send_history']           = 'sms.php?act=display_send_history_ui';
//$modules['12_others']['06_sms_charge_history']         = 'sms.php?act=display_charge_history_ui';
//$modules['12_others']['email_list'] = 'email_list.php?act=list';
//$modules['12_others']['magazine_list'] = 'magazine_list.php?act=list';
//$modules['12_others']['attention_list'] = 'attention_list.php?act=list';
//$modules['12_others']['view_sendlist'] = 'view_sendlist.php?act=list';


$modules['13_wechat']['01_wechat_config'] = 'wechat/index';
$modules['13_wechat']['02_wechat_masssend'] = 'wechat/mass_message';
$modules['13_wechat']['03_wechat_autoreply'] = 'wechat/reply_subscribe';
$modules['13_wechat']['04_wechat_selfmenu'] = 'wechat/menu_list';
$modules['13_wechat']['05_wechat_tmplmsg'] = 'wechat/template_massage_list';
$modules['13_wechat']['06_wechat_contactmanage'] = 'wechat/subscribe_list';
$modules['13_wechat']['07_wechat_appmsg'] = 'wechat/article';
$modules['13_wechat']['08_wechat_qrcode'] = 'wechat/share_list';
$modules['13_wechat']['09_wechat_extends'] = 'Extend/index';
// $modules['13_wechat']['10_wechat_remind'] = '../index.php?m=admin&c=wechat&a=remind';
$modules['13_wechat']['11_wechat_customer'] = 'wechat/customer_service';


$GLOBALS['modules'] = $modules;
