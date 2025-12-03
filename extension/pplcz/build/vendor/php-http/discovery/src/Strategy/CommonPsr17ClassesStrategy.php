<?php

namespace PPLCZVendor\Http\Discovery\Strategy;

use PPLCZVendor\Psr\Http\Message\RequestFactoryInterface;
use PPLCZVendor\Psr\Http\Message\ResponseFactoryInterface;
use PPLCZVendor\Psr\Http\Message\ServerRequestFactoryInterface;
use PPLCZVendor\Psr\Http\Message\StreamFactoryInterface;
use PPLCZVendor\Psr\Http\Message\UploadedFileFactoryInterface;
use PPLCZVendor\Psr\Http\Message\UriFactoryInterface;
/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * Don't miss updating src/Composer/Plugin.php when adding a new supported class.
 */
final class CommonPsr17ClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [RequestFactoryInterface::class => ['PPLCZVendor\\Phalcon\\Http\\Message\\RequestFactory', 'PPLCZVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLCZVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLCZVendor\\Http\\Factory\\Diactoros\\RequestFactory', 'PPLCZVendor\\Http\\Factory\\Guzzle\\RequestFactory', 'PPLCZVendor\\Http\\Factory\\Slim\\RequestFactory', 'PPLCZVendor\\Laminas\\Diactoros\\RequestFactory', 'PPLCZVendor\\Slim\\Psr7\\Factory\\RequestFactory', 'PPLCZVendor\\HttpSoft\\Message\\RequestFactory'], ResponseFactoryInterface::class => ['PPLCZVendor\\Phalcon\\Http\\Message\\ResponseFactory', 'PPLCZVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLCZVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLCZVendor\\Http\\Factory\\Diactoros\\ResponseFactory', 'PPLCZVendor\\Http\\Factory\\Guzzle\\ResponseFactory', 'PPLCZVendor\\Http\\Factory\\Slim\\ResponseFactory', 'PPLCZVendor\\Laminas\\Diactoros\\ResponseFactory', 'PPLCZVendor\\Slim\\Psr7\\Factory\\ResponseFactory', 'PPLCZVendor\\HttpSoft\\Message\\ResponseFactory'], ServerRequestFactoryInterface::class => ['PPLCZVendor\\Phalcon\\Http\\Message\\ServerRequestFactory', 'PPLCZVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLCZVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLCZVendor\\Http\\Factory\\Diactoros\\ServerRequestFactory', 'PPLCZVendor\\Http\\Factory\\Guzzle\\ServerRequestFactory', 'PPLCZVendor\\Http\\Factory\\Slim\\ServerRequestFactory', 'PPLCZVendor\\Laminas\\Diactoros\\ServerRequestFactory', 'PPLCZVendor\\Slim\\Psr7\\Factory\\ServerRequestFactory', 'PPLCZVendor\\HttpSoft\\Message\\ServerRequestFactory'], StreamFactoryInterface::class => ['PPLCZVendor\\Phalcon\\Http\\Message\\StreamFactory', 'PPLCZVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLCZVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLCZVendor\\Http\\Factory\\Diactoros\\StreamFactory', 'PPLCZVendor\\Http\\Factory\\Guzzle\\StreamFactory', 'PPLCZVendor\\Http\\Factory\\Slim\\StreamFactory', 'PPLCZVendor\\Laminas\\Diactoros\\StreamFactory', 'PPLCZVendor\\Slim\\Psr7\\Factory\\StreamFactory', 'PPLCZVendor\\HttpSoft\\Message\\StreamFactory'], UploadedFileFactoryInterface::class => ['PPLCZVendor\\Phalcon\\Http\\Message\\UploadedFileFactory', 'PPLCZVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLCZVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLCZVendor\\Http\\Factory\\Diactoros\\UploadedFileFactory', 'PPLCZVendor\\Http\\Factory\\Guzzle\\UploadedFileFactory', 'PPLCZVendor\\Http\\Factory\\Slim\\UploadedFileFactory', 'PPLCZVendor\\Laminas\\Diactoros\\UploadedFileFactory', 'PPLCZVendor\\Slim\\Psr7\\Factory\\UploadedFileFactory', 'PPLCZVendor\\HttpSoft\\Message\\UploadedFileFactory'], UriFactoryInterface::class => ['PPLCZVendor\\Phalcon\\Http\\Message\\UriFactory', 'PPLCZVendor\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLCZVendor\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLCZVendor\\Http\\Factory\\Diactoros\\UriFactory', 'PPLCZVendor\\Http\\Factory\\Guzzle\\UriFactory', 'PPLCZVendor\\Http\\Factory\\Slim\\UriFactory', 'PPLCZVendor\\Laminas\\Diactoros\\UriFactory', 'PPLCZVendor\\Slim\\Psr7\\Factory\\UriFactory', 'PPLCZVendor\\HttpSoft\\Message\\UriFactory']];
    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }
        return $candidates;
    }
}
