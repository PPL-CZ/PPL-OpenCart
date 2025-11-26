<?php

declare (strict_types=1);
namespace PPLCZVendor\Jane\Component\OpenApiRuntime\Client;

use PPLCZVendor\Psr\Http\Message\RequestInterface;
interface AuthenticationPlugin
{
    public function authentication(RequestInterface $request) : RequestInterface;
    public function getScope() : string;
}
