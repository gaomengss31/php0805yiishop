<p>
    <?=\yii\bootstrap\Html::a('垃圾桶',['brand/index2'],['class'=>'btn btn-info'])?>
</p>

<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>品牌名称</th>
        <th>品牌简介</th>
        <th>品牌logo</th>
        <th>排序sort</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=\yii\bootstrap\Html::img($model->logo?$model->logo:'/upload/default.png',['height'=>50])?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\Brand::$status_options[$model->status]?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$model->id],['class'=>'btn btn-sm btn-success'])?>||
                <?=\yii\bootstrap\Html::a('删除',['brand/del','id'=>$model->id],['class'=>'btn btn-sm btn-danger'])?>
            </td>
        </tr>
    <?php endforeach;?>


</table>

<?php
//添加分页工具条
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager]);