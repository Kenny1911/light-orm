<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader;

use Kenny1911\LightOrm\Mapping\ClassMetadata;
use Kenny1911\LightOrm\Mapping\ClassMetadataNotExists;
use Kenny1911\LightOrm\Mapping\InvalidMapping;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ChainLoaderTest extends TestCase
{
    public function testCreateFromIterableListArray(): void
    {
        $loader1 = $this->createLoader([]);
        $loader2 = $this->createLoader([]);

        $this->assertCreateFromIterable([$loader1, $loader2], [$loader1, $loader2]);
    }

    public function testCreateFromIterableAssociativeArray(): void
    {
        $loader1 = $this->createLoader([]);
        $loader2 = $this->createLoader([]);

        $this->assertCreateFromIterable([$loader1, $loader2], ['a' => $loader1, 'b' => $loader2]);
    }

    public function testCreateFromIterableIterator(): void
    {
        $loader1 = $this->createLoader([]);
        $loader2 = $this->createLoader([]);
        $iterator = new \ArrayIterator([$loader1, $loader2]);

        $this->assertCreateFromIterable([$loader1, $loader2], $iterator);
    }

    public function testCreateFromIterableIteratorWithKeys(): void
    {
        $loader1 = $this->createLoader([]);
        $loader2 = $this->createLoader([]);
        $iterator = new \ArrayIterator(['a' => $loader1, 'b' => $loader2]);

        $this->assertCreateFromIterable([$loader1, $loader2], $iterator);
    }

    public function testCreateFromIterableGenerator(): void
    {
        $loader1 = $this->createLoader([]);
        $loader2 = $this->createLoader([]);

        $generator = function () use ($loader1, $loader2) {
            yield $loader1;
            yield $loader2;
        };

        $this->assertCreateFromIterable([$loader1, $loader2], $generator());
    }

    public function testCreateFromIterableGeneratorWithKeys(): void
    {
        $loader1 = $this->createLoader([]);
        $loader2 = $this->createLoader([]);

        $generator = function () use ($loader1, $loader2) {
            yield 'a' => $loader1;
            yield 'b' => $loader2;
        };

        $this->assertCreateFromIterable([$loader1, $loader2], $generator());
    }

    public function testLoad(): void
    {
        $loader = new ChainLoader([
            $this->createLoader([ChainLoaderTestEntity1::class, ChainLoaderTestEntity2::class]),
            $this->createLoader([ChainLoaderTestEntity3::class]),
        ]);

        $this->assertInstanceOf(ClassMetadata::class, $loader->load(ChainLoaderTestEntity1::class));
        $this->assertInstanceOf(ClassMetadata::class, $loader->load(ChainLoaderTestEntity2::class));
        $this->assertInstanceOf(ClassMetadata::class, $loader->load(ChainLoaderTestEntity3::class));
    }

    public function testLoadClassMetadataNotExists(): void
    {
        $this->expectException(ClassMetadataNotExists::class);

        $loader = new ChainLoader([
            $this->createLoader([ChainLoaderTestEntity1::class]),
            $this->createLoader([ChainLoaderTestEntity2::class]),
        ]);

        $loader->load(ChainLoaderTestEntity3::class);
    }

    public function testLoadNoLoaders(): void
    {
        $this->expectException(ClassMetadataNotExists::class);

        $loader = new ChainLoader([]);

        $loader->load(ChainLoaderTestEntity3::class);
    }

    /**
     * @param list<class-string> $supportedClassNames
     */
    private function createLoader(array $supportedClassNames): Loader
    {
        /** @var ClassMetadata<object>&MockObject $classMetadata */
        $classMetadata = $this->getMockBuilder(ClassMetadata::class)->getMock();

        return new readonly class($supportedClassNames, $classMetadata) implements Loader
        {
            /**
             * @param list<class-string> $supportedClassNames
             * @param ClassMetadata<object> $classMetadata
             */
            public function __construct(
                private array $supportedClassNames,
                private ClassMetadata $classMetadata,
            ) {}

            /**
             * @template T of object
             * @param class-string<T> $className
             * @return ClassMetadata<T>
             */
            public function load(string $className): ClassMetadata
            {
                if (in_array($className, $this->supportedClassNames, true)) {
                    /** @var ClassMetadata<T> */
                    return $this->classMetadata;
                }

                throw ClassMetadataNotExists::create($className);
            }
        };
    }

    /**
     * @param list<Loader> $expected
     * @param iterable<Loader> $iterator
     */
    private function assertCreateFromIterable(array $expected, iterable $iterator): void
    {
        $this->assertSame(
            $expected,
            new \ReflectionProperty(ChainLoader::class, 'loaders')
                ->getRawValue(ChainLoader::createFromIterable($iterator))
            ,
        );
    }
}

final readonly class ChainLoaderTestEntity1 {}
final readonly class ChainLoaderTestEntity2 {}
final readonly class ChainLoaderTestEntity3 {}
