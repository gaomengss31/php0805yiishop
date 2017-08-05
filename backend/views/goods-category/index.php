<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>Tree</th>
        <th>name</th>
        <th>Parent ID</th>
        <th>简介</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->tree?></td>
            <td><?=str_repeat('—',$model['depth']).$model['name']?></td>
            <td><?=$model->parent_id?></td>
            <td><?=$model->intro?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$model->id],['class'=>'btn btn-sm btn-success'])?>||
                <?=\yii\bootstrap\Html::a('删除',['goods-category/delete','id'=>$model->id],['class'=>'btn btn-sm btn-danger'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>