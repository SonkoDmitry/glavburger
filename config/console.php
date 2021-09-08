<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$main = require(__DIR__ . '/main.php');

$console = [
	'id' => 'bot.glavburger-console',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log', 'gii'],
	'controllerNamespace' => 'app\commands',
	'modules' => [
		'gii' => 'yii\gii\Module',
	],
	'components' => [
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
	],
];

return \yii\helpers\ArrayHelper::merge($main, $console);
