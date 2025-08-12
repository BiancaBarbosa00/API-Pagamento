<?php

namespace App\DB;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database {
    public static function start() {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => $_ENV["DB_DRIVER"],
            'host' => $_ENV["DB_HOST"],
            'database' => $_ENV["DB_DBNAME"],
            'username' => $_ENV["DB_USERNAME"],
            'password' => $_ENV["DB_PASSWORD"],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}