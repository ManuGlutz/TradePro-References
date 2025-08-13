<?php

namespace Sylius\ReferenceLinking\Controller\Api;

use Sylius\ReferenceLinking\Service\ReferenceUrlResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/references')]
final class ReferenceUrlController extends AbstractController
{
    public function __construct(private ReferenceUrlResolver $resolver)
    {
    }

    #[Route(path: '/resolve', methods: ['GET'])]
    public function resolveAction(Request $request): JsonResponse
    {
        $type = (string) $request->query->get('type');
        $sku = (string) $request->query->get('sku');
        $url = $this->resolver->resolveUrl($type, $sku);

        return $this->json([
            'sku' => $sku,
            'type' => $type,
            'url' => $url,
        ]);
    }

    #[Route(path: '/resolve-batch', methods: ['POST'])]
    public function resolveBatchAction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $type = (string) ($data['type'] ?? '');
        $skus = $data['skus'] ?? [];

        $results = $this->resolver->resolveUrls($type, $skus);

        return $this->json([
            'type' => $type,
            'results' => $results,
        ]);
    }
}

