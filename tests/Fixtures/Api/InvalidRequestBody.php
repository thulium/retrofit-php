<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;
use Retrofit\Attribute\Response\ResponseBody;
use Retrofit\Call;
use stdClass;

interface InvalidRequestBody
{
    #[GET('/users')]
    #[ResponseBody('string', stdClass::class)]
    public function notArrayTypeHasParametrizedType(): Call;
}
