<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface;
use TYPO3\CMS\Core\SystemResource\Publishing\UriGenerationOptions;
use TYPO3\CMS\Core\SystemResource\SystemResourceFactory;

final class IconAssetUrlResolver
{
    public function __construct(
        private readonly SystemResourceFactory $systemResourceFactory,
        private readonly SystemResourcePublisherInterface $resourcePublisher,
    ) {}

    public function resolve(string $extPath, ?ServerRequestInterface $request = null): string
    {
        $resource = $this->systemResourceFactory->createPublicResource($extPath);

        return (string)$this->resourcePublisher->generateUri($resource, $request, new UriGenerationOptions(cacheBusting: false));
    }
}
