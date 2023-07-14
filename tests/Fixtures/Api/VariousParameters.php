<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\Query;
use Retrofit\Attribute\Response\ResponseBody;
use Retrofit\Call;

interface VariousParameters
{
    #[GET('/users/{id}')]
    #[ResponseBody('void')]
    public function defaultValue(#[Path('id')] int $id = 100): Call;

    #[GET('/users/{id}')]
    #[ResponseBody('void')]
    public function passedByReference(#[Path('id')] int &$id): Call;

    #[GET('/users')]
    #[ResponseBody('void')]
    public function variadic(#[Query('ids')] int...$ids): Call;
}
