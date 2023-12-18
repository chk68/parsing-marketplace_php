<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connect = mysqli_connect($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);
if (mysqli_connect_errno()) {
    printf("Ошибка подключения: %s\n", mysqli_connect_error());
    exit();
}
mysqli_query($connect, "SET NAMES utf8");

$result = mysqli_query($connect, "SELECT * FROM ads_info");

mysqli_close($connect);

include '../public/html/ads-list.html';