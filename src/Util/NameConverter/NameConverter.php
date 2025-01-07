<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Util\NameConverter;

interface NameConverter
{
    /**
     * @param class-string $className
     *
     * @return non-empty-string
     */
    public function classNameToTableName(string $className): string;

    /**
     * @param non-empty-string $fieldName
     *
     * @return non-empty-string
     */
    public function fieldNameToColumnName(string $fieldName): string;
}
