<?php

namespace backend\models;

use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
    //获取文章详情表的相关数据
    public function getGoodsIntro(){
        return $this->hasOne(GoodsIntro::className(),['goods_id'=>'id']);
    }

    //获取商品相册的一对多关系
    public function getGalleries()
    {
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }
    //获取图片轮播数据
    public function getPics()
    {
        $images = [];
        foreach ($this->galleries as $img){
            $images[] = Html::img($img->path);
        }
        return $images;
    }

    //获取文章分类表的一些数据
    public function getBrand(){
        return $this->hasOne(Brand::className(),['id'=>'brand_id']);
    }
    public $imgFile;

    public static $status_options = [
        0=>'回收',
        1=>'正常'
    ];

    public static $status_istop = [
        0=>'下架',
        1=>'在售'
    ];


    /**
     * @inheritdoc
     */
    public static function getBrandOptions()
    {
        return ArrayHelper::map(Brand::find()->asArray()->all(),'id','name');
    }

    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time', 'view_times'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['sn'], 'string', 'max' => 20],
            [['logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'LOGO图片',
            'goods_category_id' => '商品分类id',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '是否在售(1在售 0下架)',
            'status' => '状态(1正常 0回收站)',
            'sort' => '排序',
            'create_time' => '添加时间',
            'view_times' => '浏览次数',
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new GoodsCategoryQuery(get_called_class());
    }

    public static function getZtreeNodes()
    {
        $nodes =  GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        $nodes[] = ['id'=>0,'parent_id'=>0,'name'=>'顶级分类','open'=>1];
        return $nodes;
    }

    //异常提示信息
    public static function exceptionInfo($msg)
    {
        $infos = [
            'Can not move a node when the target node is same.'=>'不能修改到自己节点下面',
            'Can not move a node when the target node is child.'=>'不能修改到自己的子孙节点下面',
        ];
        return isset($infos[$msg])?$infos[$msg]:$msg;
    }
}