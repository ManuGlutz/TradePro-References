<?php

namespace Sylius\ReferenceLinking\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Sylius\ReferenceLinking\Entity\ReferenceLinkingPolicy;

final class ReferenceLinkingPolicyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $defaults = [
            'required_accessory' => ReferenceLinkingPolicy::TARGET_GROUP,
            'optional_accessory' => ReferenceLinkingPolicy::TARGET_GROUP,
            'spare_part' => ReferenceLinkingPolicy::TARGET_VARIANT,
            'matching' => ReferenceLinkingPolicy::TARGET_GROUP,
        ];

        foreach ($defaults as $type => $target) {
            $manager->persist(new ReferenceLinkingPolicy($type, $target));
        }

        $manager->flush();
    }
}

