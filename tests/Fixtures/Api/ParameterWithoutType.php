<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Call;

interface ParameterWithoutType
{
    #[GET('/users/{login}')]
    public function parameterWithoutType(#[Path('login')] $login): Call;
}
