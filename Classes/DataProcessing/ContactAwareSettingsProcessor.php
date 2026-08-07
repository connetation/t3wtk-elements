<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Same as FlexFormSettingsProcessor (FlexForm settings merged on top of
 * TypoScript settings), plus injects the global contact.* Site Settings
 * under settings.contact.* — read directly from Site::getSettings(), NOT
 * via TypoScript {$contact.*} constant substitution.
 *
 * Why not TypoScript substitution: TypoScriptService::convertTypoScriptArrayToPlainArray()
 * (used by the generic FlexFormSettingsProcessor to turn the raw TypoScript
 * settings array into a plain one) turns any key that has BOTH a scalar
 * value AND child keys into an array shaped like
 * ['_typoScriptNodeValue' => <value>, ...children]. Something in the wider
 * settings tree gave "contact" (or "address") exactly that shape once
 * wired via {$contact.*} in GlobalAssets.typoscript, so {settings.contact.
 * phone} etc. resolved to an ARRAY instead of a string, crashing Fluid with
 * "Cannot cast an array to string." Reading straight from SiteSettings
 * sidesteps that pipeline entirely and guarantees plain scalars.
 *
 * Used by: Contact Form, Map & Location.
 */
final class ContactAwareSettingsProcessor implements DataProcessorInterface
{
    private const CONTACT_KEYS = [
        'phone', 'phoneIcon', 'email', 'emailIcon', 'officeHours',
    ];
    private const CONTACT_ADDRESS_KEYS = [
        'company', 'street', 'zip', 'city', 'country', 'url', 'icon',
    ];

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $fieldName = (string)$cObj->stdWrapValue('fieldName', $processorConfiguration, 'pi_flexform');
        $targetVar = (string)$cObj->stdWrapValue('as', $processorConfiguration, 'settings');

        $flexData = GeneralUtility::makeInstance(FlexFormService::class)
            ->convertFlexFormContentToArray((string)($processedData['data'][$fieldName] ?? ''));

        $typoScriptSettings = GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($contentObjectConfiguration['settings.'] ?? []);

        $settings = array_replace_recursive(
            $typoScriptSettings,
            $flexData['settings'] ?? []
        );

        $settings['contact'] = $this->resolveContactSettings($cObj);

        $processedData[$targetVar] = $settings;

        return $processedData;
    }

    private function resolveContactSettings(ContentObjectRenderer $cObj): array
    {
        $site = $cObj->getRequest()->getAttribute('site');
        $get = static fn(string $identifier): string => $site instanceof Site
            ? (string)($site->getSettings()->get($identifier, '') ?? '')
            : '';

        $contact = ['address' => []];
        foreach (self::CONTACT_KEYS as $key) {
            $contact[$key] = $get('contact.' . $key);
        }
        foreach (self::CONTACT_ADDRESS_KEYS as $key) {
            $contact['address'][$key] = $get('contact.address.' . $key);
        }

        return $contact;
    }
}
