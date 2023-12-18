<?php

class AdService
{
    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getAllAds()
    {
        $result = mysqli_query($this->dbConnection, "SELECT ad_id, ad_price, currency, user_email FROM ads_info");
        $ads = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $ads[] = [
                    'ad_id' => $row['ad_id'],
                    'ad_price' => $row['ad_price'],
                    'currency' => $row['currency'],
                    'user_email' => $row['user_email'],
                ];
            }
        }

        return $ads;
    }

    public function updateAdPrice($adId, $newAdPrice, $newCurrency)
    {
        $updateQuery = "UPDATE ads_info SET ad_price = '$newAdPrice', currency = '$newCurrency' WHERE ad_id = '$adId'";
        mysqli_query($this->dbConnection, $updateQuery);
    }
}
