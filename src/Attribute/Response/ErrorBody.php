<?php
declare(strict_types=1);

namespace Retrofit\Attribute\Response;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class ErrorBody
{
    use WithBody;
}
