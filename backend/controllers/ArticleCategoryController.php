<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Request;

class ArticleCategoryController extends \yii\web\Controller
{
    public function actionAdd(){
        $model = new ArticleCategory();
        $request = new Request();
        //判断传值方式
        if($request->isPost){
            //如果是post方式提交，就开始加载数据
            $model->load($request->post());
            if($model->validate()){
               $model->save(false);//由于默认情况下，保存操作是会调用validate方法，有验证码的时候，需要关闭验证，所以用false
                    //跳转
                    return $this->redirect(['article-category/index']);
                }else{
                    //验证失败 打印错误信息
                    var_dump($model->getErrors());exit;
                }
            }
        return $this->render('add',['model'=>$model]);
    }
    public function actionIndex()
    {
        $query = ArticleCategory::find()->where(['status' => [1,0]])->orderBy('sort DESC');
        $total = $query->count();
        $parPage = 2;
        $pager = new Pagination(
            [
                'totalCount'=>$total,
                'defaultPageSize'=>$parPage
            ]
        );
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        //where(['status' => [1,0]])->orderBy('sort DESC');
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    public function actionEdit($id){
        //$model = new ArticleCategory();
        $model = ArticleCategory::findOne(['id'=>$id]);
        $request = new Request();
        //判断传值方式
        if($request->isPost){
            //如果是post方式提交，就开始加载数据
            $model->load($request->post());
            if($model->validate()){
                $model->save(false);//由于默认情况下，保存操作是会调用validate方法，有验证码的时候，需要关闭验证，所以用false
                //跳转
                return $this->redirect(['article-category/index']);
            }else{
                //验证失败 打印错误信息
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        $model = ArticleCategory::findOne($id);
        $model->status = -1;
        $model->save();
        return $this->redirect('index');
    }
}
