<?php
namespace app\controllers;

use app\models\Feedback;
use app\models\Message;
use app\models\Place;
use app\models\Redirect;
use app\models\Result;
use app\models\User;
use app\extended\telegrambot\api\types\Message as ApiMessage;
use yii\db\Expression;
use yii\helpers\FileHelper;
use yii\web\Controller;
use Yii;

class BotController extends Controller
{
    public function init()
    {
        parent::init();
    }

    /**
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id === 'webhook') {
            $this->enableCsrfValidation = false;
            Yii::$app->request->parsers = [
                'application/json' => 'yii\web\JsonParser',
            ];
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        echo 'index';
    }

    public function actionWebhook()
    {
        $data = Yii::$app->request->bodyParams;
        if (is_array($data)) {
            $dump = [
                'request' => $data,
                'response' => null,
            ];

            if (!empty($data['message'])) {
                $message = ApiMessage::fromResponse($data['message']);
                User::createIfNotExist($message->getFrom());

                $detect = Message::detect($message);
                if ($detect != 'location' && $detect != 'feedback' && $detect != 'help' && $detect != 'start'
                    && ($lastMessage = Message::find()->where(['user_id' => 1,])->orderBy('id desc')->one())
                    && ($rawData = @json_decode($lastMessage->raw, true))
                    && Message::detect(ApiMessage::fromResponse($rawData['message'])) == 'feedback'
                ) {
                    $model = new Feedback();
                    $model->user_id = Yii::$app->user->id;
                    $model->text = $message->getText() ? $message->getText() : json_encode($data);
                    $model->save();

                    $detect = 'feedback_response';
                }

	            \Yii::$app->botan->track(Yii::$app->user->id, is_null($detect) ? 'other' : $detect);

                switch ($detect) {
                    case 'location':
                        $this->_processLocation($message->getChat()->getId(),
                            $message->getLocation()->getLatitude(),
                            $message->getLocation()->getLongitude(), true);
                        break;

                    case 'feedback':
                        $this->_processFeedback($message);
                        break;

                    case 'help':
                        $this->_processHelp($message);
                        break;

                    case 'more':
                        if (!$searchRecord = Result::find()->where([
                            'user_id' => Yii::$app->user->id,
                        ])->limit(1)->orderBy('created_at desc')->one()
                        ) {
                            $this->_sendIntro($message);
                        } else {
                            $this->_processLocation($message->getChat()->getId(), $searchRecord->latitude,
                                $searchRecord->longitude);
                        }
                        break;

                    case 'feedback_response':
                        $this->_processFeedback($message, true);
                        break;

                    case 'start':
                    default:
                        $this->_sendIntro($message);
                        break;
                }
                Message::store($data);
            }
            FileHelper::createDirectory(Yii::getAlias('@app/storage/' . date('Y.m.d', time())));
            $file = fopen(Yii::getAlias('@app/storage/' . date('Y.m.d/H:i:s', time()) . ':'
                . explode('.', sprintf('%01.4f', microtime(true)))[1] . '.json'), 'a');

            $dump['response'] = Yii::$app->bot->responseRaw;
            fwrite($file, json_encode($dump));
            fclose($file);
        }
    }

    /**
     * @param \app\extended\telegrambot\api\types\Message $message
     */
    protected function _sendIntro($message)
    {
        Yii::$app->bot->sendMessage($message->getChat()->getId(),
            'Для начала поиска отправьте своё местоположение. Сделать это можно из того-же меню, из которого вы прикрепляете картинки.');
    }

    protected function _processMore()
    {
    }

