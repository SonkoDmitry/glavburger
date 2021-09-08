<?php

return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=database_name',
            'username' => 'database_user',
            'password' => 'database_user_password',
        ],
        'bot' => [
            /*
			 * botId and botKey are splitted by ":" from BotFather response access token
			 * for example, access key is 1234567:abcdefgh. BotId is 1234567 and botKey is abcdefgh
			 */
            'botId' => '1234567',
            'botKey' => 'abcdefgh',
            /**
             * Used for send feedback
             */
            'owner' => '1234567890',
        ],
        'searchMaps' => [
            'key' => '12345-1234-1234-1234-12345678',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/info.log',
                    'categories' => [
                        'telegram',
                    ],
                    'levels' => [
                        'info',
                    ],
                ],
            ],
        ],
    ],
    'params' => [
        'adminEmail' => 'admin@example.com', //some email, don't know for what
        'env' => 'prod', //available values dev|prod|test
        'debug' => false, //available values true|false
        'searchMapsKey' => '12345-1234-1234-1234-12345678',
    ],
];