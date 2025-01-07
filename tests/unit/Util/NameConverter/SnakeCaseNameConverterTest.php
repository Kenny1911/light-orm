<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Util\NameConverter;

final class SnakeCaseNameConverterTest extends NameConverterTestCase
{
    public static function dataClassNameToTableName(): array
    {
        return [
            [self::class, 'snake_case_name_converter_test']
        ];
    }

    public static function dataFieldNameToColumnName(): array
    {
        return [
            ['foo', 'foo'],
            ['Foo', 'foo'],
            ['fooBar', 'foo_bar'],
            ['foo_bar', 'foo_bar'],
            ['FOO', 'foo'],
            ['fooBAR', 'foo_bar'],
        ];
    }

    protected function crateNameConverter(): NameConverter
    {
        return new SnakeCaseNameConverter();
    }
}
