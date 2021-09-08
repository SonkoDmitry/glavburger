<?php

namespace app\models;

use app\extended\yiisoft\yii2\behaviors\TimestampBehavior;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "results".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $location
 * @property double $longitude
 * @property double $latitude
 * @property string $point
 * @property integer $offset
 * @property integer $total
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 */
class Result extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'results';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['user_id', 'location', 'longitude', 'latitude', 'point', 'total', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'location', 'longitude', 'latitude', 'point', 'total'], 'required'],
            [['user_id', 'offset', 'total'], 'integer'],
            //[['location', 'point'], 'string'],
            [['point'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['created_at', 'updated_at'], 'safe']
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
            'location' => 'Location',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'point' => 'Point',
            'offset' => 'Offset',
            'total' => 'Total',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}
