<?php

use yii\db\Migration;

class m170722_024222_cteate_goods_intro_table extends Migration
{
    public function up()
    {
        $this->createTable('goods_intro', [
            'goods_id' => $this->primaryKey(),
            'content'=>$this->text()->comment('商品描述'),
        ]);

    }

    public function down()
    {
        echo "m170722_024222_cteate_goods_intro_table cannot be reverted.\n";

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
