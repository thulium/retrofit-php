<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\Response\ResponseBody;
use Retrofit\Core\Call;
use stdClass;

interface InvalidRequestBody
{
    #[GET('/users')]
    #[ResponseBody('string', stdClass::class)]
    public function notArrayTypeHasParametrizedType(): Call;
}
