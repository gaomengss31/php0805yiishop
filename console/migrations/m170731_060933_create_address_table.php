<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170731_060933_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'user_id'=>$this->integer(),
            'username'=>$this->string(50)->comment('用户名'),
            'province'=>$this->string(50)->comment('省'),
            'city'=>$this->string(50)->comment('市'),
            'area'=>$this->string(50)->comment('区'),
            'address'=>$this->string(200)->comment('详细地址'),
            'tel'=>$this->integer()->comment('电话'),
            'status'=>$this->integer(1)->comment('状态（1默认，0不默认）'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
