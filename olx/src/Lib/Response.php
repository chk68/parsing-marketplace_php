<?php

namespace MyApp\Lib;

use MyApp\DTO\ResponseDTO;

class Response
{
    private bool $success = true;
    private $data;
    private $error;
    private int $status = 200;

    public function setSuccess(bool $success = true):Response {
        $this->success = $success;
        return $this;
    }

    public function setData($data):Response {
        $this->data = $data;
        return $this;
    }

    public function setStatus(int $status): Response {
        $this->status = $status;
        return $this;
    }

    public function setError($error): Response {
        $this->error = $error;
        return $this;
    }

    public function setDTO(ResponseDTO $responseDTO): Response {
        $this->success = $responseDTO->isSuccess();
        $this->data = $responseDTO->getData();
        $this->error = $responseDTO->getError();
        $this->status = $responseDTO->getStatus();
        return $this;
    }

    public function send(): void {
        http_response_code($this->status);
        header('Content-Type: application/json; charset=utf-8');
        $response = [
            'success' => $this->success,
        ];

        if ($this->success) {
            $response['data'] = $this->data;
        } else {
            $response['error'] = $this->error;
        }

        echo json_encode($response);

        exit;
    }
}