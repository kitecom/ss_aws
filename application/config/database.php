<?php

return [
    'default-connection' => 'concrete',
    'connections' => [
        'concrete' => [
            'driver' => 'c5_pdo_mysql',
            'server' => 'localhost',
            'database' => 'ss_aws',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
    ],
];
