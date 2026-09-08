<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

final class SlideItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_slide_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'slides';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return $processorConfiguration['settingsField'] ?? '';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function finalizeProcessedData(
        array $processedData,
        array $rows,
        array $settings,
        string $settingsField,
        int $contentElementUid
    ): array {
        // ─── Parallax slider: background image on tt_content ─────
        if (str_contains($settingsField, 'parallax')) {
            $grouped = $this->resolveFileReferences(
                'tt_content',
                'tx_conn_elements_parallax_slider_bg_image',
                [$contentElementUid]
            );
            $processedData['parallaxBgImage'] = $grouped[$contentElementUid][0] ?? null;
        }

        return $processedData;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $allowedDelays = [3000, 4000, 5000, 7000];

        if (str_contains($settingsField, 'hero')) {
            $delay = (int)($raw['autoplay_delay'] ?? 4000);
            return [
                'height'          => $this->resolveHeroHeight($raw['height'] ?? null),
                'effect'          => in_array($raw['effect'] ?? 'fade', ['fade', 'slide'], true)
                                        ? ($raw['effect'] ?? 'fade') : 'fade',
                'autoplay'        => (bool)($raw['autoplay'] ?? true),
                'autoplay_delay'  => in_array($delay, $allowedDelays, true) ? $delay : 4000,
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'show_pagination' => (bool)($raw['show_pagination'] ?? true),
            ];
        }

        if (str_contains($settingsField, 'thumbs')) {
            $thumbsPerView = (int)($raw['thumbs_per_view'] ?? 4);
            $delay = (int)($raw['autoplay_delay'] ?? 4000);
            return [
                'height'          => in_array($raw['height'] ?? 'large', ['medium', 'large'], true)
                                        ? ($raw['height'] ?? 'large') : 'large',
                'thumbs_per_view' => in_array($thumbsPerView, [4, 5, 6], true) ? $thumbsPerView : 4,
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'autoplay'        => (bool)($raw['autoplay'] ?? false),
                'autoplay_delay'  => in_array($delay, $allowedDelays, true) ? $delay : 4000,
                'openInLightbox'  => (bool)($raw['openInLightbox'] ?? false),
            ];
        }

        if (str_contains($settingsField, 'parallax')) {
            $delay = (int)($raw['autoplay_delay'] ?? 4000);
            return [
                'height'          => in_array($raw['height'] ?? 'large', ['medium', 'large', 'fullscreen'], true)
                                        ? ($raw['height'] ?? 'large') : 'large',
                'show_navigation' => (bool)($raw['show_navigation'] ?? true),
                'show_pagination' => (bool)($raw['show_pagination'] ?? true),
                'autoplay'        => (bool)($raw['autoplay'] ?? false),
                'autoplay_delay'  => in_array($delay, $allowedDelays, true) ? $delay : 4000,
            ];
        }

        $slidesPerView = (int)($raw['slides_per_view'] ?? 3);
        $delay = (int)($raw['autoplay_delay'] ?? 4000);

        return [
            'slides_per_view' => in_array($slidesPerView, [1, 2, 3, 4], true) ? $slidesPerView : 3,
            'loop'            => (bool)($raw['loop'] ?? true),
            'autoplay'        => (bool)($raw['autoplay'] ?? false),
            'autoplay_delay'  => in_array($delay, $allowedDelays, true) ? $delay : 4000,
            'show_navigation' => (bool)($raw['show_navigation'] ?? true),
            'show_pagination' => (bool)($raw['show_pagination'] ?? true),
        ];
    }

    // settings.height used to be a medium/large/fullscreen select; it is now a
    // 20-100 (step 5) vh slider. Map old saved keyword values to their
    // equivalent vh number so existing elements keep their original height.
    private function resolveHeroHeight(mixed $raw): int
    {
        $legacy = ['medium' => 55, 'large' => 70, 'fullscreen' => 100];
        if (is_string($raw) && isset($legacy[$raw])) {
            return $legacy[$raw];
        }

        $height = (int)($raw ?? 50);

        return ($height >= 20 && $height <= 100) ? $height : 50;
    }

    protected function defaultSettings(string $settingsField): array
    {
        if (str_contains($settingsField, 'hero')) {
            return [
                'height'          => 50,
                'effect'          => 'fade',
                'autoplay'        => true,
                'autoplay_delay'  => 4000,
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
                'openInLightbox'  => false,
            ];
        }

        if (str_contains($settingsField, 'parallax')) {
            return [
                'height'          => 'large',
                'show_navigation' => true,
                'show_pagination' => true,
                'autoplay'        => false,
                'autoplay_delay'  => 4000,
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
