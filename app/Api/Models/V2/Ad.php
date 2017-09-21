<?php

namespace app\api\models\v2;

use Yii;

/**
 * This is the model class for table "ecs_ad".
 *
 * @property integer $ad_id
 * @property integer $position_id
 * @property integer $media_type
 * @property string $ad_name
 * @property string $ad_link
 * @property string $ad_code
 * @property integer $start_time
 * @property integer $end_time
 * @property string $link_man
 * @property string $link_email
 * @property string $link_phone
 * @property string $click_count
 * @property integer $enabled
 */
class Ad extends Foundation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ad}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position_id', 'media_type', 'start_time', 'end_time', 'click_count', 'enabled'], 'integer'],
            [['ad_code'], 'required'],
            [['ad_code'], 'string'],
            [['ad_name', 'link_man', 'link_email', 'link_phone'], 'string', 'max' => 60],
            [['ad_link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ad_id' => 'Ad ID',
            'position_id' => 'Position ID',
            'media_type' => 'Media Type',
            'ad_name' => 'Ad Name',
            'ad_link' => 'Ad Link',
            'ad_code' => 'Ad Code',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'link_man' => 'Link Man',
            'link_email' => 'Link Email',
            'link_phone' => 'Link Phone',
            'click_count' => 'Click Count',
            'enabled' => 'Enabled',
        ];
    }

    /**
     * 获取轮播图
     */
    public static function getBanner($id)
    {
        $time = time();
        $num = 3;
        $res = Ad::find()
            ->select('ad_id, position_id, media_type, ad_link, ad_code, ad_name, RAND() AS rnd ')
            ->with('position')
            ->where("enabled = 1 AND start_time <= '" . $time . "' AND end_time >= '" . $time . "' ")
            ->andWhere("position_id = '" . $id . "' ")
            ->orderBy('rnd')
            ->limit($num)
            ->asArray()
            ->all();

        $res = array_map(
            function ($v) {
                if (!empty($v['position'])) {
                    $temp = array_merge($v, $v['position']);
                    unset($temp['position']);
                    return $temp;
                }
            },
            $res
        );

        $ads = array();
        $position_style = '';
        $link = '';
        foreach ($res as $row) {
            if ($row['position_id'] != $id) {
                continue;
            }
            if ($row['ad_link']) {
                //广告位链接类型1.商品详情页 2.分类列表页
                if (strlen(strstr($row['ad_link'], 'goods_id')) > 0) {
                    $link = '../goods/goods?objectId=';
                } elseif (strlen(strstr($row['ad_link'], 'category_id')) > 0) {
                    $link = '../product_list/product_list?id=';
                }
                $lenth = strpos($row['ad_link'], '=');
                $new_id = substr($row['ad_link'], $lenth+1);
            }
            switch ($row['media_type']) {
                case 0: // 图片广告
                    $src = (strpos($row['ad_code'], 'http://') === false && strpos($row['ad_code'], 'https://') === false) ?
                        "data/attached/afficheimg/$row[ad_code]" : $row['ad_code'];
                    $ads[] = array(
                        'pic'=> Yii::$app->params['SHOP_URL'].'/'.$src,
                        'link'=> $link.$new_id
                        );

                    break;
                case 1: // Flash
                   
                    break;
                case 2: // CODE
                    break;
                case 3: // TEXT
                    break;
            }
        }
 
    
        return $ads;
    }

    public function getPosition()
    {
        return $this->hasOne(AdPosition::className(), ['position_id' => 'position_id']);
    }
}
