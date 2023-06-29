<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures;

use Retrofit\Attribute\Body;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\POST;
use Retrofit\Attribute\Url;
use Retrofit\Call;
use Retrofit\Tests\Fixtures\Model\UserRequest;

interface SomeApi
{
    #[GET('/users')]
    public function getUsers(): Call;

    #[GET('/users/{id}')]
    public function getUser(#[Path('id')] int $id): Call;

    #[GET('/users/{name}')]
    public function getUserByName(#[Path(name: 'name', encoded: true)] string $name): Call;

    #[POST('/users')]
    public function createUser(#[Body] UserRequest $userRequest): Call;

    #[POST]
    public function multipleUrl(#[Url] string $url): Call;
}
