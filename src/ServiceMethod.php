<?php
declare(strict_types=1);

namespace Retrofit;

interface ServiceMethod
{
    /**
     * @param array<mixed> $args
     * @return mixed
     */
    public function invoke(array $args): mixed;
}
