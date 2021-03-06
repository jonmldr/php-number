<?php

namespace Madebybob\Number\Tests;

use Madebybob\Number\Exception\InvalidNumberInputTypeException;
use Madebybob\Number\Money;
use Madebybob\Number\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    /** @test */
    public function true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function testCanInitializeFromString(): void
    {
        $number = new Number('200');

        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('200.00', $number->toString(2));
        $this->assertEquals('200', $number->toString(0));
    }

    public function testCanInitializeFromInteger(): void
    {
        $number = new Number(200);

        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('200.00', $number->toString(2));
        $this->assertEquals('200', $number->toString(0));

        $number = new Number(-200);

        $this->assertEquals('-200.0000', $number->toString());
        $this->assertEquals('-200.00', $number->toString(2));
        $this->assertEquals('-200', $number->toString(0));
    }

    public function testCanAddStringAsImmutable(): void
    {
        $number = new Number('200');
        $result = $number->add('400');

        $this->assertInstanceOf(Number::class, $result);
        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('600.0000', $result->toString());
        $this->assertTrue($result->isPositive());
    }

    public function testCanAddFloatAsImmutable(): void
    {
        $number = new Number('200');
        $result = $number->add(400.5);

        $this->assertInstanceOf(Number::class, $result);
        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('600.5000', $result->toString());
        $this->assertTrue($result->isPositive());
    }

    public function testCanAddIntegerAsImmutable(): void
    {
        $number = new Number('200');
        $result = $number->add(400);

        $this->assertInstanceOf(Number::class, $result);
        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('600.0000', $result->toString());
        $this->assertTrue($result->isPositive());
    }

    public function testCanAddNumberAsImmutable(): void
    {
        $number = new Number('200');
        $result = $number->add(new Number('400'));

        $this->assertInstanceOf(Number::class, $result);
        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('600.0000', $result->toString());
        $this->assertTrue($result->isPositive());
    }

    public function testCannotAddArray(): void
    {
        $number = new Number('200');

        $this->expectException(InvalidNumberInputTypeException::class);
        $number->add([]);
    }

    public function testCannotAddObject(): void
    {
        $number = new Number('200');

        $this->expectException(InvalidNumberInputTypeException::class);
        $number->add(new \stdClass());
    }

    public function testCannotAddBoolean(): void
    {
        $number = new Number('200');

        $this->expectException(InvalidNumberInputTypeException::class);
        $number->add(true);
    }

    public function testCannotAddNull(): void
    {
        $number = new Number('200');

        $this->expectException(InvalidNumberInputTypeException::class);
        $number->add(null);
    }

    public function testCanSubtractNumberAsImmutable(): void
    {
        $number = new Number('200');
        $result = $number->subtract(new Number('50'));

        $this->assertInstanceOf(Number::class, $result);
        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('150.0000', $result->toString());

        // alias sub
        $number = new Number('200');
        $result = $number->sub(new Number('50'));

        $this->assertInstanceOf(Number::class, $result);
        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('150.0000', $result->toString());

        // alias minus
        $number = new Number('200');
        $result = $number->minus(new Number('50'));

        $this->assertInstanceOf(Number::class, $result);
        $this->assertEquals('200.0000', $number->toString());
        $this->assertEquals('150.0000', $result->toString());
    }

    public function testIsPositive(): void
    {
        $this->assertTrue((new Number('200'))->isPositive());
        $this->assertTrue((new Number('1'))->isPositive());

        $this->assertFalse((new Number('200'))->isNegative());
        $this->assertFalse((new Number('1'))->isNegative());
        $this->assertFalse((new Number('0'))->isNegative());
    }

    public function testIsNegative(): void
    {
        $this->assertTrue((new Number('-200'))->isNegative());
        $this->assertTrue((new Number('-1'))->isNegative());

        $this->assertFalse((new Number('-200'))->isPositive());
        $this->assertFalse((new Number('-1'))->isPositive());
    }

    public function testIsZero(): void
    {
        $this->assertTrue((new Number('-0'))->isZero());
        $this->assertTrue((new Number('0'))->isZero());

        $this->assertFalse((new Number('-200'))->isZero());
        $this->assertFalse((new Number('200'))->isZero());
        $this->assertFalse((new Number('-1'))->isZero());
        $this->assertFalse((new Number('1'))->isZero());
        $this->assertFalse((new Number('0.000000085'))->isZero());
    }

    public function testCanTraceByParent(): void
    {
        $five = new Number(5);
        $seven = $five->add(2);
        $fifteen = $seven->add(8);

        $this->assertEquals($five->toString(), '5.0000');
        $this->assertEquals($seven->toString(), '7.0000');
        $this->assertEquals($fifteen->toString(), '15.0000');

        $this->assertEquals($seven->parent()->toString(), '5.0000');
        $this->assertEquals($fifteen->parent()->toString(), '7.0000');
        $this->assertEquals($fifteen->parent()->parent()->toString(), '5.0000');
        $this->assertNull($five->parent());
    }

    public function testCanInitializeMoney(): void
    {
        // Instance initialized by constructor.
        $five = new Money(5, 'EUR');
        $this->assertEquals($five->toString(), '5.0000');
        $this->assertEquals($five->isoCode(), 'EUR');

        // Instance initialized by init().
        $seven = $five->add(2);
        $this->assertEquals($seven->toString(), '7.0000');
        $this->assertEquals($seven->isoCode(), 'EUR');

        $this->assertEquals($seven->parent(), $five);
    }
}
