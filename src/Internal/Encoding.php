<?php
declare(strict_types=1);

namespace Retrofit\Internal;

enum Encoding
{
    case FORM_URL_ENCODED;
    case MULTIPART;
}
