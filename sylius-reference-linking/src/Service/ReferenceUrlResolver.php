<?php

namespace Sylius\ReferenceLinking\Service;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\ReferenceLinking\Entity\ReferenceLinkingPolicy;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ReferenceUrlResolver
{
    public const REFERENCE_TYPES = ["required_accessory","optional_accessory","spare_part","matching"];

    private ProductVariantRepositoryInterface $variantRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ProductVariantRepositoryInterface $variantRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->variantRepository = $variantRepository;
        $this->entityManager = $entityManager;
    }

    public function resolveUrl(string $referenceType, string $variantCode): ?string
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $this->variantRepository->findOneBy(['code' => $variantCode]);
        if ($variant === null) {
            return null;
        }

        $product = $variant->getProduct();
        $policy = $this->entityManager->find(ReferenceLinkingPolicy::class, $referenceType);
        $target = $policy?->getTargetLevel() ?? ReferenceLinkingPolicy::TARGET_GROUP;

        $code = $product->getCode();
        if ($target === ReferenceLinkingPolicy::TARGET_GROUP) {
            return sprintf('/de_CH/products/%s', $code);
        }

        return sprintf('/de_CH/products/%s?artnr=%s', $code, $variantCode);
    }

    /**
     * @param string[] $variantCodes
     * @return array<string,string|null>
     */
    public function resolveUrls(string $referenceType, array $variantCodes): array
    {
        $results = [];
        foreach ($variantCodes as $code) {
            $results[$code] = $this->resolveUrl($referenceType, $code);
        }

        return $results;
    }
}

