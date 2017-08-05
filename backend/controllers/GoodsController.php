<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use backend\models\GoodsSearcherForm;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use yii\data\Pagination;
use yii\web\Request;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use backend\models\GoodsGallery;

class GoodsController extends \yii\web\Controller
{
    //////////////////////////////////////展示商品表单\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionIndex()
    {
        $model  = new GoodsSearcherForm();
        $query = Goods::find();
        $model->load(\Yii::$app->request->get());
        /*var_dump($model->maxPrice);
        exit;*/
        if($model->name){
            $query->andWhere(['like','name',$model->name]);
        }
        if($model->maxPrice){
            $query->andWhere(['<=','market_price',$model->maxPrice]);
        }
        if($model->minPrice){
            $query->andWhere(['>=','market_price',$model->minPrice]);
        }

        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'pageSize'=>5
        ]);

        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager,'model'=>$model]);
    }
    //查看单个商品
    public function actionView($id){
        $models = Goods::findOne(['id'=>$id]);
        $models2 = GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('view',['models'=>$models,'models2'=>$models2]);
    }

    /////////////////////////添加商品表单\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionAdd()
    {
        $model = new Goods();
        $model2 = new GoodsIntro();
        $request = new Request();
        //判断传值方式
        if ($request->isPost) {
            //如果是post方式提交，就开始加载数据
            $model->load($request->post());
            $model2->load($request->post());
            if ($model->validate() && $model2->validate()) {
                $model->create_time = time();
                $model->view_times= 1;

                //确定先找到一个day
                $day = date('Y-m-d');
                $goodsConut = GoodsDayCount::findOne(['day'=>$day]);
                //如果goodCount返回为空，则直接在count字段中保存一个0
                if($goodsConut==null){
                    //准备保存，首先实例化对象
                    $goodsConut = new GoodsDayCount();
                    //拿到各个字段的值，准备保存
                    $goodsConut->day = $day;
                    $goodsConut->count = 0;
                    //确定值以后，开始保存
                    $goodsConut->save();
                }
                //开始拼接sn字段的值
                $model->sn = date('Ymd').substr('000'.($goodsConut->count+1),-4,4);
                //开始保存model
                $model->save();
                //当$goodsConut有值的时候，准备保存这个

                $model2->goods_id = $model->id;
                $model2->save();
                \Yii::$app->session->setFlash('success','商品添加成功,请添加商品相册');
                return $this->redirect(['goods/gallery','id'=>$model->id]);
            } else {
                //验证失败 打印错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model, 'model2' => $model2]);
    }
    //////////////////////////修改\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionEdit($id){
        //$model = new Goods();
        //$model2 = new GoodsIntro();
        $model = Goods::findOne(['id'=>$id]);
        $model2 = GoodsIntro::findOne(['goods_id'=>$id]);
        $request = new Request();
        //判断传值方式
        if ($request->isPost) {
            //如果是post方式提交，就开始加载数据
            $model->load($request->post());
            $model2->load($request->post());
            if ($model->validate() && $model2->validate()) {
                $model->create_time = time();
                $model->view_times= 1;
                $model->save(false);



                /*echo "<pre/>";
                var_dump($model);
                exit;*/
                //由于默认情况下，保存操作是会调用validate方法，有验证码的时候，需要关闭验证，所以用false
                //跳转

                $model2->goods_id = $model->id;

                $model2->save();

                return $this->redirect(['goods/index']);
            } else {
                //验证失败 打印错误信息
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model, 'model2' => $model2]);
    }

    /////////////////////////////////删除\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionDelete($id){
        $model = Goods::findOne(['id'=>$id]);
        $model->status = 1;
        $model->save();
        return $this->redirect('index');
    }

    public function actions() {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ],
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,//如果文件已存在，是否覆盖
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },//文件的保存方式
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //$action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    $qiniu->uploadFile(
                        $action->getSavePath(), $action->getWebUrl()
                    );
                    $url = $qiniu->getLink($action->getWebUrl());
                    $action->output['fileUrl']  = $url;
                },
            ],
        ];
    }


    //测试七牛云文件上传
    public function actionQiniu()
    {

        $config = [
            'accessKey'=>'hqNnJqiC0r7xoCcroZKMbqgbmELaZPyYmrbnNIDg',
            'secretKey'=>'KwzsOiQ7UbAesjwXKh5fMblJCbbrOHuN6grCQxzq',
            'domain'=>'http://otbhsfl07.bkt.clouddn.com/',
            'bucket'=>'yiishop',
            'area'=>Qiniu::AREA_HUADONG
        ];



        $qiniu = new Qiniu($config);
        $key = 'upload/2e/79/2e795418fcb72341d801d1fa70ca6fabc33444cb.png';

        //将图片上传到七牛云
        $qiniu->uploadFile(
            \Yii::getAlias('@webroot').'/upload/2e/79/2e795418fcb72341d801d1fa70ca6fabc33444cb.png',
            $key);
        //获取该图片在七牛云的地址
        $url = $qiniu->getLink($key);
        var_dump($url);
    }
    //相册
    public function actionGallery($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new NotFoundHttpException('找不到这个商品');
        }
        return $this->render('gallery',['goods'=>$goods]);
    }
}


