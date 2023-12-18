<?php

require_once "IService.php";
require_once __DIR__ . "/../Controller/NewAdNotificationController.php";

class Service implements IService
{
    private $dataManager;

    public function __construct(DataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    public function addAd($adUrl, $userEmail)
    {
        $adId = $this->extractAdIdFromHtml(file_get_contents($adUrl));

        if ($adId) {
            $apiUrl = "https://www.olx.ua/api/v1/targeting/data/?page=ad&params%5Bad_id%5D={$adId}";
            $apiResponse = file_get_contents($apiUrl);
            $adInfo = json_decode($apiResponse, true);

            if (isset($adInfo["data"]["targeting"]["ad_id"])) {
                $adPrice = $adInfo["data"]["targeting"]["ad_price"];
                $currency = isset($adInfo["data"]["targeting"]["currency"]) ? $adInfo["data"]["targeting"]["currency"] : "UAH";

                $lastInsertedId = $this->dataManager->insertAdInfo($adId, $adUrl, $adPrice, $currency, $userEmail);

                $userConfirmed = $this->dataManager->checkUserConfirmationStatusById($lastInsertedId);

                if ($userConfirmed) {
                    $newAdController = new NewAdNotificationController();
                    $newAdController->sendNotification($userEmail, $adId);
                } else {
                    header("Location: confirm-email.php?id=" . urlencode($lastInsertedId));
                    exit();
                }
            } else {
                echo "No info.\n";
            }
        } else {
            echo "Failed to extract adId from URL.\n";
            echo "HTML content: " . file_get_contents($adUrl);
        }
    }

    private function extractAdIdFromHtml(string $html)
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $divElement = $xpath->query('//div[@class="css-cgp8kk"]');

        if ($divElement->length > 0) {
            $text = $divElement->item(0)->textContent;
            preg_match('/ID: (\d+)/', $text, $matches);
            if (!empty($matches[1])) {
                return $matches[1];
            }
        }
        return false;
    }
}
