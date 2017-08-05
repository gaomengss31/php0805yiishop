<?php

namespace backend\controllers;

use backend\models\GoodsCategory;

use yii\db\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class GoodsCategoryController extends \yii\web\Controller
{
    public function actionAdd()
    {
        $model = new GoodsCategory();
        //接收并验证数据
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            //判断是否增加一级分类
            if ($model->parent_id) {//判断是否是一级分类
                $category = GoodsCategory::findOne(['id' => $model->parent_id]);
                if ($category) {//如果$category存在，就接着往下走开始保存
                    $model->prependTo($category);
                } else {//如果不存在，要报错
                    throw new HttpException(404, '上级分类不存在');
                }


            } else {//是一级分类,因为上面已经有new和加载值了，所以这个地方直接make就行了
                $model->makeRoot();
            }
            //新增一个提示信息
            \Yii::$app->session->setFlash('success', '这里添加成功啦，卧槽~~');
            return $this->redirect('index');
        }
        return $this->render('add', ['model' => $model]);

    }
////////////////////////////////首页显示\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionIndex()
    {
        $models = GoodsCategory::find()->all();
        return $this->render('index', ['models' => $models]);
    }
////////////////////////////////这特么就是个测试\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionTest()
    {
        //创建一个根节点
        /*$category = new GoodsCategory();
        //为根节点命名
        $category ->name = '家用电器';
        //将创建的节点保存到数据库
        $category->makeRoot();*/


        //创建子节点
        //$russia = new Menu(['name' => 'Russia']);
        //$russia->prependTo($countries);

        /*$category1 = new GoodsCategory();
        $category1->name = '小家电';
        $category = GoodsCategory::findOne(['id'=>1]);
        $category1->parent_id =$category->id;
        $category1->prependTo($category);*/

        //$cate = GoodsCategory::findOne(['id'=>4])->delete();
        //echo '操作成功';
    }

    public function actionZtree()
    {
        //不加载布局文件
        return $this->renderPartial('ztree');
    }

    ////////////////////////////////添加商品分类\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionAdd2()
    {
        $model = new GoodsCategory(['parent_id' => 0]);
        //接收并验证数据
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {

            //判断是否增加一级分类
            if ($model->parent_id) {//判断是否是一级分类

                $category = GoodsCategory::findOne(['id' => $model->parent_id]);

                if ($category) {//如果$category存在，就接着往下走开始保存
                    $model->prependTo($category);
                } else {//如果不存在，要报错
                    throw new HttpException(404, '上级分类不存在');
                }


            } else {//是一级分类,因为上面已经有new和加载值了，所以这个地方直接make就行了
                $model->makeRoot();
            }
            //新增一个提示信息
            \Yii::$app->session->setFlash('success', '这里添加成功啦，卧槽~~');

            return $this->redirect(['index']);
        }
        $categories = GoodsCategory::find()->select(['id', 'parent_id', 'name'])->asArray()->all();
        return $this->render('add2', ['model' => $model, 'categories' => $categories]);

    }

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>删除分类<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    //$cate = GoodsCategory::findOne(['id'=>4])->delete();
    public function actionDelete($id)
    {
        //判断删除的$id行，是否还有子类，如果有无法删除，如果没有就可以执行删除

        $cate1 = GoodsCategory::findOne(['id'=>$id]);
        if($cate1->id=$cate1->parent_id){//当要删行的id =parent_id的时候，证明有子类，不能删
            throw new HttpException(404,'不能删除');
        }else{//判断无子类,就删
            $cate = GoodsCategory::findOne(['id' => $id])->delete();
        }
        return $this->redirect('index');
    }
    //////////////////////////修改\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionEdit($id){
        //$model = new GoodsCategory(['parent_id' => 0]);

        $model = GoodsCategory::findOne(['id'=>$id]);
        //找出那一行以后，先判断是否存在
        if($model==null){
            throw new NotFoundHttpException('分类不存在');
        }
        //接收并验证数据
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {

            try{
                if ($model->parent_id) {//判断是否是一级分类
                    $category = GoodsCategory::findOne(['id' => $model->parent_id]);
                    if ($category) {//如果$category存在，就接着往下走开始保存
                        $model->prependTo($category);
                    } else {//如果不存在，抛出异常
                        throw new HttpException(404, '上级分类不存在');
                    }
                } else {//是一级分类,因为上面已经有new和加载值了，所以这个地方直接make就行了
                    if($model->oldAttributes['parent_id']==0){
                        $model->save();
                    }else{
                        $model->makeRoot();
                    }

                }
                //新增一个提示信息
                \Yii::$app->session->setFlash('success', '这里添加成功啦，卧槽~~');
                return $this->redirect(['index']);
            }catch (Exception $e){
                $model->addError('parent_id',GoodsCategory::exceptionInfo($e->getMessage()));
            }

        }
        $categories = GoodsCategory::find()->select(['id', 'parent_id', 'name'])->asArray()->all();
        return $this->render('add2', ['model' => $model, 'categories' => $categories]);
    }
}
