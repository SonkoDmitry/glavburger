<?php

$main = require(__DIR__ . '/main.php');

$web = [
    'bootstrap' => ['log'],
	'defaultRoute' => 'site/index',
	//'layout' => false,
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'ZRpT_n7XW7X2r8Qd-VJ12mvue0WMaQHG',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning',
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                ],
            ],
        ],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			//'enableStrictParsing'=>true,
			//'suffix' => '.html',
			'rules' => [
				'gii' => 'gii',
				'POST /hookmookleviyhook' => 'bot/webhook',
                'GET /sl/<code:\w+>' => 'redirect/redirect',
                [
                    'verb' => 'GET',
                    'pattern' => '/sl/<code:\w+>',
                    'route' => 'redirect/redirect',
                    'suffix' => '/',
                ],
				//'<action:\w+>' => 'site/<action>',
			],
		],
    ],
];
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
	$web['bootstrap'][] = 'debug';
	$web['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		'allowedIPs' => ['*'],
	];

	$web['bootstrap'][] = 'gii';
	$web['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		'allowedIPs' => ['*'],
	];

	//$web['components']['urlManager']['rules']['gii'] = 'gii';
}

return \yii\helpers\ArrayHelper::merge($main, $web);
