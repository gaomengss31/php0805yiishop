<?php
namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property integer $tel
 * @property integer $status
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'tel', 'status'], 'integer'],
            [['username', 'province', 'city', 'area'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'username' => '用户名',
            'province' => '省',
            'city' => '市',
            'area' => '区',
            'address' => '详细地址',
            'tel' => '电话',
            'status' => '状态（1默认，0不默认）',
        ];
    }
}
