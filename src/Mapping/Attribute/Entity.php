<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class Entity
{
    /**
     * @param non-empty-string|null $tableName
     */
    public function __construct(
        public ?string $tableName = null,
    ) {}
}