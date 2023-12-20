<?php

namespace MyApp\Model;

use MyApp\Helpers\ConfirmationCode;
use MyApp\Service\NotificationService;
use PDO;
use PDOException;


class DataManager
{
    private NotificationService $notificationService;
    protected PDO $pdo;

    public function __construct($host, $user, $password, $database)
    {
        try {
            $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, $user, $password, $options);
            $this->createAdsInfoTable();
            $this->notificationService = new NotificationService($this);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getAllAds(): array
    {
        $query = "SELECT ad_id, ad_price, currency, user_email FROM ads_info";
        $statement = $this->pdo->query($query);
        $ads = [];

        if ($statement) {
            $ads = $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        return $ads;
    }

    public function updateAdPrice($adId, $newAdPrice, $newCurrency)
    {
        $query = "UPDATE ads_info SET ad_price = ?, currency = ? WHERE ad_id = ?";
        $statement = $this->pdo->prepare($query);

        if ($statement) {
            $statement->execute([$newAdPrice, $newCurrency, $adId]);
        } else {
            die("Error in preparing statement: " . implode(" ", $this->pdo->errorInfo()));
        }
    }

    private function createAdsInfoTable()
    {
        $query = "
        CREATE TABLE IF NOT EXISTS ads_info (
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

        try {
            $this->pdo->exec($query);
        } catch (PDOException $e) {
            die("Error creating table: " . $e->getMessage());
        }
    }

    public function insertAdInfo($adId, $adUrl, $adPrice, $currency, $userEmail)
    {
        $confirmationCode = $this->generateConfirmationCode();
        $lastInsertedId = $this->getLastInsertedId();
        $userConfirmed = $this->checkUserConfirmationStatusById($lastInsertedId);

        if ($userConfirmed) {
            $query = "INSERT INTO ads_info (ad_id, ad_url, ad_price, currency, user_email, confirmation_code, confirmed) VALUES (?, ?, ?, ?, ?, ?, true)";
        } else {
            $query = "INSERT INTO ads_info (ad_id, ad_url, ad_price, currency, user_email, confirmation_code) VALUES (?, ?, ?, ?, ?, ?)";
        }

        $statement = $this->pdo->prepare($query);

        if ($statement) {
            $params = [$adId, $adUrl, $adPrice, $currency, $userEmail, $confirmationCode];

            if ($userConfirmed) {
                $params[] = $userConfirmed;
            }

            $result = $statement->execute($params);

            if (!$result) {
                die("Error: " . implode(" ", $statement->errorInfo()));
            }

            $this->sendConfirmationCodeByEmail($userEmail, $confirmationCode);

            return $this->getLastInsertedId();
        } else {
            die("Error in preparing statement: " . implode(" ", $this->pdo->errorInfo()));
            return null;
        }
    }

    public function checkConfirmationCodeById($userId, $enteredCode): bool
    {
        $query = "SELECT * FROM ads_info WHERE id = ? AND confirmation_code = ?";
        $statement = $this->pdo->prepare($query);

        if ($statement) {
            $statement->execute([$userId, $enteredCode]);
            return $statement->rowCount() > 0;
        } else {
            die("Error in preparing statement: " . implode(" ", $this->pdo->errorInfo()));
            return false;
        }
    }

    public function confirmUserEmailById($userId)
    {
        $query = "UPDATE ads_info SET confirmed = true WHERE id = ?";
        $statement = $this->pdo->prepare($query);

        if ($statement) {
            $statement->execute([$userId]);
            $this->sendNotificationByEmail($userId);
        } else {
            die("Error in preparing statement: " . implode(" ", $this->pdo->errorInfo()));
        }
    }

    public function getAdInfoById($userId)
    {
        $query = "SELECT ad_id, user_email FROM ads_info WHERE id = ?";
        $statement = $this->pdo->prepare($query);

        if ($statement) {
            $statement->execute([$userId]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        } else {
            die("Error in preparing statement: " . implode(" ", $this->pdo->errorInfo()));
            return null;
        }
    }

    public function checkUserConfirmationStatusById($userId): bool
    {
        $query = "SELECT confirmed FROM ads_info WHERE id = ?";
        $statement = $this->pdo->prepare($query);

        if ($statement) {
            $statement->execute([$userId]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return (bool)$row['confirmed'];
            }
        } else {
            die("Error in preparing statement: " . implode(" ", $this->pdo->errorInfo()));
        }

        return false;
    }

    public function getLastInsertedId()
    {
        return $this->pdo->lastInsertId();
    }

    public function sendConfirmationCodeByEmail($userEmail, $confirmationCode)
    {
        $this->notificationService->sendConfirmationCodeByEmail($userEmail, $confirmationCode);
    }

    public function sendNotificationByEmail($userId)
    {
        $this->notificationService->sendNotificationByEmail($userId);
    }

    public function generateConfirmationCode(): string
    {
        return ConfirmationCode::generate();
    }

}
