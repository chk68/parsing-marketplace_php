<?php

namespace MyApp\DTO;

class ResponseDTO
{
    private bool $success = true;
    private $data;
    private array $error = [];
    private int $status = 200;

    public function setSuccess(bool $success = true):ResponseDTO {
        $this->success = $success;
        return $this;
    }

    public function setData($data):ResponseDTO {
        $this->data = $data;
        return $this;
    }

    public function setStatus(int $status): ResponseDTO {
        $this->status = $status;
        return $this;
    }

    public function setError($error): ResponseDTO {
        $this->error[] = $error;
        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}