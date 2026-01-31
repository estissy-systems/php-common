<?php

declare(strict_types=1);

namespace ValueObject;

use DivisionByZeroError;
use EstissySystems\PhpCommon\ValueObject\Currency;
use EstissySystems\PhpCommon\ValueObject\Money;
use EstissySystems\PhpCommon\ValueObject\Percent;
use LogicException;
use OverflowException;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testGetRawShouldReturnCorrectMoneyString(): void
    {
        $money = Money::fromAmountAndCurrency(15000, Currency::PLN);

        self::assertSame('150.00000000000000 PLN', $money->getRaw());

        $money = Money::fromAmountAndCurrency(15005, Currency::PLN);

        self::assertSame('150.05000000000000 PLN', $money->getRaw());

        $money = Money::fromAmountAndCurrency(0, Currency::PLN);

        self::assertSame('0.00000000000000 PLN', $money->getRaw());

        $money = Money::fromAmountAndCurrency(14, Currency::PLN);

        self::assertSame('0.14000000000000 PLN', $money->getRaw());

        $money = Money::fromAmountAndCurrency(-15005, Currency::PLN);

        self::assertSame('-150.05000000000000 PLN', $money->getRaw());

        $money = Money::fromAmountAndCurrency(15000, Currency::JPY);

        self::assertSame('15000.00000000000000 JPY', $money->getRaw());
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

    public function testEqualsShouldReturnFalseWhenDifferentObjectProvided(): void
    {
        $money = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $percent = Percent::fromBasisPoints(15000);

        self::assertFalse($money->equals($percent));
    }

    public function testAddShouldAddAmountsAndKeepCurrencyWhenMoneyWithSameCurrencyProvided(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $secondMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->add($secondMoney);

        self::assertSame('30005.00000000000000', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());
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

        self::assertSame('-5.00000000000000', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());
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

        $newMoney = $firstMoney->multiply('1.2');

        self::assertSame('18000.00000000000000', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());

        $firstMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->multiply('1.33');

        self::assertSame('19956.65000000000000', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());

        $firstMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->multiply('1.33333');

        self::assertSame('20006.61665000000000', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());

        $money = Money::fromAmountAndCurrency(9007199254740995, Currency::USD);
        $newMoney = $money->multiply('0.3333333333333333');

        self::assertSame('3002399751580331.36642669150863', $newMoney->getRawAmount());
    }

    public function testDivideShouldDivideAmountAndKeepCurrency(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::PLN);

        $newMoney = $firstMoney->divide('1.2');

        self::assertSame('12500.00000000000000', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());

        $firstMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->divide('1.33');

        self::assertSame('11281.95488721804511', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());

        $firstMoney = Money::fromAmountAndCurrency(15005, Currency::PLN);

        $newMoney = $firstMoney->divide('1.33333');

        self::assertSame('11253.77813444533611', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());

        $money = Money::fromAmountAndCurrency(100, Currency::PLN);
        $dividedMoney = $money->divide(6);

        self::assertSame('16.66666666666666', $dividedMoney->getRawAmount());

        $this->expectException(DivisionByZeroError::class);
        $money = Money::fromAmountAndCurrency(100, Currency::PLN);
        $money->divide(0);

        $this->expectException(DivisionByZeroError::class);
        $money = Money::fromAmountAndCurrency(100, Currency::PLN);
        $money->divide('0.000');
    }

    public function testConvertShouldMultiplyMoneyAndChangeCurrency(): void
    {
        $firstMoney = Money::fromAmountAndCurrency(15000, Currency::USD);

        $newMoney = $firstMoney->convert(Currency::PLN, '3.8');

        self::assertSame('57000.00000000000000', $newMoney->getRawAmount());
        self::assertSame(Currency::PLN, $newMoney->getCurrency());
    }

    public function testToHumanStringShouldReturnMoneyInProvidedLocaleFormat(): void
    {
        $money = Money::fromAmountAndCurrency(150000000, Currency::PLN);
        $result = $money->getRoundedLocaleString('pl_PL');

        self::assertSame('1 500 000,00 zł', $result);

        $money = Money::fromAmountAndCurrency(150000005, Currency::PLN);
        $result = $money->getRoundedLocaleString('pl_PL');

        self::assertSame('1 500 000,05 zł', $result);

        $money = Money::fromAmountAndCurrency(-15000005, Currency::PLN);
        $result = $money->getRoundedLocaleString('pl_PL');

        self::assertSame('-150 000,05 zł', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::EUR);
        $result = $money->getRoundedLocaleString('de_DE');

        self::assertSame('150.000,05 €', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::USD);
        $result = $money->getRoundedLocaleString('en_US');

        self::assertSame('$150,000.05', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::PLN);
        $result = $money->getRoundedLocaleString('en_US');

        self::assertSame('PLN 150,000.05', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::JPY);
        $result = $money->getRoundedLocaleString('en_US');

        self::assertSame('¥15,000,005', $result);

        $money = Money::fromAmountAndCurrency(15000005, Currency::EUR);
        $result = $money->getRoundedLocaleString('en_US');

        self::assertSame('€150,000.05', $result);

        $money = Money::fromAmountAndCurrency('92233720368547758080', Currency::EUR);
        $result = $money->getRoundedLocaleString('en_US');

        self::assertSame('€922,337,203,685,477,580.80', $result);
    }

    public function testGetRoundedStringAmountInMajorUnitsShouldReturnRoundedAmount(): void
    {
        $money = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $money = $money->multiply('1.33333');

        self::assertSame('200.00', $money->getRoundedStringAmountInMajorUnits());
    }

    public function testGetRoundedStringAmountInMinorUnitsShouldReturnRoundedAmount(): void
    {
        $money = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $money = $money->multiply('1.33333');

        self::assertSame('20000', $money->getRoundedStringAmountInMinorUnits());
    }

    public function testGetRoundedIntAmountInMinorUnitsShouldReturnRoundedAmount(): void
    {
        $money = Money::fromAmountAndCurrency(15000, Currency::PLN);
        $money = $money->multiply('1.33333');

        self::assertSame(20000, $money->getRoundedIntAmountInMinorUnits());
    }

    public function testGetRoundedIntAmountInMinorUnitsShouldThrowOverflowExceptionWhenMoneyCanNotBeCastedToInt(): void
    {
        $money = Money::fromAmountAndCurrency(PHP_INT_MAX, Currency::PLN);
        $money = $money->multiply('10');

        $this->expectException(OverflowException::class);

        $money->getRoundedIntAmountInMinorUnits();

        $money = Money::fromAmountAndCurrency(PHP_INT_MIN, Currency::PLN);
        $money = $money->multiply('10');

        $this->expectException(OverflowException::class);

        $money->getRoundedIntAmountInMinorUnits();
    }
}
