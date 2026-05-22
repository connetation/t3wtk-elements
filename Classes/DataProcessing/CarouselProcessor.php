<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class CarouselProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar         = $processorConfiguration['as'] ?? 'items';
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['settings'] = $this->defaultSettings();
            return $processedData;
        }

        // ─── FlexForm settings ────────────────────────────────────
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_carousel_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        // ─── Slides ───────────────────────────────────────────────
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_carousel_slide');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_carousel_slide')
            ->where(
                $queryBuilder->expr()->eq(
                    'content_element',
                    $queryBuilder->createNamedParameter($contentElementUid, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'deleted',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'hidden',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)
                )
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        // ─── Resolve FAL image per slide ──────────────────────────
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        foreach ($rows as &$row) {
            try {
                $files = $fileRepository->findByRelation(
                    'tx_conn_elements_domain_model_carousel_slide',
                    'image',
                    $row['uid']
                );
                $row['imageFile'] = $files[0] ?? null;
            } catch (\Exception $e) {
                $row['imageFile'] = null;
            }
        }
        unset($row);

        $processedData[$targetVar] = $rows;
        $processedData['settings'] = $settings;

        return $processedData;
    }

    private function resolveSettings(array $raw): array
    {
        $allowedRatios = ['21x9', '16x9', '4x3', '1x1'];
        $aspectRatio   = in_array($raw['aspectRatio'] ?? '', $allowedRatios, true)
            ? $raw['aspectRatio'] : '16x9';

        return [
            'aspectRatio' => $aspectRatio,
            'controls'    => (bool)(int)($raw['controls']   ?? 1),
            'indicators'  => (bool)(int)($raw['indicators'] ?? 1),
            'autoplay'    => (bool)(int)($raw['autoplay']   ?? 0),
            'interval'    => max(1000, (int)($raw['interval'] ?? 5000)),
            'fade'        => (bool)(int)($raw['fade']       ?? 0),
            'dark'        => (bool)(int)($raw['dark']       ?? 0),
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'aspectRatio' => '16x9',
            'controls'    => true,
            'indicators'  => true,
            'autoplay'    => false,
            'interval'    => 5000,
            'fade'        => false,
            'dark'        => false,
        ];
    }
}
