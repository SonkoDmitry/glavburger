<?php

namespace app\extended\telegrambot\api;

use TelegramBot\Api\Exception;
use yii\base\Configurable;

/**
 * Class BotApi
 * @package app\extended\telegrambot\api
 *
 * @property integer $owner Bot owner telegram ID. Use for forward feedback messages
 * @property integer $botId Int number for bot telegram ID
 * @property string $botKey Secret key
 */
class BotApi extends \TelegramBot\Api\BotApi implements Configurable
{
    public $botId;
    public $botKey;
    public $owner;

    public $responseRaw;

    public function __construct($config = [])
    {
        if (!empty($config)) {
            \Yii::configure($this, $config);
        }
        parent::__construct($this->botId . ':' . $this->botKey);
    }

    /**
     * Call method
     *
     * @param string $method
     * @param array $data
     *
     * @return mixed
     */
    public function call($method, array $data = null)
    {
        $options = array(
            CURLOPT_URL => $this->getUrl() . '/' . $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => null,
            CURLOPT_POSTFIELDS => null
        );

        if ($data) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt_array($this->curl, $options);

        $response = json_decode(curl_exec($this->curl), $this->returnArray);
        if (!empty($this->responseRaw)){
            $this->responseRaw = [
                $this->responseRaw,
                $response
            ];
        } else {
            $this->responseRaw=$response;
        }

        if ($this->returnArray) {
            if (!$response['ok']) {
                throw new Exception($response['description'], $response['error_code']);
            }

            return $response['result'];
        }

        if (!$response->ok) {
            throw new Exception($response->description, $response->error_code);
        }

        return $response->result;
    }
}