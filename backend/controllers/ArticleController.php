<?php

namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\web\Request;

class ArticleController extends \yii\web\Controller
{
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>分页列表<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionIndex($keywords='')
    {

        //这个查询语句，需要把and写在前面
        $query=Article::find()->where(['and','status in (1,0)',"name like '%{$keywords}%'"])->orderBy('sort desc,id desc');


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
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>文章增加<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionAdd(){
        $model = new Article();
        $model2= new ArticleDetail();
        $request = new Request();
        //判断传值方式
        if($request->isPost){
            //如果是post方式提交，就开始加载数据
            $model->load($request->post());
            $model2->load($request->post());
            if($model->validate() && $model2->validate()){
                $model->create_time=time();
                //由于默认情况下，保存操作是会调用validate方法，有验证码的时候，需要关闭验证，所以用false
                $model->save(false);
                //跳转
                $model2->article_id = $model->id;
                $model2->save();

                return $this->redirect(['article/index']);
            }else{
                //验证失败 打印错误信息
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'model2'=>$model2]);
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>删除<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionDelete($id){
        $model = Article::findOne($id);
        $model->status = -1;
        $model->save();
        return $this->redirect('index');
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>输入框插件<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>修改<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionEdit($id){

        $model = Article::findOne(['id'=>$id]);
        $model2 = ArticleDetail::findOne(['article_id'=>$id]);
        $request = new Request();
        //判断传值方式
        if($request->isPost){
            //如果是post方式提交，就开始加载数据
            $model->load($request->post());
            $model2->load($request->post());
            if($model->validate() && $model2->validate()){
                $model->create_time=time();
                //由于默认情况下，保存操作是会调用validate方法，有验证码的时候，需要关闭验证，所以用false
                $model->save();
                //跳转
                $model2->article_id = $model->id;
                $model2->save();

                return $this->redirect(['article/index']);
            }else{
                //验证失败 打印错误信息
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'model2'=>$model2]);
    }
}

//$query=Article::find()->where(['and','status in (1,0)',"name like '%{$keywords}%'"])->orderBy('sort desc,id desc');
