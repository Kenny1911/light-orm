<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping;

/**
 * @template T of object
 * @implements ClassMetadata<T>
 */
final readonly class EntityClassMetadata implements ClassMetadata
{
    /** @var non-empty-array<non-empty-string, FieldMetadata> */
    public array $fields;

    /**
     * @param class-string<T> $className
     * @param non-empty-string $tableName
     * @param non-empty-list<non-empty-string> $identifiers
     * @param non-empty-list<FieldMetadata> $fields
     *
     * @throws InvalidMapping
     */
    public function __construct(
        public string $className,
        public string $tableName,
        array $fields,
        public array $identifiers,
    ) {
        // Check class name
        if (false === class_exists($this->className)) {
            throw InvalidMapping::classNotExists($this->className);
        }

        // Check and map fields
        $fieldsMap = [];

        foreach ($fields as $field) {
            if (!property_exists($this->className, $field->fieldName)) {
                throw InvalidMapping::fieldNotExists($this->className, $field->fieldName);
            }

            $fieldsMap[$field->fieldName] = $field;
        }

        $this->fields = $fieldsMap;

        // Check primary keys
        foreach ($this->identifiers as $primaryKey) {
            if (!isset($this->fields[$primaryKey])) {
                throw InvalidMapping::invalidPrimaryKey($this->className, $primaryKey);
            }
        }
    }

    /**
     * @param T $object
     * @return non-empty-array<non-empty-string, mixed>
     */
    public function getIdentifierValue(object $object): array
    {
        $id = [];

        foreach ($this->identifiers as $fieldName) {
            $id[$fieldName] = $this->getFieldValue($object, $fieldName);
        }

        return $id;
    }

    public function isSingleIdentifier(): bool
    {
        return 1 === count($this->identifiers);
    }

    /**
     * @param T $object
     */
    public function getSingleIdentifierValue(object $object): mixed
    {
        if (!$this->isSingleIdentifier()) {
            throw new \LogicException(sprintf('Identifier of class %s is not single value.', $object::class));
        }

        return array_values($this->getIdentifierValue($object))[0];
    }

    public function getFieldValue(object $object, string $field): mixed
    {
        try {
            return new \ReflectionProperty($object::class, $field)->getRawValue($object);
        } catch (\ReflectionException) {
            throw InvalidMapping::fieldNotExists($object::class, $field);
        }
    }

    /**
     * @param T $object
     * @param non-empty-string $field
     */
    public function setFieldValue(object $object, string $field, mixed $value): void
    {
        try {
            new \ReflectionProperty($object::class, $field)->setRawValue($object, $value);
        } catch (\ReflectionException) {
            throw InvalidMapping::fieldNotExists($object::class, $field);
        }
    }
}
