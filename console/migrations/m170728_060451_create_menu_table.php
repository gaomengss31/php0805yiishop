<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m170728_060451_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->comment('菜单名称'),
            'url'=>$this->string(255)->comment('链接'),
            'parent_id'=>$this->integer()->comment('父级'),
            'sort'=>$this->integer()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
