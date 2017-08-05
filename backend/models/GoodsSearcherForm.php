<?php
namespace backend\models;

use yii\base\Model;

class GoodsSearcherForm extends Model{
    //定义出需要显示的数据框
    public $name;
    //public $brand_id;
    public $maxPrice;
    public $minPrice;

    public function rules()
    {
        return [
            [['name','maxPrice','$minPrice'],'safe']
        ];
    }
}

