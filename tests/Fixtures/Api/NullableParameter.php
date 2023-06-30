<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;
use Retrofit\Attribute\Header;
use Retrofit\Call;

interface NullableParameter
{
    #[GET('/users')]
    public function nullableParameter(#[Header('x-custom-header')] ?string $header): Call;
}
