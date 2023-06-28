<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;

interface SampleApi
{
    #[GET('/info/{login}')]
    public function getInfo(#[Path('login')] string $login);
}
