<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Retrofit\Call;

interface ServiceMethod
{
    public function invoke(array $args): Call;
}
