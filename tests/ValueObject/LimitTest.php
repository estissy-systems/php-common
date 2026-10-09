<?php

declare(strict_types=1);

namespace ValueObject;

use EstissySystems\PhpCommon\ValueObject\Limit;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LimitTest extends TestCase
{
    public function testCreateFromInteger(): void
    {
        $limit = Limit::fromInteger(10);

        self::assertSame(10, $limit->toInteger());
    }

    public function testCreateWithValueOfOne(): void
    {
        $limit = Limit::fromInteger(1);

        self::assertSame(1, $limit->toInteger());
    }

    #[DataProvider('invalidLimitProvider')]
    public function testThrowsExceptionForInvalidValues(int $value): void
    {
        $this->expectException(LogicException::class);

        Limit::fromInteger($value);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function invalidLimitProvider(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
            'large negative' => [-100],
        ];
    }

    public function testEqualityForSameValue(): void
    {
        $limit1 = Limit::fromInteger(10);
        $limit2 = Limit::fromInteger(10);

        self::assertTrue($limit1->equals($limit2));
    }

    public function testInequalityForDifferentValues(): void
    {
        $limit1 = Limit::fromInteger(10);
        $limit2 = Limit::fromInteger(20);

        self::assertFalse($limit1->equals($limit2));
    }

    public function testInequalityForDifferentType(): void
    {
        $limit = Limit::fromInteger(10);

        self::assertFalse($limit->equals('not a limit'));
    }

    public function testHashConsistency(): void
    {
        $limit1 = Limit::fromInteger(10);
        $limit2 = Limit::fromInteger(10);

        self::assertSame($limit1->hash(), $limit2->hash());
    }

    public function testHashDifference(): void
    {
        $limit1 = Limit::fromInteger(10);
        $limit2 = Limit::fromInteger(20);

        self::assertNotSame($limit1->hash(), $limit2->hash());
    }
}
