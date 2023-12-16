<?php

require_once "vendor/autoload.php";
require_once 'send-mail.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class DataManager
{
    private $connection;

    public function __construct($host, $user, $password, $database)
    {
        $this->connection = mysqli_connect($host, $user, $password, $database);

        if (mysqli_connect_errno()) {
            printf("Connection error: %s\n", mysqli_connect_error());
            exit();
        }

        mysqli_query($this->connection, "SET NAMES utf8");
        $this->createAdsInfoTable();
    }

    private function createAdsInfoTable()
    {
        $createTableQuery = "CREATE TABLE IF NOT EXISTS ads_info (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ad_id INT NOT NULL,
            ad_url VARCHAR(255) NOT NULL,
            ad_price INT NOT NULL,
            user_email VARCHAR(255) NOT NULL
        )";

        mysqli_query($this->connection, $createTableQuery);
    }

    public function addAd($adId, $userEmail)
    {
        $apiUrl = "https://www.olx.ua/api/v1/targeting/data/?page=ad&params%5Bad_id%5D={$adId}";
        $apiResponse = file_get_contents($apiUrl);
        $adInfo = json_decode($apiResponse, true);

        if (isset($adInfo["data"]["targeting"]["ad_id"])) {
            $adUrl = $adInfo["data"]["targeting"]["ad_url"];
            $adPrice = $adInfo["data"]["targeting"]["ad_price"];

            $insertQuery = "INSERT INTO ads_info (ad_id, ad_url, ad_price, user_email) VALUES (?, ?, ?, ?)";
            $statement = mysqli_prepare($this->connection, $insertQuery);

            if ($statement) {
                mysqli_stmt_bind_param($statement, 'isss', $adId, $adUrl, $adPrice, $userEmail);
                $result = mysqli_stmt_execute($statement);
                mysqli_stmt_close($statement);

                if ($result) {
                    echo "Added in 'ads_info'.\n";
                    sendNewAdNotification($userEmail, $adId);
                } else {
                    printf("Error: %s\n", mysqli_error($this->connection));
                }
            } else {
                printf("Error in preparing statement: %s\n", mysqli_error($this->connection));
            }
        } else {
            echo "No info.\n";
        }
    }

    public function closeConnection()
    {
        mysqli_close($this->connection);
    }
}

$dataManager = new DataManager($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $adId = $_POST["id"];
    $userEmail = $_POST["email"];

    if ($adId && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $dataManager->addAd($adId, $userEmail);
    } else {
        echo "Push ID in form or enter a valid email.\n";
    }
}

$dataManager->closeConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OlX ads</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        form {
            text-align: center;
            max-width: 300px;
            margin: auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #007BFF;
        }
    </style>
</head>
<body>
<form method="post">
    <label for="id">ID ad:</label>
    <input type="text" id="id" name="id" required>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <button type="submit" name="submit">Subscribe</button>
    <a href="ads-list.php">Ads list</a>
</form>
</body>
</html>