<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Article_Category".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property integer $sort
 * @property integer $status
 */
class ArticleCategory extends \yii\db\ActiveRecord

{
    public static $status_options = [
        -1=>'删除',
        0=>'隐藏',
        1=>'正常'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Article_Category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'intro' => 'Intro',
            'sort' => 'Sort',
            'status' => 'Status',
        ];
    }
}
