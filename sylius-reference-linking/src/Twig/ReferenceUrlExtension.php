<?php

namespace Sylius\ReferenceLinking\Twig;

use Sylius\ReferenceLinking\Service\ReferenceUrlResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ReferenceUrlExtension extends AbstractExtension
{
    public function __construct(private ReferenceUrlResolver $resolver)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('reference_url', [$this, 'referenceUrl']),
        ];
    }

    public function referenceUrl(string $type, string $sku): ?string
    {
        return $this->resolver->resolveUrl($type, $sku);
    }
}

