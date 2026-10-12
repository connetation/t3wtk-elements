<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\Utility\PhoneUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Processes contact settings from FlexForm and TypoScript configuration.
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

        $contactOverrides = $flexData['settings']['contact'] ?? [];
        $settings['contact'] = $this->resolveContactSettings($cObj, is_array($contactOverrides) ? $contactOverrides : []);

        $processedData[$targetVar] = $settings;

        return $processedData;
    }

    private function resolveContactSettings(ContentObjectRenderer $cObj, array $overrides): array
    {
        $site = $cObj->getRequest()->getAttribute('site');
        $get = static fn(string $identifier): string => $site instanceof Site
            ? (string)($site->getSettings()->get($identifier, '') ?? '')
            : '';

        $overrideAddress = is_array($overrides['address'] ?? null) ? $overrides['address'] : [];

        $contact = ['address' => []];
        foreach (self::CONTACT_KEYS as $key) {
            $override = trim((string)($overrides[$key] ?? ''));
            $contact[$key] = $override !== '' ? $override : $get('contact.' . $key);
        }
        foreach (self::CONTACT_ADDRESS_KEYS as $key) {
            $override = trim((string)($overrideAddress[$key] ?? ''));
            $contact['address'][$key] = $override !== '' ? $override : $get('contact.address.' . $key);
        }

        if ($contact['phone'] !== '') {
            // Dial-safe variant for `tel:` links, kept separate from the display
            // string in `phone` (e.g. "+43 (0) 662 123 456 - 7"), which is meant
            // to be read, not dialed as-is.
            $contact['phoneDial'] = PhoneUtility::sanitizeForDialing($contact['phone']);
        }

        return $contact;
    }
}
