<?php
declare(strict_types=1);

namespace Retrofit\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\SimpleClass;

class RetrofitTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionWhenServiceIsNotInterface(): void
    {
        //given
        $retrofit = new Retrofit();

        //when
        try {
            $retrofit->create(SimpleClass::class);
        } catch (InvalidArgumentException $e) {
            //then
            $this->assertSame('API declarations must be interface', $e->getMessage());
        }
    }

    /** @test */
    public function shouldName(): void
    {

    }
}
