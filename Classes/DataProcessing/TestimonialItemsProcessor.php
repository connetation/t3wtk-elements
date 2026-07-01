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

final class TestimonialItemsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar         = $processorConfiguration['as'] ?? 'testimonials';
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['settings'] = $this->defaultSettings();
            return $processedData;
        }

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_testimonials_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_testimonial_item');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_testimonial_item')
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

        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        foreach ($rows as &$row) {
            try {
                $files = $fileRepository->findByRelation(
                    'tx_conn_elements_domain_model_testimonial_item',
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
        $columns = (int)($raw['columns'] ?? 3);
        $colMap  = [
            1 => 'col-12',
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
        ];
        $layout = in_array($raw['layout'] ?? 'grid', ['grid', 'carousel'], true)
            ? ($raw['layout'] ?? 'grid')
            : 'grid';

        return [
            'layout'        => $layout,
            'columns'       => $columns,
            'colClass'      => $colMap[$columns] ?? $colMap[3],
            'autoplay'      => (bool)($raw['autoplay'] ?? true),
            'autoplayDelay' => max(1000, (int)($raw['autoplayDelay'] ?? 4000)),
            'showRating'    => (bool)($raw['showRating'] ?? true),
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'layout'        => 'grid',
            'columns'       => 3,
            'colClass'      => 'col-sm-6 col-lg-4',
            'autoplay'      => true,
            'autoplayDelay' => 4000,
            'showRating'    => true,
        ];
    }
}
