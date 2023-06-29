<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\Body;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\POST;
use Retrofit\Call;
use Retrofit\Tests\Fixtures\Model\UserRequest;

interface ValidApi
{
    #[GET('/info/{login}')]
    public function getInfo(#[Path('login')] string $login): Call;

    #[POST('/users')]
    public function createUser(#[Body] UserRequest $userRequest): Call;
}
