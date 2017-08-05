<?php

namespace backend\controllers;

use backend\models\Menu;


class MenuController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $models= Menu::find()->where(['parent_id'=>0])->all();

        return $this->render('index',['models'=>$models]);
    }
    //获取子菜单


    //增加
    public  function actionAdd(){
        $model = new Menu();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){

            $model->save();
            return $this->redirect('index');
        };
        return $this->render('add',['model'=>$model]);
    }

    public function actionEdit($id){
        $model = Menu::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){

            $model->save();
            return $this->redirect('index');
        };
        return $this->render('add',['model'=>$model]);
    }

    public function actionDelete($id){
        $model = Menu::findOne(['id'=>$id]);
        $del =Menu::findOne($id)->delete();
        return  $this->redirect('index');
    }
}
