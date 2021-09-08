<?php

namespace app\models;

use app\extended\yiisoft\yii2\behaviors\TimestampBehavior;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "locations".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $location
 * @property double $latitude
 * @property double $longitude
 * @property string $point
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'locations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['user_id', 'location', 'latitude', 'longitude', 'point', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'location', 'latitude', 'longitude', 'point'], 'required'],
            [['user_id'], 'integer'],
            //[['location', 'point'], 'string'],
            [['point'], 'string'],
            [['latitude', 'longitude'], 'number'],
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
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'point' => 'Point',
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

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}
