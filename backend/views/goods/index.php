<?php
$form = \yii\bootstrap\ActiveForm::begin(['layout' => 'inline','method'=>'get']);
echo $form->field($model,'name')->textInput(['placeholder'=>'标题']) ;
//echo $form->field($model,'$model->brand->name')->dropDownList(\backend\models\Goods::getBrandOptions(),['promppt'=>'请输入品牌']);
echo $form->field($model,'maxPrice')->textInput(['placeholder'=>'价格上限']);
echo $form->field($model,'minPrice')->textInput(['placeholder'=>'价格下限']);
echo \yii\bootstrap\Html::submitButton('搜索',['class'=>'btn btn-default btn-success']);
\yii\bootstrap\ActiveForm::end();
?>
<p>
    <?=\yii\bootstrap\Html::a('增加',['goods/add'],['class'=>'btn btn-default btn-success'])?>
</p>

<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>商品名称</th>
        <th>货号</th>
        <th>LOGO图片</th>
        <th>商品分类id</th>
        <th>库存</th>
        <th>添加时间</th>
        <th>价格</th>
        <th>操作</th>
    </tr>
   <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->sn?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo?$model->logo:'/upload/default.png',['height'=>50])?></td>
            <td><?=$model->brand->name?></td>
            <td><?=$model->sort?></td>
            <td><?=date("Y-m-d H:i:s",$model->create_time)?></td>
            <td><?=$model->market_price?></td>
            <td>
                <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-picture"></span>相册',['gallery','id'=>$model->id],['class'=>'btn btn-default'])?>
                <?=\yii\bootstrap\Html::a('修改',['goods/edit','id'=>$model->id],['class'=>'btn btn-sm btn-success'])?>||
                <?=\yii\bootstrap\Html::a('删除',['goods/delete','id'=>$model->id],['class'=>'btn btn-sm btn-danger'])?>||
                <?=\yii\bootstrap\Html::a('查看',['goods/view','id'=>$model->id],['class'=>'btn btn-sm btn-success'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager]);