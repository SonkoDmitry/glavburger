<?php

namespace app\extended\telegrambot\api\types;

use TelegramBot\Api\Types\Chat;
use TelegramBot\Api\Types\User;

/**
 * Class Message
 * @package app\extended\telegrambot\api\types
 * @property string $type Type of message
 */
class Message extends \TelegramBot\Api\Types\Message
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = array(
        'message_id' => true,
        'from' => '\app\extended\telegrambot\api\types\User',
        'date' => true,
        'file_size' => true,
        'chat' => '\TelegramBot\Api\Types\Chat',
        'forward_from' => '\TelegramBot\Api\Types\User',
        'forward_date' => true,
        'reply_to_message' => '\TelegramBot\Api\Types\Message',
        'text' => true,
        'audio' => '\TelegramBot\Api\Types\Audio',
        'document' => '\TelegramBot\Api\Types\Document',
        'photo' => '\TelegramBot\Api\Types\ArrayOfPhotoSize',
        'sticker' => '\TelegramBot\Api\Types\Sticker',
        'video' => '\TelegramBot\Api\Types\Video',
        'contact' => '\TelegramBot\Api\Types\Contact',
        'location' => '\app\extended\telegrambot\api\types\Location',
        'new_chat_participant' => '\TelegramBot\Api\Types\User',
        'left_chat_participant' => '\TelegramBot\Api\Types\User',
        'new_chat_title' => true,
        'new_chat_photo' => '\TelegramBot\Api\Types\ArrayOfPhotoSize',
        'delete_chat_photo' => true,
        'group_chat_created' => true
    );

    /**
     * @return User|Chat
     */
    public function getChat()
    {
        return $this->chat;
    }

    /**
     * @return \app\extended\telegrambot\api\types\User
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}