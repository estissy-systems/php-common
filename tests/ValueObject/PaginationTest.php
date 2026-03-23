<?php

declare(strict_types=1);

namespace ValueObject;

use EstissySystems\PhpCommon\ValueObject\Limit;
use EstissySystems\PhpCommon\ValueObject\Offset;
use EstissySystems\PhpCommon\ValueObject\Pagination;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function testCreateFromLimitAndOffset(): void
    {
        $limit = Limit::fromInteger(10);
        $offset = Offset::fromInteger(20);
        $pagination = Pagination::fromLimitAndOffset($limit, $offset);

        self::assertSame(10, $pagination->limit()->toInteger());
        self::assertSame(20, $pagination->offset()->toInteger());
    }

    #[DataProvider('pageAndPageSizeProvider')]
    public function testCreateFromPageAndPageSize(int $page, int $pageSize, int $expectedOffset): void
    {
        $pagination = Pagination::fromPageAndPageSize($page, $pageSize);

        self::assertSame($pageSize, $pagination->limit()->toInteger());
        self::assertSame($expectedOffset, $pagination->offset()->toInteger());
    }

    /**
     * @return array<string, array{int, int, int}>
     */
    public static function pageAndPageSizeProvider(): array
    {
        return [
            'first page' => [1, 10, 0],
            'second page' => [2, 10, 10],
            'third page' => [3, 10, 20],
            'first page with 25' => [1, 25, 0],
            'fourth page with 25' => [4, 25, 75],
        ];
    }

    public function testThrowsExceptionForZeroPage(): void
    {
        $this->expectException(LogicException::class);

        Pagination::fromPageAndPageSize(0, 10);
    }

    public function testThrowsExceptionForNegativePage(): void
    {
        $this->expectException(LogicException::class);

        Pagination::fromPageAndPageSize(-1, 10);
    }

    public function testThrowsExceptionForInvalidPageSize(): void
    {
        $this->expectException(LogicException::class);

        Pagination::fromPageAndPageSize(1, 0);
    }

    public function testEqualityForSameValues(): void
    {
        $pagination1 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );
        $pagination2 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );

        self::assertTrue($pagination1->equals($pagination2));
    }

    public function testEqualityBetweenFactoryMethods(): void
    {
        $pagination1 = Pagination::fromPageAndPageSize(3, 10);
        $pagination2 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );

        self::assertTrue($pagination1->equals($pagination2));
    }

    public function testInequalityForDifferentLimit(): void
    {
        $pagination1 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );
        $pagination2 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(20),
            Offset::fromInteger(20),
        );

        self::assertFalse($pagination1->equals($pagination2));
    }

    public function testInequalityForDifferentOffset(): void
    {
        $pagination1 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );
        $pagination2 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(30),
        );

        self::assertFalse($pagination1->equals($pagination2));
    }

    public function testInequalityForDifferentType(): void
    {
        $pagination = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );

        self::assertFalse($pagination->equals('not a pagination'));
    }

    public function testHashConsistency(): void
    {
        $pagination1 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );
        $pagination2 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );

        self::assertSame($pagination1->hash(), $pagination2->hash());
    }

    public function testHashDifference(): void
    {
        $pagination1 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(20),
        );
        $pagination2 = Pagination::fromLimitAndOffset(
            Limit::fromInteger(10),
            Offset::fromInteger(30),
        );

        self::assertNotSame($pagination1->hash(), $pagination2->hash());
    }
}
