<?php

require_once '../Service/AdService.php';
require_once '../Service/UpdatePriceService.php';
require_once '../Controller/PriceChangeController.php';
require_once '../vendor/autoload.php';

$connect = mysqli_connect($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);
if (mysqli_connect_errno()) {
    printf("Error: %s\n", mysqli_connect_error());
    exit();
}
mysqli_query($connect, "SET NAMES utf8");


$processedAds = [];
$adService = new AdService($connect);

$ads = $adService->getAllAds();

foreach ($ads as $ad) {
    $adId = $ad['ad_id'];
    $currentAdPrice = $ad['ad_price'];
    $currentCurrency = $ad['currency'];
    $userEmail = $ad['user_email'];

    if (isset($processedAds[$adId])) {
        continue;
    }

    $olxApiService = new UpdatePriceService();
    $adInfo = $olxApiService->getAdInfo($adId);

    if ($adInfo !== null) {
        $newAdPrice = $adInfo['ad_price'];
        $newCurrency = $adInfo['currency'];

        $adService->updateAdPrice($adId, $newAdPrice, $newCurrency);

        PriceChangeController::sendNotification($userEmail, $adId, $newAdPrice, $newCurrency);

        $processedAds[$adId] = true;
    }
}

mysqli_close($connect);
