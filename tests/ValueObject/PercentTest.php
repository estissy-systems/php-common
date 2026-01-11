<?php

declare(strict_types=1);

namespace ValueObject;

use EstissySystems\PhpCommon\ValueObject\Currency;
use EstissySystems\PhpCommon\ValueObject\Money;
use EstissySystems\PhpCommon\ValueObject\Percent;
use PHPUnit\Framework\TestCase;

final class PercentTest extends TestCase
{
    public function testFromBasisPoints(): void
    {
        $percent = Percent::fromBasisPoints(1234);

        self::assertSame('12.34%', $percent->toString());

        $percent = Percent::fromBasisPoints(12345);

        self::assertSame('123.45%', $percent->toString());

        $percent = Percent::fromBasisPoints(-12345);

        self::assertSame('-123.45%', $percent->toString());
    }

    public function testZero(): void
    {
        $percent = Percent::zero();

        self::assertSame('0%', $percent->toString());
    }

    public function testHundred(): void
    {
        $percent = Percent::hundred();

        self::assertSame('100%', $percent->toString());
    }

    public function testAdd(): void
    {
        $firstPercent = Percent::fromBasisPoints(500);
        $secondPercent = Percent::fromBasisPoints(1000);

        self::assertSame('15%', $firstPercent->add($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(5000);
        $secondPercent = Percent::fromBasisPoints(1005);

        self::assertSame('60.05%', $firstPercent->add($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(-5000);
        $secondPercent = Percent::fromBasisPoints(-1005);

        self::assertSame('-60.05%', $firstPercent->add($secondPercent)->toString());
    }

    public function testSubtract(): void
    {
        $firstPercent = Percent::fromBasisPoints(1000);
        $secondPercent = Percent::fromBasisPoints(500);

        self::assertSame('5%', $firstPercent->subtract($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(5000);
        $secondPercent = Percent::fromBasisPoints(1005);

        self::assertSame('39.95%', $firstPercent->subtract($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(-5000);
        $secondPercent = Percent::fromBasisPoints(-1005);

        self::assertSame('-39.95%', $firstPercent->subtract($secondPercent)->toString());
    }

    public function testIncreaseBy(): void
    {
        $firstPercent = Percent::fromBasisPoints(2000);
        $secondPercent = Percent::fromBasisPoints(5000);

        self::assertSame('30%', $firstPercent->increaseBy($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(2000);
        $secondPercent = Percent::fromBasisPoints(-5000);

        self::assertSame('10%', $firstPercent->increaseBy($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(-2000);
        $secondPercent = Percent::fromBasisPoints(5000);

        self::assertSame('-30%', $firstPercent->increaseBy($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(-2000);
        $secondPercent = Percent::fromBasisPoints(-5000);

        self::assertSame('-10%', $firstPercent->increaseBy($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(-2000);
        $secondPercent = Percent::fromBasisPoints(-20000);

        self::assertSame('20%', $firstPercent->increaseBy($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(100);
        $secondPercent = Percent::fromBasisPoints(50);

        self::assertSame('1.01%', $firstPercent->increaseBy($secondPercent)->toString());

        $firstPercent = Percent::fromBasisPoints(100);
        $secondPercent = Percent::fromBasisPoints(30);

        self::assertSame('1%', $firstPercent->increaseBy($secondPercent)->toString());
    }

    public function testToHumanString(): void
    {
        $percent = Percent::fromBasisPoints(315);

        self::assertSame('3.15%', $percent->toHumanString('en_US'));
        self::assertSame('3.15%', $percent->toHumanString('en_GB'));
        self::assertSame('3,15 %', $percent->toHumanString('de_DE'));
        self::assertSame('3,15 %', $percent->toHumanString('fr_FR'));
        self::assertSame('3,15%', $percent->toHumanString('pl_PL'));
        self::assertSame('3,15 %', $percent->toHumanString('cs_CZ'));

        $percent = Percent::fromBasisPoints(3150000);

        self::assertSame('31,500.00%', $percent->toHumanString('en_US'));
        self::assertSame('31,500.00%', $percent->toHumanString('en_GB'));
        self::assertSame('31.500,00 %', $percent->toHumanString('de_DE'));
        self::assertSame('31 500,00 %', $percent->toHumanString('fr_FR'));
        self::assertSame('31 500,00%', $percent->toHumanString('pl_PL'));
        self::assertSame('31 500,00 %', $percent->toHumanString('cs_CZ'));
    }

    public function testEquals(): void
    {
        $firstPercent = Percent::fromBasisPoints(315);
        $secondPercent = Percent::fromBasisPoints(315);

        self::assertTrue($firstPercent->equals($secondPercent));

        $firstPercent = Percent::fromBasisPoints(31500);
        $secondPercent = Percent::fromBasisPoints(31500);

        self::assertTrue($firstPercent->equals($secondPercent));

        $firstPercent = Percent::fromBasisPoints(315);
        $secondPercent = Percent::fromBasisPoints(31500);

        self::assertFalse($firstPercent->equals($secondPercent));

        $firstPercent = Percent::fromBasisPoints(31500);
        $secondPercent = Percent::fromBasisPoints(315);

        self::assertFalse($firstPercent->equals($secondPercent));

        $percent = Percent::fromBasisPoints(31500);
        $money = Money::fromAmountAndCurrency(31500, Currency::USD);

        self::assertFalse($percent->equals($money));
    }

    public function testFromFloat(): void
    {
        $percent = Percent::fromFloat(3.15);

        self::assertSame('3.15%', $percent->toString());

        $percent = Percent::fromFloat(3.155);

        self::assertSame('3.16%', $percent->toString());

        $percent = Percent::fromFloat(3000.155);

        self::assertSame('3000.16%', $percent->toString());

        $percent = Percent::fromFloat(-3000.155);

        self::assertSame('-3000.16%', $percent->toString());
    }
}
