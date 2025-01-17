<?php

$db_host = env('DB_HOST');
$db_name = env('DB_NAME');
$db_port = env('DB_PORT');
$dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name";

return [
    'class' => 'yii\db\Connection',
    'dsn' => $dsn,
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
