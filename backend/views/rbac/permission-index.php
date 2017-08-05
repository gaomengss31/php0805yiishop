<table class="table table-bordered table-condensed">
    <tr>
        <th>权限名称</th>
        <th>权限描述</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['permission-edit','name'=>$model->name])?>||

                <?=\yii\bootstrap\Html::a('删除',['permission-del','name'=>$model->name])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>