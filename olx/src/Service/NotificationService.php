<?php
namespace MyApp\Service;

use MyApp\Model\DataManager;

class NotificationService
{
    private $mailerService;
    private $newAdNotificationService;
    private $dataManager;

    public function __construct(DataManager $dataManager)
    {
        $this->mailerService = new MailerService();
        // TODO
        $this->newAdNotificationService = new NewAdNotificationService();
        $this->dataManager = $dataManager;
    }

    public function sendConfirmationCodeByEmail($userEmail, $confirmationCode)
    {
        $this->mailerService->sendConfirmationCode($userEmail, $confirmationCode);
    }

    public function sendNotificationByEmail($userId)
    {
        $adInfo = $this->dataManager->getAdInfoById($userId);

        if ($adInfo) {
            $this->newAdNotificationService->sendNotification($adInfo['user_email'], $adInfo['ad_id']);
        }
    }
}