<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
define('__APP__', __DIR__);

include __DIR__ . '/yii2/Yii.php';
include __APP__ . '/Config.php';

$config = include __DIR__ . '/config/main.php';

new Config($config);

$application = new yii\console\Application($config);

$exitCode = $application->run();

exit($exitCode);
