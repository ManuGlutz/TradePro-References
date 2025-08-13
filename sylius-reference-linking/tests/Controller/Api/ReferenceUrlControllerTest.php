<?php

namespace Sylius\ReferenceLinking\Tests\Controller\Api;

use PHPUnit\Framework\TestCase;
use Sylius\ReferenceLinking\Controller\Api\ReferenceUrlController;
use Sylius\ReferenceLinking\Service\ReferenceUrlResolver;
use Symfony\Component\HttpFoundation\Request;

final class ReferenceUrlControllerTest extends TestCase
{
    public function testResolveAction(): void
    {
        $resolver = $this->createMock(ReferenceUrlResolver::class);
        $resolver->method('resolveUrl')->willReturn('/foo');
        $controller = new ReferenceUrlController($resolver);
        $request = new Request(['type' => 't', 'sku' => 's']);
        $response = $controller->resolveAction($request);
        $data = json_decode($response->getContent(), true);
        $this->assertSame('/foo', $data['url']);
    }

    public function testResolveBatchAction(): void
    {
        $resolver = $this->createMock(ReferenceUrlResolver::class);
        $resolver->method('resolveUrls')->willReturn(['a' => '/a', 'b' => null]);
        $controller = new ReferenceUrlController($resolver);
        $request = new Request([], [], [], [], [], [], json_encode(['type' => 'x', 'skus' => ['a','b']]));
        $response = $controller->resolveBatchAction($request);
        $data = json_decode($response->getContent(), true);
        $this->assertSame('/a', $data['results']['a']);
        $this->assertArrayHasKey('b', $data['results']);
    }
}

