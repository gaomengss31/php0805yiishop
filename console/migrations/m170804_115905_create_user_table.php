<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m170804_115905_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username'=>$this->string()->comment('用户名'),
            'auth_key'=>$this->string(255)->comment('auth_key'),
            'password_hash'=>$this->string(255)->comment('密码'),
            'password_reset_token'=>$this->string(255)->comment('password_reset_token'),
            'email'=>$this->string(255)->comment('邮箱'),
            'status'=>$this->integer()->comment('状态'),
            'created_at'=>$this->integer()->comment('创建时间'),
            'updated_at'=>$this->integer()->comment('更新时间'),
            'last_login_ip'=>$this->string()->comment('ip'),
            'last_login_time'=>$this->integer()->comment('时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
