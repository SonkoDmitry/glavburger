<?php

namespace app\controllers;

use app\models\Feedback;
use app\models\Location;
use app\models\Message;
use app\models\Place;
use app\models\Redirect;
use app\models\User;
use TelegramBot\Api\BotApi;
use Yii;
use yii\base\Security;
use yii\db\Expression;
use yii\db\pgsql\QueryBuilder;
use yii\filters\AccessControl;
use yii\helpers\StringHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\extended\telegrambot\api\types\Message as ApiMessage;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
	    return 'hello world!';
        echo sprintf('%01.4f', microtime(true)) . '<br>';
        //Москва
        $latitude = (float) '55.765615'; //Широта
        $ll_lalitude = (float) '0.005184';
        $longitude = (float) '37.593773'; //Долгота
        $ll_longitude = (float) '0.01266';

        $latWidth = 111.321 * cos(deg2rad($latitude)) - 0.094 * cos(3 * deg2rad($latitude));
        echo 'Длина градуса широты = ' . $latWidth . 'км<br>';
        //echo '500 метров это ' . (($latitude + floatval(0.5 / $latWidth)) - ($latitude - floatval(0.5 / $latWidth))) . ' градуса<br>';
        echo '1000 метров это ' . (1 / $latWidth) . ' градуса<br>';
        $lat = 1 / $latWidth;
        echo $ll_longitude . '=' . ($ll_longitude / floatval(1 / 110.99932138205)) . 'km<br><br>';

        $longWidth = 111.143 - 0.562 * cos(2 * deg2rad($longitude));
        echo 'Длина градуса долготы = ' . $longWidth . 'км<br>';
        $long = 1 / $longWidth;
        /*$img=<<<IMG
https://static-maps.yandex.ru/1.x/?ll={$longitude},{$latitude}&spn={$lat},{$long}&l=map&size=450,450&key=12345-1234-1234-1234-12345678
IMG;
        var_dump($img);
        die;*/
        //echo '<img src="'.$img.'"/>';
        //echo '500 метров это ' . (($longitude + floatval(0.5 / $longWidth)) - ($longitude - floatval(0.5 / $longWidth))) . ' градуса<br>';
        echo '1000 метров это ' . (1 / $longWidth) . ' градуса<br>';
        echo $ll_lalitude . '=' . ($ll_lalitude / floatval(1 / $latWidth)) . 'km<br><br>';

        //echo "spn:0.01389384269715066,0.005328888218123495<br>";
        echo "spn:0.014837980270378637,0.005328874108215587<br>";



        /*$tmp=tmpfile();
        fwrite($tmp,file_get_contents('https://static-maps.yandex.ru/1.x/?l=map&pt=37.484834,55.79589,pm2am~37.483023,55.799588,pm2bm&size=600,450'));
        //var_dump(stream_get_meta_data($tmp));
        //die;

        $curl=curl_file_create(stream_get_meta_data($tmp)['uri'],'image/png','image.png');
        echo '<pre>';
        //var_dump($curl);

        $result=Yii::$app->bot->sendPhoto(1871532,$curl);*/
        //var_dump($result);


        //die;

        /*$latitude='55.767499000000001';
        $longitude='37.598590999999999';
        $location = new Place();
        $location->yandex_id='1';
        $location->name = 'J.P. Burger';
        $location->location = new Expression('ST_MakePoint(' . $longitude . ', ' . $latitude . ')');
        $location->latitude = $latitude;
        $location->longitude = $longitude;
        $location->point = '(' . $longitude . ', ' . $latitude . ')';
        if ($location->save()) {
            var_dump($location->id);
        } else {
            var_dump($location->errors);
        }

        $latitude='55.769934999999997';
        $longitude='37.596874999999997';
        $location = new Place();
        $location->name = 'KFC';
        $location->yandex_id='2';
        $location->location = new Expression('ST_MakePoint(' . $longitude . ', ' . $latitude . ')');
        $location->latitude = $latitude;
        $location->longitude = $longitude;
        $location->point = '(' . $longitude . ', ' . $latitude . ')';
        if ($location->save()) {
            var_dump($location->id);
        } else {
            var_dump($location->errors);
        }

        $latitude='55.767681000000003';
        $longitude='37.598097000000003';
        $location = new Place();
        $location->name = 'Duckstars';
        $location->yandex_id='3';
        $location->location = new Expression('ST_MakePoint(' . $longitude . ', ' . $latitude . ')');
        $location->latitude = $latitude;
        $location->longitude = $longitude;
        $location->point = '(' . $longitude . ', ' . $latitude . ')';
        if ($location->save()) {
            var_dump($location->id);
        } else {
            var_dump($location->errors);
        }*/

        /*$latitude='55.767499000000001';
        $longitude='37.598590999999999';
        $location = new Place();
        $location->yandex_id='1';
        $location->name = 'J.P. Burger';
        $location->location = new Expression('POINT(' . $longitude . ', ' . $latitude . ')');
        $location->latitude = $latitude;
        $location->longitude = $longitude;
        $location->point = '(' . $longitude . ', ' . $latitude . ')';
        if ($location->save()) {
            var_dump($location->id);
        } else {
            var_dump($location->errors);
        }

        die;*/

        /**
         * @var Location $location
         */
        //$location = Location::findOne(2);

        $sql1 = <<<SQL
