<?php

namespace app\models;

use app\extended\yiisoft\yii2\behaviors\TimestampBehavior;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "places".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $yandex_id
 * @property string $website
 * @property string $average_bill
 * @property string $location
 * @property double $latitude
 * @property double $longitude
 * @property string $point
 * @property string $created_at
 * @property string $updated_at
 */
class Place extends \yii\db\ActiveRecord
{
    public $distance;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'places';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['name', 'yandex_id', 'location', 'latitude', 'longitude', 'point', 'created_at', 'updated_at'], 'required'],
            [['name', 'yandex_id', 'location', 'latitude', 'longitude', 'point'], 'required'],
            //[['location', 'point'], 'string'],
            [['point'], 'string'],
            [['latitude', 'longitude', 'distance'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'address', 'yandex_id', 'website', 'average_bill'], 'string', 'max' => 255]
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
            'address' => 'Address',
            'yandex_id' => 'Yandex ID',
            'website' => 'Website',
            'average_bill' => 'Average Bill',
            'location' => 'Location',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'point' => 'Point',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
