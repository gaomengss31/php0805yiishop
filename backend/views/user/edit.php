<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'old_pass');
echo $form->field($model,'new_pass')->passwordInput();
echo $form->field($model,'password2')->passwordInput();
echo \yii\bootstrap\Html::submitButton('修改密码',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();