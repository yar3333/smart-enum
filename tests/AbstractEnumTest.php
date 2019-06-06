<?php

namespace SmartEnumTests;

use PHPUnit\Framework\TestCase;
use SmartEnum\EnumException;

class AbstractEnumTest extends TestCase
{
    public function testSuccess(): void
    {
        $enum = DummyEnum::ONE();

        $this->assertInstanceOf(DummyEnum::class, $enum);

        self::assertSame('ONE', (string)$enum);
        self::assertSame('ONE', $enum->getName());
    }

    public function testGetNames(): void
    {
        $actual = DummyExtendingEnum::getNames();
        asort($actual);

        $expected = [
            'ONE',
            'TWO',
            'THREE',
            'FOUR',
        ];
        sort($expected);
        sort($actual);
        self::assertSame($expected, $actual);
    }

    public function testGetValues(): void
    {
        $actual = DummyExtendingEnum::getValues();
        asort($actual);

        $expected = [
            1,
            2,
            3,
            4,
        ];
        sort($expected);
        sort($actual);
        self::assertSame($expected, $actual);
    }

    public function testMagicStaticConstructorThrowsBadMethodCallException(): void
    {
        $this->expectException(EnumException::class);
        $this->expectExceptionMessage('Unknown member "THREE" for enum SmartEnumTests\\DummyEnum');

        /** @noinspection PhpUndefinedMethodInspection */
        DummyEnum::THREE();
    }

    public function testStaticConstructorEnsuresStrictEquality(): void
    {
        $first = DummyEnum::ONE();
        $second = DummyEnum::ONE();

        self::assertSame($first, $second);
    }

    public function testInheritanceKeepsStrictEquality(): void
    {
        $first = DummyEnum::ONE();
        $second = DummyExtendingEnum::ONE();

        self::assertSame($first, $second);
    }

    public function testStaticConstructorAllowsInternalFunctions(): void
    {
        $haystack = [DummyEnum::ONE(), DummyExtendingEnum::TWO()];
        $needle = DummyExtendingEnum::TWO();

        self::assertContains($needle, $haystack);
    }

    public function testDivergenceBreaksEquality(): void
    {
        $first = DummyDivergedEnum::THREE();
        $second = DummyExtendingEnum::THREE();

        self::assertNotSame($first, $second);
        self::assertNotEquals($first, $second);
    }

    public function testFromName(): void
    {
        self::assertSame(DummyEnum::ONE(), DummyEnum::fromName('ONE'));
    }

    public function testFromNameThrowsNoticeForNonCanonicalNames(): void
    {
        $this->expectException(EnumException::class);

        DummyEnum::fromName('One');
    }
}
