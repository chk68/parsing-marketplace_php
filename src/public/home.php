<?php

require_once "../vendor/autoload.php";
/*require_once 'send-mail.php';*/
require_once "../Model/DataManager.php";
require_once "../Service/Service.php";
require_once "../Controller/Controller.php";

#TODO видалити помилки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dataManager = new DataManager($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);
$service = new Service($dataManager);
$controller = new Controller($service);

$controller->processAdForm();

$dataManager->closeConnection();
include '../public/Html/index.html';