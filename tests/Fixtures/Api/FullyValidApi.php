<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\Body;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\POST;
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
    public function multipleUrl(#[Url] ?string $url): Call;
}
