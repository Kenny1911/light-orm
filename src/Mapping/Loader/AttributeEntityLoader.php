<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader;

use Kenny1911\LightOrm\Mapping\Attribute as ORM;
use Kenny1911\LightOrm\Mapping\ClassMetadataNotExists;
use Kenny1911\LightOrm\Mapping\EntityClassMetadata;
use Kenny1911\LightOrm\Mapping\FieldMetadata;
use Kenny1911\LightOrm\Mapping\InvalidMapping;
use Kenny1911\LightOrm\Util\NameConverter\NameConverter;
use Kenny1911\LightOrm\Util\NameConverter\SameNameConverter;

final readonly class AttributeEntityLoader implements Loader
{
    /**
     * @param class-string $className
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function isSupported(string $className): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return count(new \ReflectionClass($className)->getAttributes(ORM\Entity::class)) > 0;
    }

    public function __construct(
        private NameConverter $nameConverter = new SameNameConverter(),
    ) {}

    /**
     * @template T of object
     * @param class-string<T> $className
     * @return EntityClassMetadata<T>
     *
     * @throws \ReflectionException
     */
    public function load(string $className): EntityClassMetadata
    {
        if (!self::isSupported($className)) {
            throw ClassMetadataNotExists::create($className);
        }

        $entity = $this->loadEntityAttribute($className);
        $fields = $this->loadFields($className);
        $identifiers = $this->loadIdentifiers($className);

        return new EntityClassMetadata(
            className: $className,
            tableName: $entity->tableName ?? $this->nameConverter->classNameToTableName($className),
            fields: array_map(
                fn(string $fieldName, ORM\Field $field): FieldMetadata => new FieldMetadata(
                    fieldName: $fieldName,
                    columnName: $field->columnName ?? $this->nameConverter->fieldNameToColumnName($fieldName),
                    type: $field->type,
                    nullable: $field->nullable,
                    options: $field->options,
                ),
                array_keys($fields),
                $fields,
            ),
            identifiers: $identifiers,
        );
    }

    /**
     * @param class-string $className
     *
     * @throws \ReflectionException
     */
    private function loadEntityAttribute(string $className): ORM\Entity
    {
        $attributes = new \ReflectionClass($className)->getAttributes(ORM\Entity::class);

        if (0 === count($attributes)) {
            throw new InvalidMapping(sprintf('Class %s doesn\'t have %s attribute.', $className, ORM\Entity::class));
        }

        return $attributes[0]->newInstance();
    }

    /**
     * @param class-string $className
     *
     * @return non-empty-array<non-empty-string, ORM\Field>
     *
     * @throws \ReflectionException
     */
    private function loadFields(string $className): array
    {
        $fields = [];

        foreach ($this->getClassProperties($className) as $refProperty) {
            $attributes = $refProperty->getAttributes(ORM\Field::class);

            if (0 === count($attributes)) {
                continue;
            }

            $field = $attributes[0]->newInstance();
            /** @var non-empty-string $fieldName */
            $fieldName = $refProperty->name;
            $fields[$fieldName] = $field;
        }

        if (0 === count($fields)) {
            throw InvalidMapping::noFields($className);
        }

        return $fields;
    }

    /**
     * @param class-string $className
     *
     * @return non-empty-list<non-empty-string>
     *
     * @throws \ReflectionException
     */
    private function loadIdentifiers(string $className): array
    {
        $ids = [];

        foreach ($this->getClassProperties($className) as $refProperty) {
            $attributes = $refProperty->getAttributes(ORM\Id::class);

            if (0 === count($attributes)) {
                continue;
            }

            /** @var non-empty-string $id */
            $id = $refProperty->name;
            $ids[] = $id;
        }

        if (0 === count($ids)) {
            throw InvalidMapping::noPrimaryKeys($className);
        }

        return $ids;
    }

    /**
     * @param class-string $className
     *
     * @return \Generator<\ReflectionProperty>
     *
     * @throws \ReflectionException
     */
    private function getClassProperties(string $className): \Generator
    {
        foreach (new \ReflectionClass($className)->getProperties() as $refProperty) {
            if ($refProperty->isStatic() || $refProperty->isVirtual()) {
                continue;
            }

            yield $refProperty;
        }
    }
}
