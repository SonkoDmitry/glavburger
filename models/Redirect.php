<?php

namespace app\models;

use app\extended\yiisoft\yii2\behaviors\TimestampBehavior;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "redirects".
 *
 * @property integer $id
 * @property integer $place_id
 * @property string $code
 * @property string $url
 * @property integer $counter
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Places $place
 * @property RedirectsLog[] $redirectsLogs
 */
class Redirect extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'redirects';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['place_id', 'code', 'url', 'created_at', 'updated_at'], 'required'],
            [['place_id', 'code', 'url'], 'required'],
            [['place_id', 'counter'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'place_id' => 'Place ID',
            'code' => 'Code',
            'url' => 'Url',
            'counter' => 'Counter',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlace()
    {
        return $this->hasOne(Places::className(), ['id' => 'place_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRedirectsLogs()
    {
        return $this->hasMany(RedirectsLog::className(), ['redirect_id' => 'id']);
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
