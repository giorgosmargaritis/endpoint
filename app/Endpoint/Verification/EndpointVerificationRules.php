<?php

namespace App\Endpoint\Verification;

abstract class EndpointVerificationRules
{
    public static function getInstance()
    {
        $verificationClass = 'App\Endpoint\Verification\EndpointVerificationRules';

        return new $verificationClass();
    }
}