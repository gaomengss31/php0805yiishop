<?php
use yii\web\JsExpression;
use \kucha\ueditor\UEditor;
/**
 * Created by PhpStorm.
 * User: 79959
 * Date: 2017/7/22
 * Time: 13:55
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'logo')->hiddenInput();
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['goods/s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将图片的路径赋值给logo字段
        $("#goods-logo").val(data.fileUrl);
        //将上传的图片回显
        $("#img").attr('src',data.fileUrl);
    }
}
EOF
        ),
    ]
]);
//回显图片
echo \yii\bootstrap\Html::img($model->logo?$model->logo:false,['id'=>'img','height'=>50]);
//ztree 商品分类
echo $form->field($model,'goods_category_id')->hiddenInput();
echo '<div>
   <ul id="treeDemo" class="ztree"></ul>
</div>
';
//品牌分类
echo $form->field($model,'brand_id')->dropDownList(\backend\models\Goods::getBrandOptions());
echo $form->field($model,'market_price');
echo $form->field($model,'shop_price');
echo $form->field($model,'stock');
//是否在售
echo $form->field($model,'is_on_sale',['inline'=>1])->radioList(\backend\models\Goods::$status_istop);
echo $form->field($model,'sort');
echo $form->field($model,'status',['inline'=>true])->radioList(\backend\models\Goods::$status_options);
echo $form->field($model2,'content')->widget('kucha\ueditor\UEditor',[
    'clientOptions' => [
        //编辑区域大小
        'initialFrameHeight' => '200',
        //设置语言
        'lang' =>'en', //中文为 zh-cn
        //定制菜单
        'toolbars' => [
            [
                'fullscreen', 'source', 'undo', 'redo', '|',
                'fontsize',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat',
                'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                'forecolor', 'backcolor', '|',
                'lineheight', '|',
                'indent', '|'
            ],
        ],
    ]
]);

//这里是提交界面了
echo \yii\bootstrap\Html::submitButton('添加',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();


//调用视图方法加载ztree静态资源
//加载css文件
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
//加载js
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//加载js代码
//把数组转为json字符串
/*$nodes='[
   {name:"test1", open:true, children:[
      {name:"test1_1"}, {name:"test1_2"}]},
   {name:"test2", open:true, children:[
      {name:"test2_1"}, {name:"test2_2"}]}
   ]';*/
//选择的顶级分类
$nodes=\yii\helpers\Json::encode(\backend\models\Goods::getZtreeNodes());
$node_id=$model->goods_category_id;
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    var zTreeObj;
   // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
   var setting = {
       data: {
		simpleData: {
			enable: true,
			idKey: "id",
			pIdKey: "parent_id",
			rootPId: 0
		}
	  },
	  callback: {
		onClick:function(event, treeId, treeNode) {
		   //alert(treeNode.id);
		   //将选中的分类id，赋值给隐藏域id
		   $("#goods-goods_category_id").val(treeNode.id);
		   //console.debug(treeNode.id);
		}
	}
   };
   // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
   var zNodes = {$nodes};

      zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        //展开全部节点  categories 那里 'open'=>1 只展开1级分类
        zTreeObj.expandAll(true);
        //获取节点
        var node = zTreeObj.getNodeByParam("id","{$node_id}", null);
        //选中节点
        zTreeObj.selectNode(node);
JS
));