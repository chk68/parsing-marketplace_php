<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use MyApp\Controller\PriceChangeController;
use MyApp\Helpers\UpdatePriceService;
use MyApp\Model\DataManager;
use MyApp\Repositories\AdRepository;


$dataManager = new DataManager($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);

$processedAds = [];
$adService = new AdRepository($dataManager);

$ads = $adService->getAllAds();

foreach ($ads as $ad) {
    $adId = $ad['ad_id'];
    $currentAdPrice = $ad['ad_price'];
    $currentCurrency = $ad['currency'];
    $userEmail = $ad['user_email'];

    if (isset($processedAds[$adId])) {
        continue;
    }

    $olxApiService = UpdatePriceService::getAdInfo($adId);

    if ($olxApiService !== null) {
        $newAdPrice = $olxApiService['ad_price'];
        $newCurrency = $olxApiService['currency'];

        if ($newAdPrice != $currentAdPrice) {
            $adService->updateAdPrice($adId, $newAdPrice, $newCurrency);
            PriceChangeController::sendNotification($userEmail, $adId, $newAdPrice, $newCurrency);
        } /*else {
            PriceChangeController::sendThankYou($userEmail, $adId);
        }*/

        $processedAds[$adId] = true;
    }
}
