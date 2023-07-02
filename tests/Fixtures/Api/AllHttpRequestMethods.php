<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\DELETE;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\HEAD;
use Retrofit\Attribute\OPTIONS;
use Retrofit\Attribute\PATCH;
use Retrofit\Attribute\POST;
use Retrofit\Attribute\PUT;

interface AllHttpRequestMethods
{
    #[DELETE('/delete')]
    public function delete();

    #[GET('/get')]
    public function get();

    #[HEAD('/head')]
    public function head();

    #[OPTIONS('/options')]
    public function options();

    #[PATCH('/patch')]
    public function patch();

    #[POST('/post')]
    public function post();

    #[PUT('/put')]
    public function put();
}
