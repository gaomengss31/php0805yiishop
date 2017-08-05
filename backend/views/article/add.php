<?php
$form = \yii\bootstrap\ActiveForm::begin();
//echo 111;
echo $form->field($model,'name');
echo $form->field($model,'intro');
echo $form->field($model,'article_category_id')->dropDownList(\backend\models\Article::getCategoryOptions(),['prompt'=>'=请选择分类=']);
echo $form->field($model,'sort');
echo $form->field($model,'status',['inline'=>'ture'])->radioList(\backend\models\Brand::getStatusOption());
echo $form->field($model2,'content')->widget('kucha\ueditor\UEditor',[

    'clientOptions' => [
        //编辑区域大小
        'initialFrameHeight' => '150',
        ]
]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();