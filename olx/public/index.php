<?php

require_once "../vendor/autoload.php";

use MyApp\Controller\Controller;
use MyApp\Model\DataManager;
use \MyApp\Repositories\AdRepository;

#TODO видалити помилки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dataManager = new DataManager($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);
$service = new AdRepository($dataManager);
$controller = new Controller($service);

$controller->processAdForm();

