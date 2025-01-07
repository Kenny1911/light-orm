<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub;

use Kenny1911\LightOrm\Mapping\Attribute as ORM;

#[ORM\Entity]
final class EntityWithCompositePrimaryKey
{
    #[ORM\Id]
    #[ORM\Field(type: 'integer')]
    public int $id1;

    #[ORM\Id]
    #[ORM\Field(type: 'integer')]
    public int $id2;

    #[ORM\Field(type: 'string')]
    public string $title;

    public function __construct(int $id1, int $id2, string $title)
    {
        $this->id1 = $id1;
        $this->id2 = $id2;
        $this->title = $title;
    }
}