    /**
     * @param $to
     * @param $latitude
     * @param $longitude
     * @param $isNew
     *
     * @throws \Exception
     */
    protected function _processLocation($to, $latitude, $longitude, $isNew = false)
    {
        //Yii::$app->bot->sendMessage($to,'Сейчас мы что нибудь найдем рядом с тобой, дружище!');

        if ((!$searchRecord = Result::find()->where([
            'user_id' => Yii::$app->user->id,
            'longitude' => $longitude,
            'latitude' => $latitude,
        ])->limit(1)->orderBy('created_at desc')->one()) || $isNew
        ) {
            Yii::$app->searchMaps->search($longitude, $latitude, 2000);
            $sqlCount = "select count(*) from places where ST_DWithin(places.location, ST_GeometryFromText('POINT({$longitude} {$latitude})'), 2000, true)";
            $count = Yii::$app->db->createCommand($sqlCount)->queryScalar();

            $searchRecord = new Result();
            $searchRecord->user_id = Yii::$app->user->id;
            $searchRecord->location = new Expression('ST_MakePoint(' . $longitude . ', ' . $latitude . ')');
            $searchRecord->latitude = $latitude;
            $searchRecord->longitude = $longitude;
            $searchRecord->point = '(' . $longitude . ', ' . $latitude . ')';
            $searchRecord->total = $count;
            $searchRecord->save();
        }

        if ($searchRecord->total == 0) {
            Yii::$app->bot->sendMessage($to, 'К сожалению, мы не нашли рядом ничего подходящего :-(');

            return;
        } else {
            if ($searchRecord->offset == $searchRecord->total) {
                Yii::$app->bot->sendMessage($to,
                    'К сожалению, рядом больше ничего нет. Попробуйте отправить другую геолокацию.');
            } else {
                /**
                 * @var Place $query
                 */
                $query = Place::find()
                    ->select('*')
                    ->addSelect("round(ST_distance_sphere(ST_AsText(location),'POINT({$longitude} {$latitude})')) as distance")
                    ->where("ST_DWithin(places.location, ST_GeometryFromText('POINT({$longitude} {$latitude})'), 2000, true)")
                    ->orderBy('distance asc')->limit(1)->offset($searchRecord->offset)->one();
                $searchRecord->offset++;
                $searchRecord->update();

	            $text = (!empty($query->name) ? $query->name . "\n" : "")
	                    . (!empty($query->address) ? $query->address . "\n" : "")
	                    . (!empty($query->website) ? $query->website . "\n" : "")
                    . \Yii::t('app',
                        'До ресторана {n, plural, one{# метр} few{# метра} many{# метров} other{# метра}} '
                        . '({m, plural, one{# минута} few{# минуты} many{# минут} other{# минуты}} пешком)',
                        [
                            'n' => $query->distance,
                            'm' => ceil($query->distance / 1.5 / 60),
                        ]) . "\n";

                if (!$redirect = Redirect::findOne(['place_id' => $query->yandex_id])) {
                    do {
                        $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 6);
                    } while (Redirect::findOne(['code' => $code]));
                    $redirect = new Redirect();
                    $redirect->place_id = $query->id;
                    $redirect->code = $code;
                    $redirect->url = 'https://maps.yandex.ru/?ol=biz&oid=' . $query->yandex_id;
                    $redirect->save();
                }
                $text .= "Смотреть на Яндекс.Картах - " . Yii::$app->urlManager->createAbsoluteUrl('/sl/' . $redirect->code) . '/';

                Yii::$app->bot->sendMessage($to, $text, true);

                $tmp = tmpfile();
                fwrite($tmp, file_get_contents('https://static-maps.yandex.ru/1.x/?l=map&pt=' . $longitude . ','
                    . $latitude . ',pm2am~' . $query->longitude . ',' . $query->latitude . ',pm2bm&size=600,450'));
                //var_dump(stream_get_meta_data($tmp));
                //die;

                $curl = curl_file_create(stream_get_meta_data($tmp)['uri'], 'image/png', 'image.png');
                Yii::$app->bot->sendPhoto($to, $curl);
            }
        }
    }

    /**
     * @param \app\extended\telegrambot\api\types\Message $message
     */
    protected function _processHelp($message)
    {
        Yii::$app->bot->sendMessage($message->getChat()->getId(),
            "На самом деле все просто 😃\nЕсли хочешь найти рядом место, где готовят бургеры, отправь свое местоположение.\n"
            . "Сделать это можно из меню прикрепления фотографий и изображений, выбрав пункт \"Location\"\n"
            . "Для того, чтобы оставить отзыв, напиши /feedback и ответном сообщении, можешь можешь написать боту всё, что тебя тревожит 😄\n"
            . "Получить следующее заведение из результатов поиска можно отправив /more (предварительно естественно отправив геолокацию 👆). "
            . "Сделать приятно разработчику 😊, можно перейдя по ссылке https://telegram.me/storebot?start=glavburgerbot и оставив оценку с отзывом 👍",
            true);
    }

    /**
     * @param \app\extended\telegrambot\api\types\Message $message
     */
    protected function _processFeedback($message, $response = false)
    {
        if ($response) {
            Yii::$app->bot->sendMessage($message->getChat()->getId(), 'Большое спасибо за отзыв!');
            Yii::$app->bot->sendMessage(Yii::$app->bot->owner,
                'Новый отзыв от ' . $message->getChat()->getFirstName()
                . (($user = $message->getChat()->getUsername()) ? ' @' . $user : '') . ' (' . $message->getChat()->getId() . ')');
            Yii::$app->bot->forwardMessage(Yii::$app->bot->owner, $message->getChat()->getId(),
                $message->getMessageId());
        } else {
            Yii::$app->bot->sendMessage($message->getChat()->getId(), 'Оставьте отзыв или сообщите об ошибке:');
        }
    }
}