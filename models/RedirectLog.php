<?php

namespace app\models;

use app\extended\yiisoft\yii2\behaviors\TimestampBehavior;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "redirects_log".
 *
 * @property integer $id
 * @property integer $redirect_id
 * @property string $ip
 * @property string $user_agent
 * @property string $headers
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Redirects $redirect
 */
class RedirectLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'redirects_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['redirect_id', 'created_at', 'updated_at'], 'required'],
            [['redirect_id'], 'required'],
            [['redirect_id'], 'integer'],
            [['user_agent', 'headers'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['ip'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'redirect_id' => 'Redirect ID',
            'ip' => 'Ip',
            'user_agent' => 'User Agent',
            'headers' => 'Headers',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRedirect()
    {
        return $this->hasOne(Redirects::className(), ['id' => 'redirect_id']);
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
