<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class Field
{
    /**
     * @param non-empty-string $type
     * @param non-empty-string|null $columnName
     * @param array<string, mixed> $options
     */
    public function __construct(
        public string $type,
        public ?string $columnName = null,
        public bool $nullable = false,
        public array $options = [],
    ) {}
}