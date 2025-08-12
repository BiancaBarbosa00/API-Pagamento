<?php

require_once "../vendor/autoload.php";

use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use App\DB\Database;

Database::start();

require_once "../app.php";