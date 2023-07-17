<?php

declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\Path;
use Retrofit\Core\Call;

interface ParameterWithoutType
{
    #[GET('/users/{login}')]
    public function parameterWithoutType(#[Path('login')] $login): Call;
}
