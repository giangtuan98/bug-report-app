<?php

return [
    'pdo' => [
        'driver' => 'mysql',
        'host' => '192.168.64.2',
        'port' => '3306',
        'db_name' => 'bug',
        'db_username' => 'giang',
        'db_user_password' => 'giang',
        'default_fetch' => PDO::FETCH_OBJ,
    ],
    'mysql' => [
        'driver' => 'mysql',
        'host' => '192.168.64.2',
        'port' => '3306',
        'db_name' => 'bug',
        'db_username' => 'giang',
        'db_user_password' => 'giang',
        'default_fetch' => MYSQLI_ASSOC,
    ],
];
