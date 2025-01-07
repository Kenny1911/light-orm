<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping;

/**
 * @template T of object
 */
interface ClassMetadata
{
    /** @var class-string<T> */
    public string $className { get; }

    /** @var non-empty-array<non-empty-string, FieldMetadata> */
    public array $fields { get; }

    /**
     * @param T $object
     * @param non-empty-string $field
     *
     * @throws InvalidMapping
     */
    public function getFieldValue(object $object, string $field): mixed;

    /**
     * @param T $object
     * @param non-empty-string $field
     *
     * @throws InvalidMapping
     */
    public function setFieldValue(object $object, string $field, mixed $value): void;
}