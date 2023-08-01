<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal;

use Retrofit\Core\Call;

/**
 * @internal
 */
interface ServiceMethod
{
    /** @param list<mixed> $args */
    public function invoke(array $args): Call;
}
