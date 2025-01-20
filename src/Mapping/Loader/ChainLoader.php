<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader;

use Kenny1911\LightOrm\Mapping\ClassMetadata;
use Kenny1911\LightOrm\Mapping\ClassMetadataNotExists;

final readonly class ChainLoader implements Loader
{
    /**
     * @param list<Loader> $loaders
     */
    public function __construct(
        private array $loaders,
    ) {}

    /**
     * @param iterable<Loader> $loaders
     */
    public static function createFromIterable(iterable $loaders): self
    {
        return new self(iterator_to_array($loaders, false));
    }

    public function load(string $className): ClassMetadata
    {
        foreach ($this->loaders as $loader) {
            try {
                return $loader->load($className);
            } catch (ClassMetadataNotExists) {}
        }

        throw ClassMetadataNotExists::create($className);
    }
}
