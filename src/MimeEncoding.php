<?php
declare(strict_types=1);

namespace Retrofit;

enum MimeEncoding: string
{
    case BIT_7 = '7bit';
    case BIT_8 = '8bit';
    case BASE_64 = 'base64';
    case BINARY = 'binary';
}
