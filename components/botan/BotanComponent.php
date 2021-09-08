<?php
namespace app\components\botan;

use yii\base\Configurable;
use Yii;

class BotanComponent implements Configurable
{
	public $token;

	/**
	 * @var Curl handle
	 */
	protected $ch;

	/**
	 * @var string Tracker url
	 */
	protected $trackerUrl = 'https://api.botan.io/track?';

	public function __construct($config = [])
	{
		if (!empty($config)) {
			Yii::configure($this, $config);
		}

		if (empty($this->token)) {
			throw new Exception('Botan api token cannot be empty');
		}
	}

	/**
	 * @param string $uid Uniq ID, for example telegram from_id
	 * @param mixed $message It may be message text for message event or latitude/longitude for location event
	 * @param string $name Event name for show in statistics, eg. Text message, Location, Sticker
	 */
	public function track($uid, $eventName = 'Message', $eventData =[])
	{
		if (!$this->ch) {
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->ch, CURLOPT_POST, true);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
				'Content-type' => 'application/json',
			]);
		}
		$url = $this->trackerUrl . 'token=' . $this->token . '&uid=' . $uid . '&name=' . $eventName;
		curl_setopt($this->ch, CURLOPT_URL, $url);

		if (!empty($eventData)) {
			if (!is_array($eventData)) {
				throw new \Exception('Message must be array');
			}
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($eventData));
		}
		$result = json_decode(curl_exec($this->ch), true);

		return $result;
	}
}