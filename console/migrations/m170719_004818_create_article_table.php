<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m170719_004818_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'name'=> $this->string(50)->comment('文章名称'),
            'intro'=> $this->text()->comment('分类简介'),
            'article_id'=>$this->integer()->comment('文章分类'),
            'sort'=> $this->integer(11)->comment('排序'),
            'status'=> $this->integer(2)->comment('状态'),
            'create_time'=>$this->integer(11)->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
