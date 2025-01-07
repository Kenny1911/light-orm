<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Util\NameConverter;

final readonly class SameNameConverter implements NameConverter
{
    /**
     * @throws \ReflectionException
     */
    public function classNameToTableName(string $className): string
    {
        /** @var non-empty-string */
        return new \ReflectionClass($className)->getShortName();
    }

    public function fieldNameToColumnName(string $fieldName): string
    {
        return $fieldName;
    }
}