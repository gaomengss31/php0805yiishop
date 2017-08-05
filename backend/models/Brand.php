<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property string $logo
 * @property integer $sort
 * @property integer $status
 */
class Brand extends \yii\db\ActiveRecord
{
    public $imgFile;
    public static $status_options = [
        -1=>'删除',
        0=>'隐藏',
        1=>'正常'
    ];
    //传参数的目的是，add界面不需要显示删除项。
    public static function getStatusOption($shanchu=true){
        $options=[
            -1=>'删除',
            0=>'隐藏',
            1=>'正常',];
        if($shanchu){
                unset($options['-1']);
        }
        return $options;

    }
    public static function tableName()
    {
        return 'brand';
    }


    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '品牌',
            'intro' => '简介',
            'logo' => 'LOGO',
            'sort' => '排序',
            'status' => '状态',
        ];
    }

}
