<?php

namespace Sylius\ReferenceLinking\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\ReferenceLinking\Entity\ReferenceLinkingPolicy;
use Sylius\ReferenceLinking\Service\ReferenceUrlResolver;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ReferenceUrlResolverTest extends TestCase
{
    public function testResolvesGroupUrl(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn('GROUP1');

        $variant = $this->createMock(ProductVariantInterface::class);
        $variant->method('getProduct')->willReturn($product);

        $variantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $variantRepository->method('findOneBy')->with(['code' => 'SKU1'])->willReturn($variant);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('find')->willReturn(new ReferenceLinkingPolicy('required_accessory', ReferenceLinkingPolicy::TARGET_GROUP));

        $resolver = new ReferenceUrlResolver($variantRepository, $entityManager);
        $this->assertSame('/de_CH/products/GROUP1', $resolver->resolveUrl('required_accessory', 'SKU1'));
    }

    public function testResolvesVariantUrl(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn('GROUP2');
        $variant = $this->createMock(ProductVariantInterface::class);
        $variant->method('getProduct')->willReturn($product);
        $variantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $variantRepository->method('findOneBy')->willReturn($variant);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('find')->willReturn(new ReferenceLinkingPolicy('spare_part', ReferenceLinkingPolicy::TARGET_VARIANT));
        $resolver = new ReferenceUrlResolver($variantRepository, $entityManager);
        $this->assertSame('/de_CH/products/GROUP2?artnr=SKU2', $resolver->resolveUrl('spare_part', 'SKU2'));
    }

    public function testReturnsNullWhenVariantMissing(): void
    {
        $variantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $variantRepository->method('findOneBy')->willReturn(null);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $resolver = new ReferenceUrlResolver($variantRepository, $entityManager);
        $this->assertNull($resolver->resolveUrl('required_accessory', 'UNKNOWN'));
    }
}

