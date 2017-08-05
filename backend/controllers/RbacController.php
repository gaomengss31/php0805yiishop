<?php

namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class RbacController extends \yii\web\Controller
{
    //权限的增删改查
    //权限添加
    public function actionPermissionAdd(){
        //添加表单
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD;//这个场景的定义，是专门针对ADD界面确认的
        //接收并验证数据(先加载requset提交过来的数据和验证这些数据)
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //验证完成后，开始准备添加权限首先创建对象
            $authManager = \Yii::$app->authManager;
            //1、添加
            $permission = $authManager->createPermission($model->name);
            $permission->description = $model->description;
            //2、保存
            $authManager->add($permission);
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect('permission-index');
        }
        return $this->render('permission-add',['model'=>$model]);
    }

    //权限列表
    public function actionPermissionIndex(){
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getPermissions();//获取全部权限

        return $this->render('permission-index',['models'=>$models]);
    }
    //权限修改
    public function actionPermissionEdit($name)
    {
        //检查权限是否存在
        $authManage = \Yii::$app->authManager;
        $permission = $authManage->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('权限不存在');//如果权限不存在
        }
        //显示修改表单
        $model = new PermissionForm();
        //判断提交方式，用于区别是否回显
        if(\Yii::$app->request->isPost){
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                //赋值，将表单的数据赋值给权限
                $permission->name = $model->name;
                $permission->description =$model->description;
                //更新权限中,先传name进去，第二个参数是权限，所以，传$permission
                $authManage->update($name,$permission);
                \Yii::$app->session->setFlash('success','权限修改成功');
                return $this->redirect('permission-index');
            }
        }else{
            //这个else是回显权限数据用的。
            $model->name = $permission->name;
            $model->description = $permission->description;
        }
        return $this->render('permission-add',['model'=>$model]);
    }

    //权限删除
    public function actionPermissionDel($name)
    {
        $authManage = \Yii::$app->authManager;
        $permission = $authManage->getPermission($name);
        $authManage->remove($permission);
        return $this->redirect('permission-index');
    }

    ////////////////////////////////////角色的增删改查\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
                            //首先建立一个新的表单模型
    public function actionRoleAdd(){
        $model = new RoleForm();//实例化一个对象
        if($model->load(\Yii::$app->request->post()) && $model->validate()){//验证数据
            //创建和保存权限
            $authManager = \Yii::$app->authManager;
            $role = $authManager->createRole($model->name);
            $role->description = $model->description;
            $authManager->add($role);
            //给角色赋予权限
            //特别注意，如果一个都没选的情况下，返回的是对象，这就造成无法保存了，因为他传过来的得是数组，所以加个判断
            if(is_array($model->permissions)){
                        foreach ($model->permissions as $permissionName){
                            $permission =$authManager->getPermission($permissionName);
                            if($permission){
                                $authManager->addChild($role,$permission);
                            }
                        }
                        \Yii::$app->session->setFlash('success','角色添加成功');
                return $this->redirect(['role-index']);
            }
        }
        return $this->render('role-add',['model'=>$model]);
    }

    //修改role
    public function actionRoleEdit($name)
    {
            $authManager = \Yii::$app->authManager;
            //获取角色 回显
            $model1 = new RoleForm();
            $permission = $authManager->getPermissionsByRole($name);
            $model1->permissions = ArrayHelper::map($permission, 'name', 'name');
            $role = $authManager->getRole($name);
            $model1->name = $role->name;
            $model1->description = $role->description;


            $model = new RoleForm();
            if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
//            删除角色权限关联
                $authManager->removeChildren($role);
                $model->name = $role->name;
                $model->description = $role->description;

                //        判断赋予权限
                if (is_array($model->permissions)) {
                    foreach ($model->permissions as $permissionName) {
                        $permission = $authManager->getPermission($permissionName);
                        if ($permission) {
                            $authManager->addChild($role, $permission);
                        }
                    }
                    \Yii::$app->session->setFlash('success', '修改成功');
                    return $this->redirect(['role-index']);
                }


            }
            return $this->render('role-add', ['model' => $model1]);

    }

    //显示角色index
    public function actionRoleIndex(){
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getRoles();//获取全部权限

        return $this->render('role-index',['models'=>$models]);
    }
}
