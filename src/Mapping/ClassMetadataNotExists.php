<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping;

final class ClassMetadataNotExists extends \LogicException
{
    /**
     * @param class-string $className
     */
    public static function create(string $className): self
    {
        return new self("Metadata of class {$className} not exists.");
    }
}