SELECT
  *,
  ST_distance_sphere(location, )) as km
FROM
  public.places
ORDER BY km
SQL;
        $sql2 = <<<SQL
select
  *,
  ST_Geomfromtext(ST_AsText(location)) as geom1,
  ST_Geomfromtext(ST_AsText(location), 4326) as geom2,
  ST_AsText(location) as geom3,
  round(ST_distance_sphere(ST_AsText(location),'POINT(37.593773 55.765615)')) as distance,
  ceiling(ST_distance_sphere(ST_AsText(location),'POINT(37.593773 55.765615)') / 1.5 / 60) as minites
FROM
  public.places
ORDER BY
  distance
SQL;

        $sql3 = <<<SQL3
SELECT
    name,
    point,
    point[0] as x,
    point[1] as y,
    location,
    ST_AsText(location),
    ST_SetSRID(location,4269),
    CONCAT('POINT(',point[0],' ',point[1],')') as point_concat,
    ST_GeometryFromText(CONCAT('POINT(',point[0],' ',point[1],')')),
    ST_PointFromText(CONCAT('POINT(',point[0],' ',point[1],')')) as point_text
FROM
    public.places
LIMIT 10
SQL3;

        $sql4 = <<<SQL
select
  ST_GeometryFromText('POINT(37.593773 55.765615)'),
  ST_MakePoint('37.593773', '55.765615')
SQL;

        $longitude = '37.593773';
        $latitude = '55.765615';

        //$query=new QueryBuilder();
        //$query->con
        $sql5 = <<<SQL
select
  *,
  round(ST_distance_sphere(ST_AsText(location),'POINT({$longitude} {$latitude})')) as distance
FROM
  public.places
WHERE
  ST_DWithin(places.location, ST_GeometryFromText('POINT({$longitude} {$latitude})'), 2000, true)
ORDER BY distance
LIMIT 10
SQL;

        $query = Place::find()
            ->select('*')
            ->addSelect("round(ST_distance_sphere(ST_AsText(location),'POINT({$longitude} {$latitude})')) as distance")
            ->where("ST_DWithin(places.location, ST_GeometryFromText('POINT({$longitude} {$latitude})'), 2000, true)")
            ->orderBy('distance asc');

        //$query->limit(1);
        //$query->offset(0);
        /**
         * @var Place $place
         */
        foreach ($query->all() as $place) {
            if (!$redirect = Redirect::findOne(['place_id' => $place->yandex_id])) {
                do {
                    $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 6);
                } while (Redirect::findOne(['code' => $code]));
                $redirect = new Redirect();
                $redirect->place_id = $place->id;
                $redirect->code = $code;
                $redirect->url = 'https://maps.yandex.ru/?ol=biz&oid=' . $place->yandex_id;
                $redirect->save();
            }
            //do {
            //}
            echo $place->name . ' - ' . $place->distance . '<br>';
            echo \Yii::t('app',
                    'До ресторана {n, plural, one{# метр} few{# метра} many{# метров} other{# метра}} '
                    . '({m, plural, one{# минута} few{# минуты} many{# минут} other{# минуты}} пешком)',
                    ['n' => $place->distance, 'm' => ceil($place->distance / 1.5 / 60)]) . "<br>";
            echo Yii::$app->urlManager->createAbsoluteUrl('/sl/'.$redirect->code).'/<br><br>';
        }

        die;
        //echo "PHP: " . PHP_VERSION . "\n";
