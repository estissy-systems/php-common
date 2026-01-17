<?php

declare(strict_types=1);

namespace EstissySystems\PhpCommon\ValueObject;

use Ds\Hashable;
use LogicException;
use NumberFormatter;
use RoundingMode;

/**
 * Represents money, negative values available. The amount is stored in the smallest unit as an integer.
 */
readonly class Money implements Hashable
{
    private const int BC_MATH_SCALE = 14;

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
     * @param int $amount The amount in the smallest unit as an integer
     */
    public static function fromAmountAndCurrency(int $amount, Currency $currency): Money
    {
        return new Money((string)$amount, $currency);
    }

    public function toString(RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero): string
    {
        $amount = (int)bcround($this->amount, 0, $roundingMode);
        $divisor = 10 ** $this->currency()->getDecimals();

        $dividedAmount = $amount / $divisor;

        return sprintf("%.{$this->currency()->getDecimals()}f %s", $dividedAmount, $this->currency()->value);
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function toHumanString(
        string $humanLocale,
        RoundingMode $roundingMode = RoundingMode::HalfAwayFromZero
    ): string {
        $amount = (int)bcround($this->amount, 0, $roundingMode);
        $divisor = 10 ** $this->currency()->getDecimals();

        $dividedAmount = $amount / $divisor;

        $numberFormatter = new NumberFormatter($humanLocale, NumberFormatter::CURRENCY);

        $result = $numberFormatter->formatCurrency($dividedAmount, $this->currency()->value);

        if ($result === false) {
            throw new LogicException(
                "Error while formatting amount $dividedAmount in currency {$this->currency()->value} and locale $humanLocale."
            );
        }

        return $result;
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

    public function multiply(
        float $multiplier,
        int $multiplierPrecision,
    ): Money {
        $formattedMultiplier = number_format($multiplier, $multiplierPrecision, '.', '');

        $newAmount = bcmul($this->amount(), $formattedMultiplier, self::BC_MATH_SCALE);

        return new Money($newAmount, $this->currency());
    }

    public function divide(
        float $divisor,
        int $divisorPrecision,
    ): Money {
        $formattedDivisor = number_format($divisor, $divisorPrecision, '.', '');

        $newAmount = bcdiv($this->amount(), $formattedDivisor, self::BC_MATH_SCALE);

        return new Money($newAmount, $this->currency());
    }

    public function convert(
        Currency $targetCurrency,
        float $conversionRate,
        int $conversionRatePrecision,
    ): Money {
        $formattedConversionRate = number_format($conversionRate, $conversionRatePrecision, '.', '');

        $newAmount = bcmul($this->amount(), $formattedConversionRate, self::BC_MATH_SCALE);

        return new Money($newAmount, $targetCurrency);
    }
}
