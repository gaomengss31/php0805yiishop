<?php
namespace backend\models;

use yii\base\Model;

class PermissionForm extends Model{
    //由于是form模型,需要定义字段名称

    public $name;//权限名称
    public $description; //权限描述

    //定义一个场景
    const SCENARIO_ADD ='add';


    //定义规则：
    public function rules()
    {
        return [
            [['name','description'], 'required'],
            //权限名称不能重复,由于需要去验证数据库，但是目前这个form 不支持，所以，写一个验证规则
            ['name','validateName','on'=>self::SCENARIO_ADD],

        ];
    }

    //定义表单上，显示成中文
    public function attributeLabels()
    {
        return [
            'name'=>'权限名称',
            'description'=>'权限描述'
        ];
    }

    public function validateName(){
        $authManage = \Yii::$app->authManager;
        //获取权限名称
        if($authManage->getPermission($this->name)){
            //如果这个权限存在的情况，就返回错误
            $this->addError('name','权限已经存在，无法添加');
            //$this->addError('name','权限已存在');
        }
    }
}