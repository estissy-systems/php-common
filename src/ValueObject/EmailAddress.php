<?php

declare(strict_types=1);

namespace EstissySystems\PhpCommon\ValueObject;

use Ds\Hashable;
use LogicException;

/**
 * Represents an RFC 5321 & 5322 valid email address.
 *
 * Does not support:
 * 1. Comments in the email address ex. john.doe(comment)@example.com
 */
readonly class EmailAddress implements Hashable
{
    private string $email;
    private string $hash;

    private function __construct(string $email)
    {
        self::validate($email);

        $this->email = $email;
        $this->hash = hash('sha256', self::class . $email);
    }

    public static function validate(string $email): void
    {
        if (self::isValid($email) === false) {
            throw new LogicException("Provided email address: {$email} is not valid.");
        }
    }

    public static function isValid(string $email, bool $allowQuotationMarkInEmailLocalPart = false): bool
    {
        $result = filter_var($email, FILTER_VALIDATE_EMAIL);

        if ($result === false) {
            return false;
        }

        if ($allowQuotationMarkInEmailLocalPart === false && str_contains($email, '"') === true) {
            return false;
        }

        return true;
    }

    public static function fromString(string $email): EmailAddress
    {
        return new EmailAddress($email);
    }

    public function toString(): string
    {
        return $this->email;
    }

    /**
     * The part before @ sign
     *
     * @return string
     */
    public function localPart(): string
    {
        return explode('@', $this->email)[0];
    }

    /**
     * The part after @ sign
     *
     * @return string
     */
    public function domainPart(): string
    {
        return explode('@', $this->email)[1];
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
}
