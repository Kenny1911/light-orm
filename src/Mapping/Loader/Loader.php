<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader;

use Kenny1911\LightOrm\Mapping\ClassMetadata;
use Kenny1911\LightOrm\Mapping\ClassMetadataNotExists;
use Kenny1911\LightOrm\Mapping\InvalidMapping;

interface Loader
{
    /**
     * @template T of object
     * @param class-string<T> $className
     * @return ClassMetadata<T>
     *
     * @throws ClassMetadataNotExists
     * @throws InvalidMapping
     */
    public function load(string $className): ClassMetadata;
}
