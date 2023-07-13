<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\Body;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\POST;
use Retrofit\Attribute\Query;
use Retrofit\Attribute\Response\ResponseBody;
use Retrofit\Call;
use Retrofit\Tests\Fixtures\Model\UserRequest;

interface TypeResolverApi
{
    #[GET('/users')]
    #[ResponseBody('string')]
    public function scalarTypes(
        #[Query('bool')] bool $boolParam,
        #[Query('float')] float $floatParam,
        #[Query('int')] int $intParam,
        #[Query('mixed')] mixed $mixedParam,
        #[Query('string')] string $stringParam
    ): Call;

    #[GET('/users')]
    /**
     * @param bool[] $boolParams
     * @param array<float> $floatParams
     * @param int[] $intParams
     * @param array $mixedParams
     * @param string[] $stringParams
     */
    #[ResponseBody('string')]
    public function arrayOfScalarTypes(
        #[Query('bool')] array $boolParams,
        #[Query('float')] array $floatParams,
        #[Query('int')] array $intParams,
        #[Query('mixed')] array $mixedParams,
        #[Query('string')] array $stringParams
    ): Call;

    #[POST('/users')]
    #[ResponseBody('string')]
    public function genericClass(#[Body] object $userRequestParam): Call;

    #[POST('/users')]
    #[ResponseBody('string')]
    /** @param object[] $userRequestParam */
    public function arrayOfGenericClass(#[Body] array $userRequestParam): Call;

    #[POST('/users')]
    #[ResponseBody('string')]
    public function customClass(#[Body] UserRequest $userRequestParam): Call;

    #[POST('/users')]
    #[ResponseBody('string')]
    /** @param UserRequest[] $userRequestParams */
    public function arrayOfCustomClass(#[Body] array $userRequestParams): Call;
}
