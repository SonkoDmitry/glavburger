<?php

namespace app\models;

use app\extended\yiisoft\yii2\behaviors\TimestampBehavior;
use yii\db\Expression;
use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $telegram_message_id
 * @property integer $telegram_update_id
 * @property string $type
 * @property string $content
 * @property string $raw
 * @property string $created_at
 * @property string $updated_at
 */
class Message extends \yii\db\ActiveRecord
{
    public static $startCommands = [
        '/start',
        'start',
        'старт',
        '/старт',
    ];

    public static $moreCommands = [
        '/more',
        'more',
        'еще',
        'ещё',
        '/еще',
        '/ещё',
    ];

    public static $feedbackCommands = [
        '/feedback',
        'feedback',
        'отзыв',
        '/отзыв',
    ];

    public static $helpCommands = [
        '/help',
        'help',
        'помощь',
        'помошь',
        '/помощь',
        '/помошь',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['user_id', 'telegram_message_id', 'telegram_update_id', 'type', 'content', 'raw', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'telegram_message_id', 'telegram_update_id', 'type', 'content', 'raw'], 'required'],
            [['user_id', 'telegram_message_id', 'telegram_update_id'], 'integer'],
            [['content', 'raw'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 255]
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
            'telegram_message_id' => 'Telegram Message ID',
            'telegram_update_id' => 'Telegram Update ID',
            'type' => 'Type',
            'content' => 'Content',
            'raw' => 'Raw',
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

    /**
     * @param \app\extended\telegrambot\api\types\Message $message
     *
     * @return null|string
     */
    public static function detect($message)
    {
        if ($text = $message->getText()) {
	        $text = trim(strtolower($text));
	        if (empty($text)) {
		        return null;
	        }
            foreach (self::$startCommands as $command) {
                if ($text === $command || substr($text, 0, strlen($command)) === $command) {
                    if (Yii::$app->user->identity->createdNow && ($pos = strpos($text, ' ')) !== false) {
                        User::updateAll(
                            ['referrer' => substr($text, $pos + 1)],
                            'id=:uid', [':uid' => Yii::$app->user->id]);
                    }

                    return 'start';
                }
            }

            if (in_array($text, self::$moreCommands)) {
                return 'more';
            } elseif (in_array($text, self::$feedbackCommands)) {
                return 'feedback';
            } elseif (in_array($text, self::$helpCommands)) {
                return 'help';
            } else {
                return null;
            }
        } elseif ($message->getLocation()) {
            return 'location';
        }

        return null;
    }

    public static function store($message)
    {
        $model = new self;
        if (!empty($message['message']['text'])) {
            $model->type = 'text';
            $model->content = $message['message']['text'];
        } elseif (!empty($message['message']['location'])) {
            $model->type = 'location';
            $model->content = json_encode($message['message']['location']);
        } else {
            $model->type = 'other';
            $model->content = 'other';
        }

        $model->raw = json_encode($message);
        $model->user_id = Yii::$app->user->id;
        $model->telegram_message_id = $message['message']['message_id'];
        $model->telegram_update_id = $message['update_id'];
        $model->created_at = $model->updated_at = date('Y-m-d H:i:s', $message['message']['date']);
        $model->save();
    }
}
