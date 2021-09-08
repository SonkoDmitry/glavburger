<?php
namespace app\extended\telegrambot\api\types;

class Location extends \TelegramBot\Api\Types\Location
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