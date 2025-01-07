<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub;

use Kenny1911\LightOrm\Mapping\Attribute as ORM;

#[ORM\Entity]
final class EntityIdWithoutField
{
    #[ORM\Id]
    public int $id = 0;

    #[ORM\Field(type: 'string')]
    public string $someField = '';
}
