<?php

declare(strict_types=1);

namespace ValueObject;

use EstissySystems\PhpCommon\ValueObject\Offset;
use LogicException;
use PHPUnit\Framework\TestCase;

class OffsetTest extends TestCase
{
    public function testCreateFromInteger(): void
    {
        $offset = Offset::fromInteger(10);

        self::assertSame(10, $offset->toInteger());
    }

    public function testCreateWithZero(): void
    {
        $offset = Offset::fromInteger(0);

        self::assertSame(0, $offset->toInteger());
    }

    public function testZeroFactory(): void
    {
        $offset = Offset::zero();

        self::assertSame(0, $offset->toInteger());
    }

    public function testThrowsExceptionForNegativeValue(): void
    {
        $this->expectException(LogicException::class);

        Offset::fromInteger(-1);
    }

    public function testEqualityForSameValue(): void
    {
        $offset1 = Offset::fromInteger(10);
        $offset2 = Offset::fromInteger(10);

        self::assertTrue($offset1->equals($offset2));
    }

    public function testZeroFactoryEqualsFromInteger(): void
    {
        $offset1 = Offset::zero();
        $offset2 = Offset::fromInteger(0);

        self::assertTrue($offset1->equals($offset2));
    }

    public function testInequalityForDifferentValues(): void
    {
        $offset1 = Offset::fromInteger(10);
        $offset2 = Offset::fromInteger(20);

        self::assertFalse($offset1->equals($offset2));
    }

    public function testInequalityForDifferentType(): void
    {
        $offset = Offset::fromInteger(10);

        self::assertFalse($offset->equals('not an offset'));
    }

    public function testHashConsistency(): void
    {
        $offset1 = Offset::fromInteger(10);
        $offset2 = Offset::fromInteger(10);

        self::assertSame($offset1->hash(), $offset2->hash());
    }

    public function testHashDifference(): void
    {
        $offset1 = Offset::fromInteger(10);
        $offset2 = Offset::fromInteger(20);

        self::assertNotSame($offset1->hash(), $offset2->hash());
    }
}