//echo "ICU: " . INTL_ICU_VERSION . "\n";
        //echo Yii::$app->formatter->asDatetime(time(),'full');
        //die;


        //var_dump($query);
        //die;

        $sqlCount = "select count(*) from places where ST_DWithin(places.location, ST_GeometryFromText('POINT({$longitude} {$latitude})'), 500, true)";
        $leadsCount = Yii::$app->db->createCommand($sqlCount)->queryScalar();
        //var_dump($leadsCount);
        //die;
        echo '<pre>';
        if ($leadsCount < 10) {
            echo "finded {$leadsCount} records. start serach more<br>";
            //Yii::$app->searchMaps->search($longitude, $latitude);
            $leadsCount2 = Yii::$app->db->createCommand($sqlCount)->queryScalar();
            echo "after additional finded {$leadsCount2} records<br>";
            if ($leadsCount2==0){
                die('nothing find near((');
            }

            $query = Place::find()
                ->select('*')
                ->addSelect("round(ST_distance_sphere(ST_AsText(location),'POINT({$longitude} {$latitude})')) as distance")
                ->where("ST_DWithin(places.location, ST_GeometryFromText('POINT({$longitude} {$latitude})'), 500, true)")
                ->orderBy('distance asc');

            $query->limit(1);
            $query->offset(0);
            foreach ($query->all() as $place) {
                echo $place->name . ' - ' . $place->distance . '<br>';
                echo \Yii::t('app',
                        'До ресторана {n, plural, one{# метр} few{# метра} many{# метров} other{# метра}} '
                        . '({m, plural, one{# минута} few{# минуты} many{# минут} other{# минуты}} пешком)',
                        ['n' => $place->distance, 'm' => ceil($place->distance / 1.5 / 60)]) . "<br><br>";
            }
        }
        //var_dump($leadsCount);
        die;

        if ($leadsCount) {
            /**
             * @var Place $place
             */
            echo '<pre>';
            //var_dump(Yii::$app->formatter);
            foreach ($query->all() as $place) {
                echo $place->name . ' - ' . $place->distance . '<br>';
                echo \Yii::t('app',
                        'До ресторана {n, plural, one{# метр} few{# метра} many{# метров} other{# метра}} '
                        . '({m, plural, one{# минута} few{# минуты} many{# минут} other{# минуты}} пешком)',
                        ['n' => $place->distance, 'm' => ceil($place->distance / 1.5 / 60)]) . "<br><br>";
            }
        }
        die;

        $result = Yii::$app->db->createCommand($sql5)->queryAll();

        echo '<pre>';
        foreach ($result as $row) {
            var_dump($row);
        }
        die;

        //User::createIfNotExist(['id'=>1871532]);

        $bot = new BotApi('69576240:AAHPnypb5HOv2OXFIzaG7P-Xup6JCviORqw');
        echo '<pre>';
        var_dump($bot->getMe()->getId());
        var_dump($bot->sendPhoto('1871532', \CURLFile('https://pp.vk.me/c624416/v624416284/39530/JzPibD6ctYs.jpg')));
        //var_dump(Yii::$app->bot->getMe());
        //echo '<pre>';
        //var_dump(Yii::$app->user->identity->telegram_id);
        //var_dump(Yii::$app->user->id);
        //die;
    }
}
