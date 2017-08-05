<?php
namespace backend\models;
use yii\base\Model;

class RoleForm extends Model{
    //首先添加字段
    public $name;
    public $description;
    public $permissions=[];

    //定义一个场景
    const SCENARIO_ADD ='add';


    //定义规则：
    public function rules()
    {
        return [
            [['name','description'], 'required'],
            [['permissions'],'safe'],
            ['name','validateName','on'=>self::SCENARIO_ADD],

        ];
    }


    //定义表单上，显示成中文
    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'角色描述',
            'permission'=>'权限'
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