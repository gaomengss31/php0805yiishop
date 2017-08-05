<?php
$form = \yii\bootstrap\ActiveForm::begin();
//echo 111;
echo $form->field($model,'name');

echo $form->field($model,'parent_id')->dropDownList(
    \backend\models\Menu::getMenuOptions(),[['prompt'=>'=请选择=']]

);
//dropDownList(\backend\models\Article::getCategoryOptions(),['prompt'=>'=请选择分类=']);
echo $form->field($model,'url')->dropDownList(
    \yii\helpers\ArrayHelper::map(Yii::$app->authManager->
    getPermissions(),'name','description'),['prompt'=>'=请选择路由=']
);
//->dropDownList(\backend\models\Article::getCategoryOptions(),['prompt'=>'=请选择分类='])
//\yii\helpers\ArrayHelper::map(Yii::$app->authManager

echo $form->field($model,'sort');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();