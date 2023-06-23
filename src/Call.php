<?php
declare(strict_types=1);

namespace Retrofit;

interface Call
{
    public function execute(): void;
}
