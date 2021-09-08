<?php

$local = require(__DIR__ . '/local.php');

$main = [
    'id' => 'bot.glavburger',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    //'sourceLanguage' => 'en-US',
    'sourceLanguage' => 'ru',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'bot' => [
            'class' => 'app\extended\telegrambot\api\BotApi',
        ],
	    'botan' => [
		    'class' => 'app\components\botan\BotanComponent',
	    ],
        'searchMaps' => [
            'class' => 'app\components\yandex\SearchMaps',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'forceTranslation' => true,
                    'sourceLanguage' => 'ru-RU',
                ],
            ],
        ],
    ],
];

return \yii\helpers\ArrayHelper::merge($main, $local);