<form action=""  class="btn">
    <input type="text" name="keywords"/>
    <?php
    echo \yii\bootstrap\Html::submitButton('搜索',['class'=>'btn btn-info']);
    ?>
</form>
<p>
    <?=\yii\bootstrap\Html::a('垃圾桶',['article/index2'],['class'=>'btn btn-info'])?>
</p>

<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>文章名称</th>
        <th>文章简介</th>
        <th>分类ID</th>
        <th>排序</th>
        <th>文章状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->articlecategory->name?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\Article::$status_options[$model->status]?></td>

            <td><?=date("Y-m-d h:i:s",$model->create_time)?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['article/edit','id'=>$model->id],['class'=>'btn btn-sm btn-success'])?>||
                <?=\yii\bootstrap\Html::a('删除',['article/delete','id'=>$model->id],['class'=>'btn btn-sm btn-danger'])?>
            </td>
        </tr>
    <?php endforeach;?>


</table>
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager]);