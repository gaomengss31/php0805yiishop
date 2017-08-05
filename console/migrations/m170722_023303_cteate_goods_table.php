<?php

use yii\db\Migration;

class m170722_023303_cteate_goods_table extends Migration
{
    public function up()
    {
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=> $this->string(50)->comment('商品名称'),
            'sn'=> $this->string(20)->comment('货号'),
            'logo'=> $this->string(255)->comment('LOGO图片'),
            'goods_category_id'=>$this->integer()->comment('商品分类id'),
            'brand_id'=>$this->integer()->comment('品牌分类'),
            'market_price'=>$this->decimal(10,2)->comment('市场价格'),
            'shop_price'=>$this->decimal(10,2)->comment('商品价格'),
            'stock'=>$this->integer()->comment('库存'),
            'is_on_sale'=>$this->integer(1)->comment('是否在售(1在售 0下架)'),
            'status'=>$this->integer(2)->comment('状态(1正常 0回收站)'),
            'sort'=>$this->integer(11)->comment('排序'),
            'create_time'=>$this->integer(11)->comment('添加时间'),
            'view_times'=>$this->integer(11)->comment('浏览次数'),
        ]);

    }

    public function down()
    {
        echo "m170722_023303_cteate_goods_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
