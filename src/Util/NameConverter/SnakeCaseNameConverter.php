<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Util\NameConverter;

final readonly class SnakeCaseNameConverter implements NameConverter
{
    /**
     * @throws \ReflectionException
     */
    public function classNameToTableName(string $className): string
    {
        /** @var non-empty-string */
        return self::snakeCase(new \ReflectionClass($className)->getShortName());
    }

    public function fieldNameToColumnName(string $fieldName): string
    {
        /** @var non-empty-string */
        return self::snakeCase($fieldName);
    }

    private static function snakeCase(string $value): string
    {
        return ltrim(
            strtolower(
                (string) preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $value),
            ),
            '_',
        );
    }
}