<?php

namespace MyApp\Repositories;

use DOMDocument;
use DOMXPath;
use MyApp\DTO\ResponseDTO;
use MyApp\Model\DataManager;
use MyApp\Service\NewAdNotificationService;

class AdRepository implements InterfaceRepository
{
    private DataManager $dataManager;

    public function __construct($dataManager)
    {
        $this->dataManager = $dataManager;
    }

    public function getAllAds(): array
    {
        return $this->dataManager->getAllAds();
    }

    public function updateAdPrice($adId, $newAdPrice, $newCurrency)
    {
        $this->dataManager->updateAdPrice($adId, $newAdPrice, $newCurrency);
    }

    public function addAd($adUrl, $userEmail)
    {
        $responseDTO = new ResponseDTO();
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
                    $newAdService = new NewAdNotificationService();
                    $sendMail = $newAdService->sendNotification($userEmail, $adId);
                    $responseDTO->setSuccess($sendMail);
                    if ($sendMail) {
                        $responseDTO->setData('Mail send')
                            ->setStatus(200);
                    } else {
                        $responseDTO->setError('Mail not sending')
                            ->setStatus(404);
                    }
                } else {
                    header("Location: confirm-email.php?id=" . urlencode($lastInsertedId));
                    exit();
                }
            } else {
                return $responseDTO->setError("No info.")
                    ->setStatus(400);
            }
        } else {
            return $responseDTO->setError("Failed to extract adId from URL.")
                ->setError("HTML content: " . file_get_contents($adUrl))
                ->setStatus(400);
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
