<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping;

use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoader;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\Entity;
use Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub\EntityWithCompositePrimaryKey;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EntityClassMetadataTest extends TestCase
{
    public function testCreate(): void
    {
        $metadata = new EntityClassMetadata(
            className: Entity::class,
            tableName: 'entity',
            fields: [
                $fieldId = new FieldMetadata(
                    fieldName: 'id',
                    columnName: 'id',
                    type: 'integer',
                    nullable: false,
                    options: [],
                ),
                $fieldTitle = new FieldMetadata(
                    fieldName: 'title',
                    columnName: 'title',
                    type: 'string',
                    nullable: false,
                    options: [],
                ),
            ],
            identifiers: ['id']
        );

        $this->assertSame(Entity::class, $metadata->className);
        $this->assertSame('entity', $metadata->tableName);
        $this->assertSame($fieldId, $metadata->fields['id']);
        $this->assertSame($fieldTitle, $metadata->fields['title']);
        $this->assertSame(['id'], $metadata->identifiers);
    }

    public function testCreateClassNotExists(): void
    {
        $this->expectException(InvalidMapping::class);
        $this->expectExceptionMessage('Class SomeInvalidClass not exists.');

        new EntityClassMetadata(
            /** @phpstan-ignore-next-line */
            className: 'SomeInvalidClass',
            tableName: 'entity',
            fields: [
                new FieldMetadata(
                    fieldName: 'id',
                    columnName: 'id',
                    type: 'integer',
                    nullable: false,
                    options: [],
                ),
            ],
            identifiers: ['id']
        );
    }

    public function testCreateFieldNotExists(): void
    {
        $this->expectException(InvalidMapping::class);
        $this->expectExceptionMessage(sprintf('Field %s::$invalid not exists.', Entity::class));

        new EntityClassMetadata(
            className: Entity::class,
            tableName: 'entity',
            fields: [
                new FieldMetadata(
                    fieldName: 'invalid',
                    columnName: 'id',
                    type: 'integer',
                    nullable: false,
                    options: [],
                ),
            ],
            identifiers: ['invalid']
        );
    }

    public function testCreateInvalidPrimaryKey(): void
    {
        $this->expectException(InvalidMapping::class);
        $this->expectExceptionMessage(sprintf('Invalid primary key invalid in class %s.', Entity::class));

        new EntityClassMetadata(
            className: Entity::class,
            tableName: 'entity',
            fields: [
                new FieldMetadata(
                    fieldName: 'id',
                    columnName: 'id',
                    type: 'integer',
                    nullable: false,
                    options: [],
                ),
            ],
            identifiers: ['invalid']
        );
    }

    /**
     * @param non-empty-array<non-empty-string, mixed> $id
     *
     * @throws \ReflectionException
     */
    #[DataProvider('dataGetIdentifierValue')]
    public function testGetIdentifierValue(object $entity, array $id, bool $isSingle): void
    {
        $metadata = new AttributeEntityLoader()->load($entity::class);
        $this->assertSame($id, $metadata->getIdentifierValue($entity));
        $this->assertSame($isSingle, $metadata->isSingleIdentifier());
    }

    /**
     * @return array<non-empty-string, array{0: object, 1: non-empty-array<non-empty-string, mixed>, bool}>
     */
    public static function dataGetIdentifierValue(): array
    {
        return [
            'single identifier' => [new Entity(9, ''), ['id' => 9], true],
            'composite identifier' => [new EntityWithCompositePrimaryKey(3, 8, ''), ['id1' => 3, 'id2' => 8], false],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetSingleIdentifierValue(): void
    {
        $metadata = new AttributeEntityLoader()->load(Entity::class);
        $entity = new Entity(1, '');

        $this->assertSame(1, $metadata->getSingleIdentifierValue($entity));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetSingleIdentifierValueThrowIsNotSingleValue(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(sprintf('Identifier of class %s is not single value.', EntityWithCompositePrimaryKey::class));

        $metadata = new AttributeEntityLoader()->load(EntityWithCompositePrimaryKey::class);
        $entity = new EntityWithCompositePrimaryKey(1,2, '');

        $metadata->getSingleIdentifierValue($entity);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldValue(): void
    {
        $metadata = new AttributeEntityLoader()->load(Entity::class);
        $entity = new Entity(1,'Title');

        $this->assertSame('Title', $metadata->getFieldValue($entity, 'title'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldValueThrowFieldNotExists(): void
    {
        $this->expectException(InvalidMapping::class);
        $this->expectExceptionMessage(sprintf('Field %s::$invalid not exists.', Entity::class));

        $metadata = new AttributeEntityLoader()->load(Entity::class);
        $entity = new Entity(1,'Title');

        $metadata->getFieldValue($entity, 'invalid');
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetFieldValue(): void
    {
        $metadata = new AttributeEntityLoader()->load(Entity::class);
        $entity = new Entity(1,'Title');

        $metadata->setFieldValue($entity, 'title', 'New Title');
        $this->assertSame('New Title', $entity->title);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetFieldValueFieldNotExists(): void
    {
        $this->expectException(InvalidMapping::class);
        $this->expectExceptionMessage(sprintf('Field %s::$invalid not exists.', Entity::class));

        $metadata = new AttributeEntityLoader()->load(Entity::class);
        $entity = new Entity(1,'Title');

        $metadata->setFieldValue($entity, 'invalid', null);
    }
}
