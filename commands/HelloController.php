<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Place;
use yii\console\Controller;
use yii\db\Expression;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     *
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
	    $result=\Yii::$app->botan->track(1212,'msg2');
	    var_dump($result);
	    die;
        echo $message . "\n";

        $step=25;
        $url = 'https://search-maps.yandex.ru/v1/?apikey=12345-1234-1234-1234-12345678&lang=ru_RU&type=biz&text=%D0%B1%D1%83%D1%80%D0%B3%D0%B5%D1%80&results='.$step;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch,CURLOPT_URL,$url);
        //curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        $skip = 0;

        $cnt=0;
        $total=0;

        $longitude = (float) '37.593773';
        $latitude = (float) '55.765615';
        do {
            curl_setopt($ch, CURLOPT_URL, $url . '&skip=' . $skip);
            $result = json_decode(curl_exec($ch), true);
            $total+=count($result['features']);
            foreach ($result['features'] as $feature){
                var_dump($feature);
                die;
                if (Place::findOne(['yandex_id'=>$feature['properties']['CompanyMetaData']['id']])){
                    echo "skipped\n";
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
                    $cnt++;
                    echo "\rInserted {$cnt} records of {$total}\n";
                    flush();
                } else {
                    var_dump($location->errors);
                    die;
                }
                //var_dump($feature);
                //die;
            }
            $skip += $step;
            //sleep(5);
        } while (count($result['features']));
        var_dump($result);
        echo "Inserted {$cnt} records!\n";
    }
}
