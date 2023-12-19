<?php

namespace MyApp\Lib;

class Request
{
    private array $post;
    private array $get;
    private array $error = [];

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
    }

    public function isPost(): bool {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

    public function isGet(): bool {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    public function getQuery($name, $default = null)  {
        return (isset($this->get[$name])) ? $this->get[$name] : $default;
    }

    public function getBodyParam($name, $default = null)  {
        return (isset($this->post[$name])) ? $this->post[$name] : $default;
    }

    public function validate(array $rules): bool {
        $return = true;
        foreach ($rules as $name => $rule) {
            $value = filter_var($this->post[$name], $rule);
            if ($value === false) {
                $this->error[] = $name. ' is incorrect';
                $return = false;
            }
        }
        return $return;
    }

    public function getValidError():array {
        return $this->error;
    }
}