<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping;

final readonly class FieldMetadata
{
    /**
     * @param non-empty-string $fieldName
     * @param non-empty-string $columnName
     * @param non-empty-string $type
     * @param bool $nullable
     * @param array<string, mixed> $options
     */
    public function __construct(
        public string $fieldName,
        public string $columnName,
        public string $type,
        public bool $nullable,
        public array $options,
    ) {}
}
