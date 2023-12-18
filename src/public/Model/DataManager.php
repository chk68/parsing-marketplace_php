<?php
require_once __DIR__ . "/../Service/MailerService.php";
require_once __DIR__ . "/../Controller/NewAdNotificationController.php";
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
            currency VARCHAR(10) NOT NULL,
            user_email VARCHAR(255) NOT NULL,
            confirmation_code VARCHAR(255),
            confirmed BOOLEAN DEFAULT false,
            subscription_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        mysqli_query($this->connection, $createTableQuery);
    }

    public function insertAdInfo($adId, $adUrl, $adPrice, $currency, $userEmail)
    {
        $confirmationCode = $this->generateConfirmationCode();

        $lastInsertedId = $this->getLastInsertedId();
        $userConfirmed = $this->checkUserConfirmationStatusById($lastInsertedId);

        if ($userConfirmed) {
            $insertQuery = "INSERT INTO ads_info (ad_id, ad_url, ad_price, currency, user_email, confirmation_code, confirmed) VALUES (?, ?, ?, ?, ?, ?, true)";
        } else {
            $insertQuery = "INSERT INTO ads_info (ad_id, ad_url, ad_price, currency, user_email, confirmation_code) VALUES (?, ?, ?, ?, ?, ?)";
        }

        $statement = mysqli_prepare($this->connection, $insertQuery);

        if ($statement) {
            mysqli_stmt_bind_param($statement, 'isssss', $adId, $adUrl, $adPrice, $currency, $userEmail, $confirmationCode);
            $result = mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);

            if (!$result) {
                printf("Error: %s\n", mysqli_error($this->connection));
            }

            $this->sendConfirmationCodeByEmail($userEmail, $confirmationCode);

            return $this->getLastInsertedId();
        } else {
            printf("Error in preparing statement: %s\n", mysqli_error($this->connection));
            return null;
        }
    }



    public function checkConfirmationCodeById($userId, $enteredCode)
    {
        $checkQuery = "SELECT * FROM ads_info WHERE id = ? AND confirmation_code = ?";
        $checkStatement = mysqli_prepare($this->connection, $checkQuery);

        if ($checkStatement) {
            mysqli_stmt_bind_param($checkStatement, 'is', $userId, $enteredCode);
            mysqli_stmt_execute($checkStatement);
            $result = mysqli_stmt_get_result($checkStatement);
            mysqli_stmt_close($checkStatement);

            return mysqli_num_rows($result) > 0;
        } else {
            printf("Error in preparing statement: %s\n", mysqli_error($this->connection));
            return false;
        }
    }

    public function confirmUserEmailById($userId)
    {
        $updateQuery = "UPDATE ads_info SET confirmed = true WHERE id = ?";
        $updateStatement = mysqli_prepare($this->connection, $updateQuery);

        if ($updateStatement) {
            mysqli_stmt_bind_param($updateStatement, 'i', $userId);
            mysqli_stmt_execute($updateStatement);
            mysqli_stmt_close($updateStatement);

            $this->sendNotificationByEmail($userId);
        } else {
            printf("Error in preparing statement: %s\n", mysqli_error($this->connection));
        }
    }

    private function getAdInfoById($userId)
    {
        $selectQuery = "SELECT ad_id, user_email FROM ads_info WHERE id = ?";
        $selectStatement = mysqli_prepare($this->connection, $selectQuery);

        if ($selectStatement) {
            mysqli_stmt_bind_param($selectStatement, 'i', $userId);
            mysqli_stmt_execute($selectStatement);
            $result = mysqli_stmt_get_result($selectStatement);
            mysqli_stmt_close($selectStatement);

            return mysqli_fetch_assoc($result);
        } else {
            printf("Error in preparing statement: %s\n", mysqli_error($this->connection));
            return null;
        }
    }

    public function getLastInsertedId()
    {
        return mysqli_insert_id($this->connection);
    }

    public function checkUserConfirmationStatusById($userId)
    {
        $checkQuery = "SELECT confirmed FROM ads_info WHERE id = ?";
        $checkStatement = mysqli_prepare($this->connection, $checkQuery);

        if ($checkStatement) {
            mysqli_stmt_bind_param($checkStatement, 'i', $userId);
            mysqli_stmt_execute($checkStatement);
            $result = mysqli_stmt_get_result($checkStatement);
            mysqli_stmt_close($checkStatement);

            if ($row = mysqli_fetch_assoc($result)) {
                return (bool) $row['confirmed'];
            }
        } else {
            printf("Error in preparing statement: %s\n", mysqli_error($this->connection));
        }

        return false;
    }



    private function sendConfirmationCodeByEmail($userEmail, $confirmationCode)
    {
        $mailer = new MailerService();
        $mailer->sendConfirmationCode($userEmail, $confirmationCode);
    }
    private function sendNotificationByEmail($userId)
    {
        $adInfo = $this->getAdInfoById($userId);

        if ($adInfo) {
            $newAdController = new NewAdNotificationController();
            $newAdController->sendNotification($adInfo['user_email'], $adInfo['ad_id']);
        }
    }
    private function generateConfirmationCode()
    {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $confirmationCode = '';
        $codeLength = 3;

        for ($i = 0; $i < $codeLength; $i++) {
            $confirmationCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $confirmationCode;
    }

    public function closeConnection()
    {
        mysqli_close($this->connection);
    }
}
