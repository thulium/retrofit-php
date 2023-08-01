<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal;

/**
 * @internal
 */
enum Encoding
{
    case FORM_URL_ENCODED;
    case MULTIPART;
}
