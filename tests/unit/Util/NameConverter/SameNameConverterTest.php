<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Util\NameConverter;

final class SameNameConverterTest extends NameConverterTestCase
{
    public static function dataClassNameToTableName(): array
    {
        return [
            [self::class, 'SameNameConverterTest'],
        ];
    }

    public static function dataFieldNameToColumnName(): array
    {
        return [
            ['foo', 'foo'],
            ['Foo', 'Foo'],
            ['fooBar', 'fooBar'],
            ['foo_bar', 'foo_bar'],
        ];
    }

    protected function crateNameConverter(): NameConverter
    {
        return new SameNameConverter();
    }
}
