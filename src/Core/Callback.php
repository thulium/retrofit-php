<?php

declare(strict_types=1);

namespace Retrofit\Core;

use Throwable;

interface Callback
{
    public function onResponse(Call $call, Response $response): void;

    public function onFailure(Call $call, Throwable $t): void;
}
