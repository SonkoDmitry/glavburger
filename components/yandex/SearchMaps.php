<?php
namespace app\components\yandex;

use app\models\Place;
use yii\base\Configurable;
use yii\db\Expression;

class SearchMaps implements Configurable
{
    public $key;
    private $ch;
    private $url = 'https://search-maps.yandex.ru/v1/';
    private $searchWord = 'бургер';

    public function __construct($config = [])
    {
        if (!empty($config)) {
            \Yii::configure($this, $config);
        }
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param $longitude Долгота точки вокург которой идет поиск
     * @param $latitude Широта точки вокург которой идет поиск
     * @param int $radius Радиус для поиска объектов относительно точки
     * @param int $limit Количество данных в поиске
     */
    public function search($longitude, $latitude, $radius = 500, $limit = 100)
    {
        $longitude = (float) $longitude;
        //расчет длины градуса долготы
        $longWidth = 111.143 - 0.562 * cos(2 * deg2rad($longitude));
        $longSpn = ($radius * 2 / 1000) / $longWidth;

        $latitude = (float) $latitude;
        //расчет длины градуса широты
        $latWidth = 111.321 * cos(deg2rad($latitude)) - 0.094 * cos(3 * deg2rad($latitude));
        $latSpn = ($radius * 2 / 1000) / $latWidth;

        $url = $this->url . '?apikey=' . $this->key . '&lang=ru_RU&type=biz&text=' . rawurlencode($this->searchWord)
            . '&results=' . $limit . '&ll=' . $longitude . ',' . $latitude . '&rspn=0&spn=' . $latSpn . ',' . $longSpn;
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = @json_decode(curl_exec($this->ch), true);
        $new = 0;
        if (!empty($result) && !empty($result['features'])) {
            foreach ($result['features'] as $feature) {
                if (Place::findOne(['yandex_id' => $feature['properties']['CompanyMetaData']['id']])) {
                    continue;
                }

                $location = new Place();
                $location->name = $feature['properties']['CompanyMetaData']['name'];
                $location->yandex_id = $feature['properties']['CompanyMetaData']['id'];
                if (!empty($feature['properties']['CompanyMetaData']['address'])){
                    $location->address = $feature['properties']['CompanyMetaData']['address'];
                }
                if (!empty($feature['properties']['CompanyMetaData']['url'])){
                    $location->website = $feature['properties']['CompanyMetaData']['url'];
                }
                $location->location = new Expression('ST_MakePoint(' . $feature['geometry']['coordinates'][0] . ', ' . $feature['geometry']['coordinates'][1] . ')');
                $location->latitude = $feature['geometry']['coordinates'][1];
                $location->longitude = $feature['geometry']['coordinates'][0];
                $location->point = '(' . $feature['geometry']['coordinates'][0] . ', ' . $feature['geometry']['coordinates'][1] . ')';
                if ($location->save()){
                    $new++;
                }
            }
        }
        //curl_setopt($this->ch, CURLOPT_URL, $url . '&skip=' . $skip);
    }

    public function __destruct(){
        curl_close($this->ch);
    }
}