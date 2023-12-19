<?php

require_once "../vendor/autoload.php";

use MyApp\Model\DataManager;

$message = "";
$homeLink = "<a href='/home'>Back to Home</a>";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $userId = isset($_GET['id']) ? $_GET['id'] : '';
    include 'Html/confirm-email.html';
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dataManager = new DataManager($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);

    $enteredCode = $_POST["confirmation_code"];
    $userId = $_POST["user_id"];

    $isValidCode = $dataManager->checkConfirmationCodeById($userId, $enteredCode);

    if ($isValidCode) {
        $dataManager->confirmUserEmailById($userId);
        $message = "Email successfully confirmed!";
    } else {
        $message = "Invalid confirmation code. Please try again.";
    }

    include 'Html/confirmation-result.html';
}
