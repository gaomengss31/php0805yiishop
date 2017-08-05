<?php

use yii\db\Migration;

class m170722_024547_cteate_goods_gallery_table extends Migration
{
    public function up()
    {
        $this->createTable('goods_gallery', [
            'id' => $this->primaryKey(),
            'goods_id'=>$this->integer()->comment('商品id'),
            'path'=>$this->string()->comment('图片地址'),
        ]);
    }

    public function down()
    {
        echo "m170722_024547_cteate_goods_gallery_table cannot be reverted.\n";

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
