<?php

namespace frontend\controllers;


use frontend\models\Goods;
use frontend\models\GoodsCategory;
use yii\web\Cookie;


class GoodsController extends \yii\web\Controller
{
    public $layout = false;

    //首页
    public function actionIndex()
    {
        $models = GoodsCategory::find()->where(['parent_id'=>0])->all();
        return $this->render('index',['models'=>$models]);
    }

    //商品详情
    public function actionGoods($id){
        $models = Goods::findOne(['id'=>$id]);
        return $this->render('goods',['models'=>$models]);
    }

    //商品列表页面
    public function actionList($goods_category_id){
        $models = Goods::find()->where(['goods_category_id'=>$goods_category_id])->all();
        return $this->render('list',['models'=>$models]);
    }

    //
    //商品列表页---点击parent_id为0的情况下，显示该分类下面所有的商品列表
    //这个是递归的弄出来的，，，模型那还有一段代码
    /*public function actionList($id){

        $ids = GoodsCategory::getChildIds($id);
        $ids[] = $id;
        $models=Goods::find()->where(['goods_category_id'=>$ids])->all();
        //echo $models->createCommand()->sql;exit;
        return $this->render('list',['models'=>$models]);
    }*/


}
