<?php

namespace MyApp\Helpers;

class ConfirmationCode
{

    public static function generate(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $confirmationCode = '';
        $codeLength = 3;

        for ($i = 0; $i < $codeLength; $i++) {
            $confirmationCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $confirmationCode;
    }
}