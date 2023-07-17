<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Core\Attribute\GET;

interface MethodWithoutReturnType
{
    #[GET('/users')]
    public function withoutReturnType();
}
