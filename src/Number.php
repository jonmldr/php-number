<?php

declare(strict_types=1);

namespace Madebybob\Number;

use Madebybob\Number\Exception\InvalidNumberInputException;
use Madebybob\Number\Formatter\Formatter;
use Madebybob\Number\Formatter\FormatterInterface;

class Number
{
    private string $originalValue;
    private string $value;
    private FormatterInterface $formatter;

    public function __construct($value, ?FormatterInterface $formatter = null)
    {
        if (! $formatter) {
            $formatter = new Formatter();
        }
        $this->formatter = $formatter;

        $this->originalValue = (string) $value;
        $this->value = (string) $value;
    }

    /**
     * Adds the given value to the current number.
     *
     * @param Number|string|float|int $value
     * @param int $scale default 4
     */
    public function add($value, $scale = 4): self
    {
        $number = $this->getNumberFromValue($value);

        $sum = bcadd($this->value, $number->toString(), $scale);

        return new Number($sum);
    }

    /**
     * Subtracts the given value from the current number.
     *
     * @param Number|string|float|int $value
     * @param int $scale default 4
     */
    public function subtract($value, $scale = 4): self
    {
        $number = $this->getNumberFromValue($value);

        $sum = bcsub($this->value, $number->toString(), $scale);

        return new Number($sum);
    }

    /**
     * Alias for subtract method.
     *
     * @param Number|string|float|int $value
     * @param int $scale default 4
     */
    public function sub($value, $scale = 4): self
    {
        return $this->subtract($value, $scale);
    }

    /**
     * Alias for subtract method.
     *
     * @param Number|string|float|int $value
     * @param int $scale default 4
     */
    public function minus($value, $scale = 4): self
    {
        return $this->subtract($value, $scale);
    }

    /**
     * Return boolean if the current value is a positive number.
     */
    public function isPositive(): bool
    {
        return bccomp($this->value, '0') === 1;
    }

    /**
     * Return boolean if the current value is a positive number.
     */
    public function isNegative(): bool
    {
        return bccomp($this->value, '0') === -1;
    }

    /**
     * Returns boolean if the current value is zero "0".
     */
    public function isZero(): bool
    {
        return bccomp($this->value, '0') === 0;
    }

    /**
     * Converts the current Number instance into a string.
     *
     * @param int $scale default 4
     */
    public function toString($scale = 4): string
    {
        return bcadd('0.000', $this->value, $scale);
    }

    /**
     * @internal
     * @param Number|string|float|int $value
     */
    private function getNumberFromValue($value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_string($value) || is_float($value) || is_int($value)) {
            return new self($value);
        }

        throw new InvalidNumberInputException();
    }
}
