<?php

namespace Sylius\ReferenceLinking\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'app_reference_linking_policy')]
class ReferenceLinkingPolicy
{
    public const TARGET_GROUP = 'group';
    public const TARGET_VARIANT = 'variant';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $referenceType;

    #[ORM\Column(type: 'string', length: 10)]
    private string $targetLevel = self::TARGET_GROUP;

    public function __construct(string $referenceType, string $targetLevel)
    {
        $this->referenceType = $referenceType;
        $this->targetLevel = $targetLevel;
    }

    public function getReferenceType(): string
    {
        return $this->referenceType;
    }

    public function getTargetLevel(): string
    {
        return $this->targetLevel;
    }

    public function setTargetLevel(string $targetLevel): void
    {
        $this->targetLevel = $targetLevel;
    }
}

