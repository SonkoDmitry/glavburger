<?php
file_exists(__DIR__ . '/../config/local.php') OR die('Local config file not exist');

$local = require(__DIR__ . '/../config/local.php');
if (is_array($local) && isset($local['params'])) {
	if (isset($local['params']['env']) && in_array($local['params']['env'], ['env', 'prod', 'test'])
		&& !defined('YII_ENV')) {
		define('YII_ENV', $local['params']['env']);
	}

	if (isset($local['params']['debug']) && in_array($local['params']['debug'], [true, false])
		&& !defined('YII_DEBUG')) {
		define('YII_DEBUG', $local['params']['debug']);
	}
}

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../ExtYii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
