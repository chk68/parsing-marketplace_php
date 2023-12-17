<?php

require_once 'send-mail.php';
require_once "vendor/autoload.php";

$connect = mysqli_connect($_ENV["MYSQL_HOST"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"], $_ENV["MYSQL_DATABASE"]);
if (mysqli_connect_errno()) {
    printf("Ошибка подключения: %s\n", mysqli_connect_error());
    exit();
}

mysqli_query($connect, "SET NAMES utf8");
$processedAds = [];
$result = mysqli_query($connect, "SELECT ad_id, ad_price, currency, user_email FROM ads_info");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $adId = $row['ad_id'];
        $currentAdPrice = $row['ad_price'];
        $currentCurrency = $row['currency'];
        $userEmail = $row['user_email'];
        if (isset($processedAds[$adId])) {
            continue; // Пропускаем, если уже обработано
        }
        $adInfo = getAdInfo($adId);
        if ($adInfo !== null) {
            $newAdPrice = $adInfo['ad_price'];
            $newCurrency = $adInfo['currency'];
            handleAdPriceChange($connect, $adId, $currentAdPrice, $newAdPrice, $currentCurrency, $newCurrency, $userEmail);
            $processedAds[$adId] = true;
        }
    }
    mysqli_close($connect);
}

function getAdInfo($adId)
{
    $apiUrl = "https://www.olx.ua/api/v1/targeting/data/?page=ad&params%5Bad_id%5D={$adId}";
    $apiResponse = file_get_contents($apiUrl);
    $adInfo = json_decode($apiResponse, true);
    return isset($adInfo["data"]["targeting"]["ad_id"]) ? [
        'ad_price' => $adInfo["data"]["targeting"]["ad_price"],
        'currency' => $adInfo["data"]["targeting"]["currency"],
    ] : null;
}

function handleAdPriceChange($dbConnection, $adId, $currentAdPrice, $newAdPrice, $currentCurrency, $newCurrency, $userEmail)
{
    if ($currentAdPrice != $newAdPrice || $currentCurrency != $newCurrency) {
        $updateQuery = "UPDATE ads_info SET ad_price = '$newAdPrice', currency = '$newCurrency' WHERE ad_id = '$adId'";
        mysqli_query($dbConnection, $updateQuery);
        sendPriceChangeNotification($userEmail, $adId, $newAdPrice, $newCurrency);
    }
}
