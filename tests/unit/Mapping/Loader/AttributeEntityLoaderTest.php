<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader;

use Kenny1911\LightOrm\Mapping\ClassMetadataNotExists;
use Kenny1911\LightOrm\Mapping\FieldMetadata;
use Kenny1911\LightOrm\Mapping\InvalidMapping;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\Entity;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\EntityIdWithoutField;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\EntityWithCompositePrimaryKey;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\EntityWithoutFields;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\EntityWithoutPrimaryKey;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\EntityWithPromotedProperties;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\EntityWithSpecifiedTableNameAndColumnName;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\SomeClass;
use Kenny1911\LightOrm\Util\NameConverter\SnakeCaseNameConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AttributeEntityLoaderTest extends TestCase
{
    /**
     * @param class-string $className
     */
    #[DataProvider('dataIsSupported')]
    public function testIsSupported(string $className, bool $expected): void
    {
        $this->assertSame($expected, AttributeEntityLoader::isSupported($className));
    }

    /**
     * @return array<array{0: class-string, 1: bool}>
     */
    public static function dataIsSupported(): array
    {
        return [
            'entity' => [Entity::class, true],
            'entity with promoted properties' => [EntityWithPromotedProperties::class, true],
            'entity with specified table and column name' => [EntityWithSpecifiedTableNameAndColumnName::class, true],
            'entity with composite primary key' => [EntityWithCompositePrimaryKey::class, true],
            'entity without fields' => [EntityWithoutFields::class, true],
            'entity without primary key' => [EntityWithoutPrimaryKey::class, true],
            'some class' => [SomeClass::class, false],
        ];
    }

    /**
     * @return array<array{0: class-string, 1: bool}>
     */
    public static function dataIsSupportedEntity(): array
    {
        return self::dataIsSupported();
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadThrowClassMetadataNotExists(): void
    {
        $this->expectException(ClassMetadataNotExists::class);

        new AttributeEntityLoader()->load(SomeClass::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoad(): void
    {
        $metadata = new AttributeEntityLoader()->load(Entity::class);

        // Assert entity
        $this->assertSame(Entity::class, $metadata->className);
        $this->assertSame('Entity', $metadata->tableName);

        // Assert fields
        $this->assertCount(3, $metadata->fields);
        $this->assertSame(['id', 'title', 'description'], array_keys($metadata->fields));
        $this->assertFieldMetadata($metadata->fields['id'], 'id', 'id', 'integer', false, []);
        $this->assertFieldMetadata($metadata->fields['title'], 'title', 'title', 'string', false, []);
        $this->assertFieldMetadata($metadata->fields['description'], 'description', 'description', 'text', true, ['foo' => 'bar']);

        // Assert primary keys
        $this->assertSame(['id'], $metadata->identifiers);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadWithPromotedProperties(): void
    {
        $metadata = new AttributeEntityLoader()->load(EntityWithPromotedProperties::class);

        // Assert entity
        $this->assertSame(EntityWithPromotedProperties::class, $metadata->className);
        $this->assertSame('EntityWithPromotedProperties', $metadata->tableName);

        // Assert fields
        $this->assertCount(3, $metadata->fields);
        $this->assertSame(['id', 'title', 'description'], array_keys($metadata->fields));
        $this->assertFieldMetadata($metadata->fields['id'], 'id', 'id', 'integer', false, []);
        $this->assertFieldMetadata($metadata->fields['title'], 'title', 'title', 'string', false, []);
        $this->assertFieldMetadata($metadata->fields['description'], 'description', 'description', 'text', true, ['foo' => 'bar']);

        // Assert primary keys
        $this->assertSame(['id'], $metadata->identifiers);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadByClassNameWithSpecifiedTableNameAndColumnName(): void
    {
        $metadata = new AttributeEntityLoader()->load(EntityWithSpecifiedTableNameAndColumnName::class);

        // Assert entity
        $this->assertSame(EntityWithSpecifiedTableNameAndColumnName::class, $metadata->className);
        $this->assertSame('entity', $metadata->tableName);

        // Assert fields
        $this->assertCount(1, $metadata->fields);
        $this->assertSame(['id'], array_keys($metadata->fields));
        $this->assertFieldMetadata($metadata->fields['id'], 'id', 'pk', 'integer', false, []);

        // Assert primary keys
        $this->assertSame(['id'], $metadata->identifiers);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadWithCompositePrimaryKey(): void
    {
        $metadata = new AttributeEntityLoader(new SnakeCaseNameConverter())->load(EntityWithCompositePrimaryKey::class);

        // Assert entity
        $this->assertSame(EntityWithCompositePrimaryKey::class, $metadata->className);
        $this->assertSame('entity_with_composite_primary_key', $metadata->tableName);

        // Assert fields
        $this->assertCount(3, $metadata->fields);
        $this->assertSame(['id1', 'id2', 'title'], array_keys($metadata->fields));
        $this->assertFieldMetadata($metadata->fields['id1'], 'id1', 'id1', 'integer', false, []);
        $this->assertFieldMetadata($metadata->fields['id2'], 'id2', 'id2', 'integer', false, []);
        $this->assertFieldMetadata($metadata->fields['title'], 'title', 'title', 'string', false, []);

        // Assert primary keys
        $this->assertSame(['id1', 'id2'], $metadata->identifiers);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadNoMetadata(): void
    {
        $this->expectException(ClassMetadataNotExists::class);

        new AttributeEntityLoader()->load(SomeClass::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadThrowNoFields(): void
    {
        $this->expectException(InvalidMapping::class);
        $this->expectExceptionMessage(sprintf('Class %s doesn\'t have fields.', EntityWithoutFields::class));

        new AttributeEntityLoader()->load(EntityWithoutFields::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadThrowNoPrimaryKey(): void
    {
        $this->expectException(InvalidMapping::class);
        $this->expectExceptionMessage(sprintf('Class %s must have at least one primary key.', EntityWithoutPrimaryKey::class));

        new AttributeEntityLoader()->load(EntityWithoutPrimaryKey::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadThrowInvalidPrimaryKey(): void
    {
        $this->expectException(InvalidMapping::class);
        $this->expectExceptionMessage(sprintf('Invalid primary key id in class %s.', EntityIdWithoutField::class));

        new AttributeEntityLoader()->load(EntityIdWithoutField::class);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function assertFieldMetadata(FieldMetadata $field, string $fieldName, string $columnName, string $type, bool $nullable, array $options): void
    {
        $this->assertSame($fieldName, $field->fieldName);
        $this->assertSame($columnName, $field->columnName);
        $this->assertSame($type, $field->type);
        $this->assertSame($nullable, $field->nullable);
        $this->assertSame($options, $field->options);
    }
}
