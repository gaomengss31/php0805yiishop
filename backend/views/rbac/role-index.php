<table class="table table-bordered table-condensed">
    <tr>
        <th>角色名称</th>
        <th>角色描述</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->description?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改1',['role-edit','name'=>$model->name])?>||

                <?=\yii\bootstrap\Html::a('删除1111',['role-del','name'=>$model->name])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>