<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>分类名称</th>
        <th>分类简介</th>
        <th>排序sort</th>
        <th>分类状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->sort?></td>
            <td><?=\backend\models\ArticleCategory::$status_options[$model->status]?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['article-category/edit','id'=>$model->id],['class'=>'btn btn-sm btn-success'])?>||
                <?=\yii\bootstrap\Html::a('修改',['article-category/delete','id'=>$model->id],['class'=>'btn btn-sm btn-danger'])?>
            </td>
        </tr>
    <?php endforeach;?>


</table>
<?php
/**/
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager]);