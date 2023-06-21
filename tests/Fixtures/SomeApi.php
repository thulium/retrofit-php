<?php

namespace Retrofit\Tests\Fixtures;

use Retrofit\Attribute\GET;

interface SomeApi
{
    #[GET('/users')]
    public function getUsers();
}
