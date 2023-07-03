<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\Body;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\POST;
use Retrofit\Attribute\Query;
use Retrofit\Attribute\QueryMap;
use Retrofit\Attribute\QueryName;
use Retrofit\Attribute\Url;
use Retrofit\Call;
use Retrofit\Tests\Fixtures\Model\UserRequest;

interface FullyValidApi
{
    #[GET('/info/{login}')]
    public function getInfo(#[Path('login')] string $login): Call;

    #[POST('/users/{login}')]
    public function createUser(#[Path('login')] string $login, #[Body] UserRequest $userRequest): Call;

    #[POST]
    public function onlyUrl(#[Url] ?string $url): Call;

    #[GET('/users/{login}')]
    public function pathAndQuery(#[Path('login')] string $login, #[Query('group')] string $group): Call;

    #[GET('/users')]
    public function addQueryName(#[QueryName(true)] string $queryName): Call;

    #[GET('/users')]
    public function addQueryMap(#[QueryMap] array $queries): Call;
}
