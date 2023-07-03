<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;
use Retrofit\Attribute\QueryMap;

interface QueryMapAttribute
{
    #[GET('/users')]
    public function nullableParameter(#[QueryMap] ?array $queryMap);
}
