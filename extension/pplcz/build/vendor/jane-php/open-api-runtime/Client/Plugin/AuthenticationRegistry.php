<?php

namespace PPLCZVendor\Jane\Component\OpenApiRuntime\Client\Plugin;

use PPLCZVendor\Http\Client\Common\Plugin;
use PPLCZVendor\Http\Promise\Promise;
use PPLCZVendor\Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;
use PPLCZVendor\Psr\Http\Message\RequestInterface;
class AuthenticationRegistry implements Plugin
{
    public const SCOPES_HEADER = 'X-Jane-Authentication';
    /** @var AuthenticationPlugin[] */
    private $authenticationPlugins;
    public function __construct(array $authenticationPlugins)
    {
        $this->authenticationPlugins = $authenticationPlugins;
    }
    public function handleRequest(RequestInterface $request, callable $next, callable $first) : Promise
    {
        $scopes = $request->getHeader(self::SCOPES_HEADER);
        foreach ($this->authenticationPlugins as $authenticationPlugin) {
            if (\in_array($authenticationPlugin->getScope(), $scopes)) {
                $request = $authenticationPlugin->authentication($request);
            }
        }
        // clean headers
        $request = $request->withoutHeader(self::SCOPES_HEADER);
        return $next($request);
    }
}
