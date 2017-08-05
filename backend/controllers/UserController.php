<?php

namespace backend\controllers;
use backend\models\Admin;
use backend\models\LoginForm2;
use backend\models\Menu;
use backend\models\User;
use yii\web\NotFoundHttpException;
use yii\web\Request;


class UserController extends \yii\web\Controller
{
    public function actionLogin()
{
    //1检查用户的账号和密码是否正确
    $model = new LoginForm2();
    $request = new Request();
    if($request->isPost){
        $model->load($request->post());
        if($model->validate() && $model->login()){
            //登录成功
            \Yii::$app->session->setFlash('success','登录成功');
            return $this->redirect(['user/index']);
        }
    }

    return $this->render('login',['model'=>$model]);


}
    public function actionIndex()
    {
        $models = User::find()->all();
        return $this->render('index',['models'=>$models]);
    }

    //测试登录状态
    public function actionUser()
    {
        //可以通过 Yii::$app->user 获得一个 User实例，
        //$user = \Yii::$app->user;

        // 当前用户的身份实例。未登录用户则为 Null 。
        //$identity = \Yii::$app->user->identity;
       // var_dump($identity);

        // 当前用户的ID。 未登录用户则为 Null 。
        //$id = \Yii::$app->user->id;
        //var_dump($id);
        // 判断当前用户是否是游客（未登录的）
        $isGuest = \Yii::$app->user->isGuest;
        var_dump($isGuest);
    }

    //添加用户
    public function actionAdd()
    {
        $model = new User();
        $model->scenario=User::SCENARIO_ADD;
        $model->load(\Yii::$app->request->post());
        //var_dump(\Yii::$app->request->post());
        //var_dump($model->password);
        //exit;
        //表单的密码没有传过来
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
                $model->password_hash =\Yii::$app->security->generatePasswordHash($model->password);
                $model->status=10;
                $model->created_at = time();
                //将auth_key，由于要保存到数据库，所以随机生成一个字段，加这个目的是为了自动登录
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->save();

                //开始添加用户角色
                $authManager = \Yii::$app->authManager;
                //$authManager->revokeAll($model->id);
                if(is_array($model->roles)){
                    foreach ($model->roles as $roleName){
                        $role =$authManager->getRole($roleName);
                        if($role){
                            if($role) $authManager->assign($role,$model->id);
                        }
                    }
                    \Yii::$app->session->setFlash('success','用户角色添加成功');
                    return $this->redirect(['index']);
                }
                return $this->redirect(['user/index']);


        }else{
            //var_dump($model->getErrors());EXIT;
        }
        return $this->render('add',['model'=>$model]);
    }

    //注销
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }
    //修改
    public function actionEdit($id){
        //$model = new User();
        $model = User::findOne(['id'=>$id]);
        $request = new Request();

        if($request->isPost){
            $model->load($request->post());
            //var_dump($model);
            //exit;
            if($model->validate()){
                $model->password_hash =\Yii::$app->security->generatePasswordHash($model->password);
                $model->status=10;
                $model->created_at = time();
                //将auth_key，由于要保存到数据库，所以随机生成一个字段，加这个目的是为了自动登录
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->save(false);
                return $this->redirect(['user/index']);
            }
        }else{
            //var_dump($model->getErrors());EXIT;
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionChPw(){
        //表单字段，旧密码，新密码， 确认新密码
        //验证规则： 都不能为空，  验证旧密码是否正确， 新密码不能和旧密码一样， 确认新密码一样
        //表单验证通过，更新新密码

        //验证是否登录了
        $model = new User(['scenario'=>User::SCENARIO_EDIT]);
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());

            //验证数据成功
            if($model->validate()){
                //开始验证，旧密码是否正确。
                $id = \Yii::$app->user->id;
                $admin = User::findOne(['id'=>$id]);
                if($admin){//验证与旧密码是否相同
                    if(\Yii::$app->security->validatePassword($model->old_pass,$admin->password_hash)){
                        //判断新旧密码不能相同
                        if(!\Yii::$app->security->validatePassword($model->new_pass,$admin->password_hash)){
                                //判断新密码不能相同
                                if($model->new_pass == $model->password2){

                                    if($admin){
                                        $admin->password_hash = \Yii::$app->security->generatePasswordHash($model->new_pass);
                                        $admin->save();
                                    }
                                }else{
                                    \Yii::$app->session->setFlash('success','两次新密码不相同');
                                    return $this->redirect(['user/ch-pw']);
                                }
                        }else{
                            \Yii::$app->session->setFlash('success','新旧密码相同');
                            return $this->redirect(['user/ch-pw']);
                        }
                    }else{
                        \Yii::$app->session->setFlash('success','与旧密码不匹配');
                        return $this->redirect(['user/ch-pw']);
                    }
                }
                //$model->save(false);
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());EXIT;
            }
        }else{
            //var_dump($model->getErrors());EXIT;
        }

        return $this->render('edit',['model'=>$model]);
    }

    ////////////////////////////删除\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function actionDelete($id){

        $model = User::findOne(['id'=>$id]);

        $del = User::findOne($id)->delete();
        return  $this->redirect('index');
    }

}
