<?php

declare(strict_types=1);

namespace ValueObject;

use EstissySystems\PhpCommon\ValueObject\Currency;
use EstissySystems\PhpCommon\ValueObject\Money;
use LogicException;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testToStringShouldReturnCorrectMoneyString(): void
    {
        $money = Money::fromAmountAndCurrency(15000, Currency::PLN);

        self::assertSame('150.00 PLN', $money->toString());

        $money = Money::fromAmountAndCurrency(15005, Currency::PLN);

        self::assertSame('150.05 PLN', $money->toString());

        $money = Money::fromAmountAndCurrency(0, Currency::PLN);

        self::assertSame('0.00 PLN', $money->toString());

        $money = Money::fromAmountAndCurrency(14, Currency::PLN);

        self::assertSame('0.14 PLN', $money->toString());

        $money = Money::fromAmountAndCurrency(-15005, Currency::PLN);

        self::assertSame('-150.05 PLN', $money->toString());

        $money = Money::fromAmountAndCurrency(15000, Currency::JPY);

        self::assertSame('15000 JPY', $money->toString());
    }

    public function testEqualsShouldReturnTrueWhenAmountAndCurrencyOfProvidedMoneyIsEqual(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);

        self::assertTrue($firstMoney->equals($secondMoney));
    }

    public function testEqualsShouldReturnFalseWhenAmountOrCurrencyOfProvidedMoneyIsNotEqual(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        self::assertFalse($firstMoney->equals($secondMoney));

        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15000, Currency::USD);

        self::assertFalse($firstMoney->equals($secondMoney));

        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15005, Currency::USD);

        self::assertFalse($firstMoney->equals($secondMoney));
    }

    public function testAddShouldAddAmountsAndKeepCurrencyWhenMoneyWithSameCurrencyProvided(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->add($secondMoney);

        self::assertSame(30005, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());
    }

    public function testAddShouldThrowExceptionWhenMoneyWithDifferentCurrencyProvided(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15005, Currency::USD);

        $this->expectException(LogicException::class);

        $firstMoney->add($secondMoney);
    }

    public function testSubtractShouldSubtractAmountsAndKeepCurrencyWhenMoneyWithSameCurrencyProvided(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->subtract($secondMoney);

        self::assertSame(-5, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());
    }

    public function testSubtractShouldThrowExceptionWhenMoneyWithDifferentCurrencyProvided(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15005, Currency::USD);

        $this->expectException(LogicException::class);

        $firstMoney->subtract($secondMoney);
    }

    public function testMultiplyShouldMultiplyAmountAndKeepCurrency(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);

        $newMoney = $firstMoney->multiply(1.2);

        self::assertSame(18000, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());

        $firstMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->multiply(1.33);

        self::assertSame(19957, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());

        $firstMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->multiply(1.33333);

        self::assertSame(20007, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());
    }

    public function testDivideShouldDivideAmountAndKeepCurrency(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);

        $newMoney = $firstMoney->divide(1.2);

        self::assertSame(12500, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());

        $firstMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->divide(1.33);

        self::assertSame(11282, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());

        $firstMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->divide(1.33333);

        self::assertSame(11254, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());
    }

    public function testConvertShouldMultiplyMoneyAndChangeCurrency(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::USD);

        $newMoney = $firstMoney->convert(Currency::PLN, 3.8);

        self::assertSame(57000, $newMoney->amount());
        self::assertSame(Currency::PLN, $newMoney->currency());
    }

    public function testToHumanStringShouldReturnMoneyInProvidedLocaleFormat(): void
    {
        $money = Money::fromAmountAndCurrency(150000005, Currency::PLN);
        $result = $money->toHumanString('pl_PL');

        self::assertSame('1 500 000,05 zł', $result);

        $money = Money::fromAmountAndCurrency(-15000005, Currency::PLN);
        $result = $money->toHumanString('pl_PL');

        self::assertSame('-150 000,05 zł', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::EUR);
        $result = $money->toHumanString('de_DE');

        self::assertSame('150.000,05 €', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::USD);
        $result = $money->toHumanString('en_US');

        self::assertSame('$150,000.05', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::PLN);
        $result = $money->toHumanString('en_US');

        self::assertSame('PLN 150,000.05', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::JPY);
        $result = $money->toHumanString('en_US');

        self::assertSame('¥15,000,005', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::EUR);
        $result = $money->toHumanString('en_US');

        self::assertSame('€150,000.05', $result);
    }
}
