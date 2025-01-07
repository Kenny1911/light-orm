<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub;

use Kenny1911\LightOrm\Mapping\Attribute as ORM;

#[ORM\Entity(tableName: 'entity')]
final class EntityWithSpecifiedTableNameAndColumnName
{
    #[ORM\Id]
    #[ORM\Field(type: 'integer', columnName: 'pk')]
    public int $id = 0;
}
