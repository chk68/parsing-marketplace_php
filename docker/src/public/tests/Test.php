<?php

use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;


class DataTest extends TestCase
{
    public function testAddAd()
    {
        $dataManager = $this->getMockBuilder(DataManager::class)
            ->setMethods(['extractAdIdFromHtml', 'addAd'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataManager->method('extractAdIdFromHtml')->willReturn('123');

        // ДАННІ СЮДИ
        $dataManager->expects($this->once())
            ->method('addAd')
            ->with('https://www.olx.ua/api/v1/targeting/data/?page=ad&params%5Bad_id%5D=823063227', 'money.88887777@gmail.com')
            ->willReturnCallback(function () {
                echo "Added in 'ads_info'." . PHP_EOL;
            });

        // ДАННІ СЮДИ
        $dataManager->addAd('https://www.olx.ua/api/v1/targeting/data/?page=ad&params%5Bad_id%5D=823063227', 'money.88887777@gmail.com');

        // Добавляем утверждение
        $this->expectOutputString("Added in 'ads_info'." . PHP_EOL);
    }
}

class EmailTest extends TestCase  // емейл не спрацює, якщо update-price не працездатний.
{
    public function testSendNewAdNotification()
    {
        $mailerMock = $this->getMockBuilder(PHPMailer::class)
            ->onlyMethods(['send'])
            ->getMock();

        $mailerMock->expects($this->never())->method('send');

        $this->replaceFunction('createMailer', function () use ($mailerMock) {
            return $mailerMock;
        });

        // ДАННІ СЮДИ
        $result = sendNewAdNotification('money.88887777@gmail.com', '823063227');

        $this->assertTrue($result);
    }

    public function testSendPriceChangeNotification()
    {
        $mailerMock = $this->getMockBuilder(PHPMailer::class)
            ->onlyMethods(['send'])
            ->getMock();

        $mailerMock->expects($this->never())->method('send');

        $this->replaceFunction('createMailer', function () use ($mailerMock) {
            return $mailerMock;
        });

        // ДАННІ СЮДИ
        $result = sendPriceChangeNotification('money.88887777@gmail.com', '823063227', '1000', 'USD');

        $this->assertTrue($result);
    }

    protected function replaceFunction($functionName, $implementation)
    {
        $namespace = explode('\\', __NAMESPACE__);
        $namespace[count($namespace) - 1] = 'GlobalNamespace';
        $globalNamespace = implode('\\', $namespace);

        return $this->getMockBuilder($globalNamespace . '\\' . $functionName)
            ->setMethods(null)
            ->setNamespaceName($globalNamespace)
            ->setMockClassName('GlobalNamespace_' . $functionName)
            ->setMockClassName($functionName)
            ->setMockClassName($functionName . 'Mock')
            ->getMock();
    }
}