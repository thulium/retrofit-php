<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal;

use Retrofit\Core\Call;

interface ServiceMethod
{
    public function invoke(array $args): Call;
}
