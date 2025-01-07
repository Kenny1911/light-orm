<?php

declare(strict_types=1);

namespace Kenny1911\LightOrm\Mapping\Loader\AttributeEntityLoaderStub;

use Kenny1911\LightOrm\Mapping\Attribute as ORM;

#[ORM\Entity]
final class Entity
{
    #[ORM\Id]
    #[ORM\Field(type: 'integer')]
    public int $id;

    #[ORM\Field(type: 'string')]
    public string $title;

    #[ORM\Field(type: 'text', nullable: true, options: ['foo' => 'bar'])]
    public ?string $description = null;

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
