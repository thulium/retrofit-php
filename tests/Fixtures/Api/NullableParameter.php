<?php

declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\Header;
use Retrofit\Core\Call;

interface NullableParameter
{
    #[GET('/users')]
    public function nullableParameter(#[Header('x-custom-header')] ?string $header): Call;
}
