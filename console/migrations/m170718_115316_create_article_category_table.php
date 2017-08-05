<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_category`.
 */
class m170718_115316_create_article_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article_category', [
            'id' => $this->primaryKey(),
            'name'=> $this->string(50)->comment('ÎÄÕÂÃû³Æ'),
            'intro'=> $this->text()->comment('·ÖÀà¼ò½é'),
            'sort'=> $this->integer(11)->comment('ÅÅÐò'),
            'status'=> $this->integer(2)->comment('×´Ì¬'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_category');
    }
}
