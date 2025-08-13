<?php

namespace Sylius\ReferenceLinking\Tests\Controller\Product;

use PHPUnit\Framework\TestCase;
use Sylius\ReferenceLinking\Controller\Product\ShowController;
use Symfony\Component\HttpFoundation\Request;

final class ShowControllerTest extends TestCase
{
    public function testSetsCanonicalHeader(): void
    {
        $controller = new ShowController();
        $request = new Request(['artnr' => 'SKU1']);
        $response = $controller($request, 'de_CH', 'GROUP1');
        $this->assertTrue($response->headers->has('Link'));
        $this->assertStringContainsString('/de_CH/products/GROUP1', $response->headers->get('Link'));
    }
}

