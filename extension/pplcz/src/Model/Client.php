<?php

namespace PPLCZ\Model;

class Client extends \PPLCZ\Model\Runtime\Client\Client
{
    public static function create($httpClient = null, array $additionalPlugins = array(), array $additionalNormalizers = array())
    {
        if (null === $httpClient) {
            $httpClient = \PPLCZVendor\Http\Discovery\Psr18ClientDiscovery::find();
            $plugins = array();
            if (count($additionalPlugins) > 0) {
                $plugins = array_merge($plugins, $additionalPlugins);
            }
            $httpClient = new \PPLCZVendor\Http\Client\Common\PluginClient($httpClient, $plugins);
        }
        $requestFactory = \PPLCZVendor\Http\Discovery\Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = \PPLCZVendor\Http\Discovery\Psr17FactoryDiscovery::findStreamFactory();
        $normalizers = array(new PPLCZVendor\Symfony\Component\Serializer\Normalizer\ArrayDenormalizer(), new \PPLCZ\Model\Normalizer\JaneObjectNormalizer());
        if (count($additionalNormalizers) > 0) {
            $normalizers = array_merge($normalizers, $additionalNormalizers);
        }
        $serializer = new \PPLCZVendor\Symfony\Component\Serializer\Serializer($normalizers, array(new \PPLCZVendor\Symfony\Component\Serializer\Encoder\JsonEncoder(new \PPLCZVendor\Symfony\Component\Serializer\Encoder\JsonEncode(), new \PPLCZVendor\Symfony\Component\Serializer\Encoder\JsonDecode(array('json_decode_associative' => true)))));
        return new static($httpClient, $requestFactory, $serializer, $streamFactory);
    }
}