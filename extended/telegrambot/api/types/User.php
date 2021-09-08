<?php
namespace app\extended\telegrambot\api\types;

class User extends \TelegramBot\Api\Types\User
{
    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = [];
        foreach (static::$map as $key => $item) {
            if ($item === true) {
                $method = 'get' . self::toCamelCase($key);
                $attributes[$key] = $this->$method();
            }
        }
        return $attributes;
    }
}