<?php
declare(strict_types=1);

namespace Retrofit;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface Callback
{
    public function onResponse(Call $call, ResponseInterface $response): void;

    public function onFailure(Call $call, Throwable $t): void;
}
