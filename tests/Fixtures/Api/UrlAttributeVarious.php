<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\Query;
use Retrofit\Attribute\Url;
use Retrofit\Call;

interface UrlAttributeVarious
{
    #[GET]
    public function methodWhenPathIsBeforeUrl(#[Path('login')] string $login, #[Url] string $url): Call;

    #[GET]
    public function methodWithQuery(#[Query('group')] string $group, #[Url] string $url): Call;
}
