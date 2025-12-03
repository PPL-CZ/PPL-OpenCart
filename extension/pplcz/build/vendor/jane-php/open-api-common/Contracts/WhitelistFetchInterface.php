<?php

namespace PPLCZVendor\Jane\Component\OpenApiCommon\Contracts;

use PPLCZVendor\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLCZVendor\Jane\Component\OpenApiCommon\Registry\Registry;
interface WhitelistFetchInterface
{
    public function addOperationRelations(OperationGuess $operationGuess, Registry $registry);
}
