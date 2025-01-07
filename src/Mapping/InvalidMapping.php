<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping;

final class InvalidMapping extends \LogicException
{
    /**
     * @param class-string $className
     */
    public static function classNotExists(string $className): self
    {
        return new self("Class {$className} not exists.");
    }

    /**
     * @param class-string $className
     * @param non-empty-string $fieldName
     */
    public static function fieldNotExists(string $className, string $fieldName): self
    {
        return new self("Field {$className}::\${$fieldName} not exists.");
    }

    /**
     * @param class-string $className
     */
    public static function noFields(string $className): self
    {
        return new self("Class {$className} doesn't have fields.");
    }

    /**
     * @param class-string $className
     * @param non-empty-string $primaryKey
     */
    public static function invalidPrimaryKey(string $className, string $primaryKey): self
    {
        return new self("Invalid primary key {$primaryKey} in class {$className}.");
    }

    public static function noPrimaryKeys(string $className): self
    {
        return new self("Class {$className} must have at least one primary key.");
    }

    /**
     * @param non-empty-string $type
     */
    public static function invalidColumnType(string $type): self
    {
        return new self("Invalid column type {$type}.");
    }
}
