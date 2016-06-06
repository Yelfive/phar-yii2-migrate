<?php

if (file_exists(__DIR__ . '/db.php')) {
//    set_error_handler(function () {
//        echo "Database config cache is invalid\n";
//        echo "Clearing cache...\n";
//        unlink(__DIR__ . '/db.php');
//        echo "Done!\n";
//        echo "Please run command again.\n";
//        die;
//    }, E_ALL);
    $db = include __DIR__ . '/db.php';
//    restore_error_handler();
}

if (!isset($db) || !is_array($db)) {
    $db = [];
}

$config = [
    'id' => 'migrate',
    'basePath' => __APP__,
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        'console' => __APP__,
    ],
    'components' => [
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => 'console\controllers\MigrateController',
            'templateFile' => '@console/templates/create.php',
        ],
    ],
];

$db && $config['components']['db'] = $db;

return $config;