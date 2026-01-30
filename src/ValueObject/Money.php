<?php

declare(strict_types=1);

namespace EstissySystems\PhpCommon\ValueObject;

use Ds\Hashable;
use LogicException;
use NumberFormatter;
use RoundingMode;

/**
 * Represents money, negative values available. The amount is stored in the smallest unit possible.
 */
readonly class Money implements Hashable
{
    private const int BC_MATH_SCALE = 14;
    private const string NON_BREAKING_SPACE_UNICODE_CODE = "\u{00A0}";
    private const string NARROW_NON_BREAKING_SPACE_UNICODE_CODE = "\u{202F}";

    /**
     * @var numeric-string
     */
    private string $amount;
    private Currency $currency;
    private string $hash;

    /**
     * @param numeric-string $amount
     * @param Currency $currency
     */
    private function __construct(string $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->hash = hash('sha256', self::class . $amount . $currency->value);
    }

    /**
     * @param int|numeric-string $amount The amount in the smallest unit as possible
     */
    public static function fromAmountAndCurrency(int|string $amount, Currency $currency): Money
    {
        if (is_int($amount)) {
            $amount = (string)$amount;
        }

        return new Money($amount, $currency);
    }

    public function toString(RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero): string
    {
        $amount = bcround($this->amount, 0, $roundingMode);
        $divisor = 10 ** $this->currency()->getDecimals();

        $dividedAmount = bcdiv($amount, (string)$divisor, $this->currency()->getDecimals());

        return sprintf("%s %s", $dividedAmount, $this->currency()->value);
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function toLocaleString(
        string $locale,
        RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero
    ): string {
        $divisor = 10 ** $this->currency()->getDecimals();
        $dividedAmount = bcdiv($this->amount, (string)$divisor, self::BC_MATH_SCALE);

        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $numberFormatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $this->currency()->value);

        $symbol = $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        $decimalSeparator = null;
        if ($this->currency()->getDecimals() > 0) {
            $decimalSeparator = $numberFormatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
        }
        $groupingSeparator = $numberFormatter->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL);

        $roundedAmount = bcround($dividedAmount, $this->currency()->getDecimals(), $roundingMode);

        $integralPart = explode('.', $roundedAmount)[0];
        $decimalPart = null;
        if ($this->currency()->getDecimals() > 0) {
            $decimalPart = explode('.', $roundedAmount)[1];
        }

        $groupedIntegralPart = preg_replace('/\B(?=(\d{3})+(?!\d))/', $groupingSeparator, $integralPart);

        $exampleCurrencyAmount = 1;
        $exampleFormatting = $numberFormatter->formatCurrency($exampleCurrencyAmount, $this->currency()->value);

        if ($exampleFormatting === false) {
            throw new LogicException(
                "Error while formatting currency {$this->currency()->value} for locale $locale"
            );
        }

        $spaceSeparator = match (true) {
            str_contains(
                $exampleFormatting,
                self::NON_BREAKING_SPACE_UNICODE_CODE
            ) => self::NON_BREAKING_SPACE_UNICODE_CODE,
            str_contains(
                $exampleFormatting,
                self::NARROW_NON_BREAKING_SPACE_UNICODE_CODE
            ) => self::NARROW_NON_BREAKING_SPACE_UNICODE_CODE,
            default => '',
        };

        return match (true) {
            str_starts_with(
                $exampleFormatting,
                $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . $spaceSeparator,
            ) && $decimalSeparator !== null, => $symbol . $spaceSeparator . $groupedIntegralPart . $decimalSeparator . $decimalPart,
            str_ends_with(
                $exampleFormatting,
                $spaceSeparator . $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL),
            ) && $decimalSeparator !== null => $groupedIntegralPart . $decimalSeparator . $decimalPart . $spaceSeparator . $symbol,
            str_starts_with(
                $exampleFormatting,
                $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL),
            ) && $decimalSeparator !== null, => $symbol . $groupedIntegralPart . $decimalSeparator . $decimalPart,
            str_ends_with(
                $exampleFormatting,
                $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL),
            ) && $decimalSeparator !== null, => $groupedIntegralPart . $decimalSeparator . $decimalPart . $symbol,
            str_starts_with(
                $exampleFormatting,
                $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . $spaceSeparator,
            ) && $decimalSeparator === null, => $symbol . $spaceSeparator . $groupedIntegralPart,
            str_ends_with(
                $exampleFormatting,
                $spaceSeparator . $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL),
            ) && $decimalSeparator === null => $groupedIntegralPart . $spaceSeparator . $symbol,
            str_starts_with(
                $exampleFormatting,
                $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL),
            ) && $decimalSeparator === null, => $symbol . $groupedIntegralPart,
            str_ends_with(
                $exampleFormatting,
                $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL),
            ) && $decimalSeparator === null, => $groupedIntegralPart . $symbol,
            default => throw new LogicException(
                "Error while formatting currency {$this->currency()->value} for locale $locale"
            )
        };
    }

    public function equals($obj): bool
    {
        if (($obj instanceof Hashable) === false) {
            return false;
        }

        return $this->hash === $obj->hash();
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function add(Money $money): Money
    {
        if ($this->currency() !== $money->currency()) {
            throw new LogicException(
                "Error while adding money. Money currency {$this->currency()->value} is not equal added money currency {$money->currency()->value}."
            );
        }

        $newAmount = bcadd($this->amount(), $money->amount(), 0);

        return new Money($newAmount, $this->currency());
    }

    /**
     * @return numeric-string
     */
    public function amount(RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero): string
    {
        return bcround($this->amount, 0, $roundingMode);
    }

    public function subtract(Money $money): Money
    {
        if ($this->currency() !== $money->currency()) {
            throw new LogicException(
                "Error while subtracting money. Money currency {$this->currency()->value} is not equal subtracted money currency {$money->currency()->value}."
            );
        }

        $newAmount = bcsub($this->amount(), $money->amount(), 0);

        return new Money($newAmount, $this->currency());
    }

    /**
     * @param int|numeric-string $multiplier
     */
    public function multiply(
        int|string $multiplier,
    ): Money {
        if (is_string($multiplier) === true && is_numeric($multiplier) === false) {
            throw new LogicException('Error while dividing. Divisor must be a numeric string or integer.');
        }

        if (is_int($multiplier) === true) {
            $multiplier = (string)$multiplier;
        }

        $newAmount = bcmul($this->amount(), $multiplier, self::BC_MATH_SCALE);

        return new Money($newAmount, $this->currency());
    }

    /**
     * @param int|numeric-string $divisor
     */
    public function divide(
        int|string $divisor,
    ): Money {
        if (is_string($divisor) === true && is_numeric($divisor) === false) {
            throw new LogicException('Error while dividing. Divisor must be a numeric string or integer.');
        }

        if (is_int($divisor) === true) {
            $divisor = (string)$divisor;
        }

        if ((float)$divisor === 0.0) {
            throw new LogicException('Error while dividing. Divisor must not be zero.');
        }

        $newAmount = bcdiv($this->amount(), $divisor, self::BC_MATH_SCALE);

        return new Money($newAmount, $this->currency());
    }

    /**
     * @param int|numeric-string $conversionRate
     */
    public function convert(
        Currency $targetCurrency,
        int|string $conversionRate,
    ): Money {
        if (is_string($conversionRate) === true && is_numeric($conversionRate) === false) {
            throw new LogicException('Error while dividing. Divisor must be a numeric string or integer.');
        }

        if (is_int($conversionRate) === true) {
            $conversionRate = (string)$conversionRate;
        }

        $newAmount = bcmul($this->amount(), $conversionRate, self::BC_MATH_SCALE);

        return new Money($newAmount, $targetCurrency);
    }
}
