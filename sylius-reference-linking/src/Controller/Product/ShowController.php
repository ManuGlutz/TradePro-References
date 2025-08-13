<?php

namespace Sylius\ReferenceLinking\Controller\Product;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/{_locale}/products/{code}', requirements: ['_locale' => 'de_CH|de_DE|en_GB|fr_CH'], name: 'sylius_shop_product_show')]
final class ShowController extends AbstractController
{
    public function __invoke(Request $request, string $_locale, string $code): Response
    {
        $response = $this->render('product/show.html.twig', [
            'code' => $code,
            'variant' => $request->query->get('artnr'),
        ]);

        $canonical = sprintf('/%s/products/%s', $_locale, $code);
        $response->headers->set('Link', sprintf('<%s>; rel="canonical"', $canonical));

        return $response;
    }
}

