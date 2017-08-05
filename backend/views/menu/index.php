<table class="table table-bordered table-condensed">
    <tr>
        <th>菜单名称</th>
        <th>菜单链接</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->name?></td>
            <td><?=$model->url?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id],['class'=>'btn btn-sm btn-success'])?>||
                <?=\yii\bootstrap\Html::a('删除',['menu/delete','id'=>$model->id],['class'=>'btn btn-sm btn-danger'])?>
            </td>
        </tr>
        <?php foreach($model->children as $child):?>
            <tr>
                <td><?=$child->name?></td>
                <td><?=$child->url?></td>
                <td>
                    <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$child->id],['class'=>'btn btn-sm btn-success'])?>||
                    <?=\yii\bootstrap\Html::a('删除',['menu/delete','id'=>$child->id],['class'=>'btn btn-sm btn-danger'])?>
                </td>
            </tr>
        <?php endforeach;?>
    <?php endforeach;?>
</table>