<?php
namespace MyApp\Controller;

use MyApp\Lib\Request;
use MyApp\Lib\Response;
use MyApp\Repositories\InterfaceRepository;

class Controller implements IController
{
    private InterfaceRepository $service;
    private Request $request;
    private Response $response;

    public function __construct(InterfaceRepository $service)
    {
        $this->service = $service;
        $this->request = new Request();
        $this->response = new Response();
    }

    public function processAdForm()
    {
        if ($this->request->isPost()) {
            $validate = $this->request->validate([
                'email' => FILTER_VALIDATE_EMAIL,
                'url' => FILTER_VALIDATE_URL
            ]);

            if ($validate) {
                $responseDTO = $this->service->addAd(
                    $this->request->getBodyParam('url'),
                    $this->request->getBodyParam('email')
                );
                $this->response
                    ->setDTO($responseDTO)
                    ->send();
            } else {
                $this->response
                    ->setStatus(400)
                    ->setError($this->request->getValidError())
                    ->send();
            }
        } else {
            include '../public/Html/index.html';
        }
    }
}