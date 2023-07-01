<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\Path;
use Retrofit\Attribute\Url;

interface AllAttributeParametersInOneMethod
{
    public function allInOne(#[Url] string $url, #[Path('/users')] string $path);
}
