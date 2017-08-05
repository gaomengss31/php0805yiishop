<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m170718_051442_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->comment('Ãû³Æ'),
            'intro'=>$this->text()->comment('¼ò½é'),
            'logo'=>$this->string(255)->comment('LOGO'),
            'sort'=>$this->integer()->comment('ÅÅÐò'),
            'status'=>$this->smallInteger()->comment('×´Ì¬'),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
