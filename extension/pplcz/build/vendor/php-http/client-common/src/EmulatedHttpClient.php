<?php

declare (strict_types=1);
namespace PPLCZVendor\Http\Client\Common;

use PPLCZVendor\Http\Client\HttpAsyncClient;
use PPLCZVendor\Http\Client\HttpClient;
/**
 * Emulates a synchronous HTTP client with the help of an asynchronous client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class EmulatedHttpClient implements HttpClient, HttpAsyncClient
{
    use HttpAsyncClientDecorator;
    use HttpClientEmulator;
    public function __construct(HttpAsyncClient $httpAsyncClient)
    {
        $this->httpAsyncClient = $httpAsyncClient;
    }
}
