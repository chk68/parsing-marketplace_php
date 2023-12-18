<?php

require_once "IController.php";
class Controller implements IController
{
    private $service;

    public function __construct(IService $service)
    {
        $this->service = $service;
    }

    public function processAdForm()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $adUrl = $_POST["url"];
            $userEmail = $_POST["email"];

            if ($adUrl && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                $this->service->addAd($adUrl, $userEmail);
            } else {
                echo "Enter a valid ad URL and email.\n";
            }
        }
    }
}