<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
{
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>���brand<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionAdd(){
        $model = new Brand();
        $request = new Request();

        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                      $model->save();

                      return $this->redirect(['index']);
                  }else{
                      var_dump($model->getErrors());exit;
                  }
        }
        return $this->render('add',['model'=>$model]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionIndex()
    {

        $query = Brand::find()->where(['status' => [1,0]])->orderBy('sort DESC');
        //$query = Brand::find()->where(['status' => [1,0]])->orderBy('sort DESC')->all();

        $total = $query->count();
        $parPage = 2;
        $pager = new Pagination(
            [
                'totalCount'=>$total,
                'defaultPageSize'=>$parPage
            ]
        );
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);

    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionIndex2(){
        $query2 = Brand::find()->where(['status' => -1])->orderBy('sort DESC');
        //$query = Brand::find()->where(['status' => [1,0]])->orderBy('sort DESC')->all();

        $total = $query2->count();
        $parPage = 5;
        $pager = new Pagination(
            [
                'totalCount'=>$total,
                'defaultPageSize'=>$parPage
            ]
        );
        $models2 = $query2->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index2',['models2'=>$models2,'pager'=>$pager]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionEdit($id){
        //$model = new Brand();
        $model = Brand::findOne(['id'=>$id]);
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['index']);
            }else{

                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionDel($id){
        $model = Brand::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        return $this->redirect('index');
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actions() {
        return [
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
                'overwriteIfExist' => true,
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
                },
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
                    $action->output['fileUrl'] = $action->getWebUrl();
                    /*$action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"*/
                    //��ͼƬ�ϴ�����ţ����
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    $qiniu->uploadFile(
                        $action->getSavePath(),$action->getWebUrl()
                    );
                    $url = $qiniu->getLink($action->getWebUrl());
                    $action->output['fileUrl'] = $url;

                    },
            ],
        ];
    }
//测试七牛云，这个地方必须要有，验证key的地方
    public function actionQiniu(){

        $config = [
            'accessKey'=>'_0_0yq12zkEYI-SPX-PS8FGc7XqVD_sNxcwyVo3L',
            'secretKey'=>'49TsocJoSpsupLcThag5twhw9XKpy1Y73gZwYTnD',
            'domain'=>'http://otbw5uw0l.bkt.clouddn.com/',
            'bucket'=>'yiishop',
            'area'=>Qiniu::AREA_HUADONG
        ];
        $qiniu = new Qiniu($config);
        $key = 'upload/cc/8a/cc8af6aa8ea51370c22352d716f4c4b9ba6177e7.jpg';

        $qiniu->uploadFile(\Yii::getAlias('@webroot'.'/upload/cc/8a/cc8af6aa8ea51370c22352d716f4c4b9ba6177e7.jpg'),$key);
        $url = $qiniu->getLink($key);
        var_dump($url);
    }



}
