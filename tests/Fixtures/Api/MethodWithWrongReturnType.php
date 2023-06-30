<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;

interface MethodWithWrongReturnType
{
    #[GET('/users')]
    public function methodWithWrongReturnType(): int;
}
