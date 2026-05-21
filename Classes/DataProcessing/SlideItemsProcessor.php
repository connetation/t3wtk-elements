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

final class SlideItemsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar         = $processorConfiguration['as'] ?? 'slides';
        $settingsField     = $processorConfiguration['settingsField'] ?? '';
        $contentElementUid = (int)($processedData['data']['uid'] ?? 0);

        if ($contentElementUid === 0) {
            $processedData[$targetVar] = [];
            $processedData['settings'] = $this->defaultSettings($settingsField);
            return $processedData;
        }

        // ─── FlexForm settings ────────────────────────────────────
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $rawFlex = $processedData['data'][$settingsField] ?? '';
        $flexData = $flexFormService->convertFlexFormContentToArray($rawFlex);
        $settings = $this->resolveSettings($flexData['settings'] ?? [], $settingsField);

        // ─── Slide items ──────────────────────────────────────────
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_slide_item');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_conn_elements_domain_model_slide_item')
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
                    'tx_conn_elements_domain_model_slide_item',
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

        // ─── Parallax slider: background image on tt_content ─────
        if (str_contains($settingsField, 'parallax')) {
            try {
                $bgFiles = $fileRepository->findByRelation(
                    'tt_content',
                    'tx_conn_elements_parallax_slider_bg_image',
                    $contentElementUid
                );
                $processedData['parallaxBgImage'] = $bgFiles[0] ?? null;
            } catch (\Exception $e) {
                $processedData['parallaxBgImage'] = null;
            }
        }

        return $processedData;
    }

    private function resolveSettings(array $raw, string $settingsField): array
    {
        if (str_contains($settingsField, 'hero')) {
            return [
                'height'          => in_array($raw['height'] ?? 'large', ['medium', 'large', 'fullscreen'], true)
                                        ? ($raw['height'] ?? 'large') : 'large',
                'effect'          => in_array($raw['effect'] ?? 'fade', ['fade', 'slide'], true)
                                        ? ($raw['effect'] ?? 'fade') : 'fade',
                'autoplay'        => (bool)($raw['autoplay'] ?? true),
                'autoplay_delay'  => (int)($raw['autoplay_delay'] ?? 5000),
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'show_pagination' => (bool)($raw['show_pagination'] ?? true),
            ];
        }

        if (str_contains($settingsField, 'thumbs')) {
            $thumbsPerView = (int)($raw['thumbs_per_view'] ?? 4);
            return [
                'height'          => in_array($raw['height'] ?? 'large', ['medium', 'large'], true)
                                        ? ($raw['height'] ?? 'large') : 'large',
                'thumbs_per_view' => in_array($thumbsPerView, [4, 5, 6], true) ? $thumbsPerView : 4,
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'autoplay'        => (bool)($raw['autoplay'] ?? false),
                'autoplay_delay'  => (int)($raw['autoplay_delay'] ?? 4000),
            ];
        }

        if (str_contains($settingsField, 'parallax')) {
            return [
                'height'          => in_array($raw['height'] ?? 'large', ['medium', 'large', 'fullscreen'], true)
                                        ? ($raw['height'] ?? 'large') : 'large',
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'show_pagination' => (bool)($raw['show_pagination'] ?? true),
                'autoplay'        => (bool)($raw['autoplay'] ?? false),
                'autoplay_delay'  => (int)($raw['autoplay_delay'] ?? 5000),
            ];
        }

        $slidesPerView = (int)($raw['slides_per_view'] ?? 3);

        return [
            'slides_per_view' => in_array($slidesPerView, [1, 2, 3, 4], true) ? $slidesPerView : 3,
            'loop'            => (bool)($raw['loop'] ?? true),
            'autoplay'        => (bool)($raw['autoplay'] ?? false),
            'autoplay_delay'  => (int)($raw['autoplay_delay'] ?? 4000),
            'show_navigation' => (bool)($raw['show_navigation'] ?? true),
            'show_pagination' => (bool)($raw['show_pagination'] ?? true),
        ];
    }

    private function defaultSettings(string $settingsField): array
    {
        if (str_contains($settingsField, 'hero')) {
            return [
                'height'          => 'large',
                'effect'          => 'fade',
                'autoplay'        => true,
                'autoplay_delay'  => 5000,
                'show_navigation' => true,
                'show_pagination' => true,
            ];
        }

        if (str_contains($settingsField, 'thumbs')) {
            return [
                'height'          => 'large',
                'thumbs_per_view' => 4,
                'show_navigation' => true,
                'autoplay'        => false,
                'autoplay_delay'  => 4000,
            ];
        }

        if (str_contains($settingsField, 'parallax')) {
            return [
                'height'          => 'large',
                'show_navigation' => true,
                'show_pagination' => true,
                'autoplay'        => false,
                'autoplay_delay'  => 5000,
            ];
        }

        return [
            'slides_per_view' => 3,
            'loop'            => true,
            'autoplay'        => false,
            'autoplay_delay'  => 4000,
            'show_navigation' => true,
            'show_pagination' => true,
        ];
    }
}
