<?php

class UpdatePriceService
{
    public function getAdInfo($adId)
    {
        $apiUrl = "https://www.olx.ua/api/v1/targeting/data/?page=ad&params%5Bad_id%5D={$adId}";
        $apiResponse = file_get_contents($apiUrl);
        $adInfo = json_decode($apiResponse, true);

        return isset($adInfo["data"]["targeting"]["ad_id"]) ? [
            'ad_price' => $adInfo["data"]["targeting"]["ad_price"],
            'currency' => $adInfo["data"]["targeting"]["currency"],
        ] : null;
    }
}
