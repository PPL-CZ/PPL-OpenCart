<?php

declare (strict_types=1);
namespace PPLCZVendor\Http\Client\Common\Exception;

use PPLCZVendor\Http\Client\Exception\RequestException;
/**
 * Thrown when the Plugin Client detects an endless loop.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class LoopException extends RequestException
{
}
