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
$result = mysqli_query($connect, "SELECT ad_id, ad_price, user_email FROM ads_info");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $adId = $row['ad_id'];
        $currentAdPrice = $row['ad_price'];
        $userEmail = $row['user_email'];
        if (isset($processedAds[$adId])) {
            continue; // Пропускаем, если уже обработано
        }
        $newAdPrice = getNewAdPrice($adId);
        if ($newAdPrice !== null) {
            handleAdPriceChange($connect, $adId, $currentAdPrice, $newAdPrice, $userEmail);
            $processedAds[$adId] = true;
        }
    }
    mysqli_close($connect);
}
function getNewAdPrice($adId)
{
    $apiUrl = "https://www.olx.ua/api/v1/targeting/data/?page=ad&params%5Bad_id%5D={$adId}";
    $apiResponse = file_get_contents($apiUrl);
    $adInfo = json_decode($apiResponse, true);
    return isset($adInfo["data"]["targeting"]["ad_id"]) ? $adInfo["data"]["targeting"]["ad_price"] : null;
}

function handleAdPriceChange($dbConnection, $adId, $currentAdPrice, $newAdPrice, $userEmail)
{
    if ($currentAdPrice != $newAdPrice) {
        $updateQuery = "UPDATE ads_info SET ad_price = '$newAdPrice' WHERE ad_id = '$adId'";
        mysqli_query($dbConnection, $updateQuery);
        sendPriceChangeNotification($userEmail, $adId, $newAdPrice);
    }
}
