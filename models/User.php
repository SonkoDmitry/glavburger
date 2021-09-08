<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\extended\yiisoft\yii2\behaviors\TimestampBehavior;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $telegram_id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $created_at
 * @property string $updated_at
 * @property string $referrer
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public static $id;
    public $createdNow = false;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        //echo "find ident";
        return ($record = self::findOne($id)) ? $record : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        //echo "get id";
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        //echo "get authkey";
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * Validates password
     *
     * @param  string $password password to validate
     *
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['telegram_id', 'created_at', 'updated_at'], 'required'],
            ['telegram_id', 'required'],
            [['telegram_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'last_name', 'username'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'telegram_id' => 'Telergram ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'username' => 'Username',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'referrer' => 'Referrer',
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

    /**
     * @param mixed $data
     *
     * @return bool|null Возращает null если юзер уже был создан, true если не было и создался, false если не было и не создался
     */
    public static function createIfNotExist(\app\extended\telegrambot\api\types\User $data)
    {
        if (!$user = self::findOne(['telegram_id' => $data->getId()])) {
            $user = new self;
            $user->telegram_id = $data->getId();
            $user->attributes = $data->getAttributes();
            if (!$user->save()) {
                return false;
            }
            $user->createdNow = true;
        }

        self::$id = $user->id;
        Yii::$app->user->login($user);

        return true;
    }
}
