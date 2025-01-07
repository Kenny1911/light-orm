<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Util\NameConverter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class NameConverterTestCase extends TestCase
{
    /**
     * @param class-string $className
     * @param non-empty-string $expected
     */
    #[DataProvider('dataClassNameToTableName')]
    public function testClassNameToTableName(string $className, string $expected): void
    {
        $this->assertSame(
            $expected,
            $this->crateNameConverter()->classNameToTableName($className),
        );
    }

    /**
     * @return list<array{0: class-string, 1: non-empty-string}>
     */
    abstract public static function dataClassNameToTableName(): array;

    /**
     * @param non-empty-string $fieldName
     * @param non-empty-string $expected
     */
    #[DataProvider('dataFieldNameToColumnName')]
    public function testFieldNameToColumnName(string $fieldName, string $expected): void
    {
        $this->assertSame(
            $expected,
            $this->crateNameConverter()->fieldNameToColumnName($fieldName),
        );
    }

    /**
     * @return list<array{0: non-empty-string, 1: non-empty-string}>
     */
    abstract public static function dataFieldNameToColumnName(): array;

    abstract protected function crateNameConverter(): NameConverter;
}
