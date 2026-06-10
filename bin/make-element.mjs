#!/usr/bin/env node
/**
 * Connetation Elements – Content Element Scaffold
 *
 * Usage:
 *   node bin/make-element.mjs
 *   node bin/make-element.mjs "Hero Banner"
 *   npm run make-element
 */

import { createInterface }                         from 'readline';
import { existsSync, mkdirSync, readFileSync,
         writeFileSync, appendFileSync }            from 'fs';
import { join, dirname }                           from 'path';
import { fileURLToPath }                           from 'url';

const __dir = dirname(fileURLToPath(import.meta.url));
const ROOT  = join(__dir, '..');

// ─── I/O helpers ──────────────────────────────────────────────────────────────

// Buffer piped stdin lines so rl.question works with non-TTY input too
const _pipedLines = [];
if (!process.stdin.isTTY) {
    const rl0 = createInterface({ input: process.stdin });
    await new Promise(r => {
        rl0.on('line', l => _pipedLines.push(l));
        rl0.on('close', r);
    });
}

const rl  = createInterface({ input: process.stdin, output: process.stdout });
const ask = (q, def = '') => {
    if (_pipedLines.length) {
        const ans = _pipedLines.shift().trim() || def;
        process.stdout.write((def ? `${q} [${def}]: ` : `${q}: `) + ans + '\n');
        return Promise.resolve(ans);
    }
    return new Promise(r =>
        rl.question(def ? `${q} [${def}]: ` : `${q}: `, a => r(a.trim() || def))
    );
};

const c = {
    reset:  '\x1b[0m',
    bold:   '\x1b[1m',
    green:  '\x1b[32m',
    yellow: '\x1b[33m',
    cyan:   '\x1b[36m',
    red:    '\x1b[31m',
    dim:    '\x1b[2m',
};
const ok   = msg => console.log(`  ${c.green}✓${c.reset}  ${msg}`);
const warn = msg => console.log(`  ${c.yellow}⚠${c.reset}  ${msg}`);
const info = msg => console.log(`  ${c.cyan}→${c.reset}  ${msg}`);

// ─── Name derivation ──────────────────────────────────────────────────────────

function toPascal(str) {
    return str.replace(/[-_\s]+(\w)/g, (_, c) => c.toUpperCase())
              .replace(/^\w/, c => c.toUpperCase());
}

function toKebab(pascal) {
    return pascal
        .replace(/([A-Z]+)([A-Z][a-z])/g, '$1-$2')
        .replace(/([a-z\d])([A-Z])/g, '$1-$2')
        .toLowerCase()
        .replace(/^-/, '');
}

function toSnake(pascal) {
    return toKebab(pascal).replace(/-/g, '_');
}

// ─── File helpers ─────────────────────────────────────────────────────────────

function ensureDir(filePath) {
    mkdirSync(dirname(filePath), { recursive: true });
}

function writeNew(rel, content) {
    const abs = join(ROOT, rel);
    ensureDir(abs);
    if (existsSync(abs)) {
        warn(`exists, skipping: ${rel}`);
        return;
    }
    writeFileSync(abs, content, 'utf8');
    ok(`created: ${rel}`);
}

function patchFile(rel, findStr, insertStr, label) {
    const abs = join(ROOT, rel);
    if (!existsSync(abs)) { warn(`file not found, skipping patch: ${rel}`); return; }
    let src = readFileSync(abs, 'utf8');
    if (src.includes(insertStr.slice(0, 40))) {
        warn(`already patched, skipping: ${rel} (${label})`);
        return;
    }
    if (!src.includes(findStr)) {
        warn(`anchor not found in ${rel}, skipping (${label})`);
        return;
    }
    src = src.replace(findStr, insertStr + findStr);
    writeFileSync(abs, src, 'utf8');
    ok(`patched: ${rel} (${label})`);
}

function patchFileAppend(rel, findStr, appendStr, label) {
    const abs = join(ROOT, rel);
    if (!existsSync(abs)) { warn(`file not found, skipping patch: ${rel}`); return; }
    let src = readFileSync(abs, 'utf8');
    if (src.includes(appendStr.slice(0, 40))) {
        warn(`already patched, skipping: ${rel} (${label})`);
        return;
    }
    if (!src.includes(findStr)) {
        warn(`anchor not found in ${rel}, skipping (${label})`);
        return;
    }
    src = src.replace(findStr, findStr + appendStr);
    writeFileSync(abs, src, 'utf8');
    ok(`patched: ${rel} (${label})`);
}

// ─── File generators ──────────────────────────────────────────────────────────

function genFlexForm(pascal, snake) {
    return `<?xml version="1.0" encoding="UTF-8"?>
<T3DataStructure>
    <sheets>
        <sDEF>
            <ROOT>
                <sheetTitle>Display</sheetTitle>
                <type>array</type>
                <el>
                    <settings.style>
                        <label>Style</label>
                        <config>
                            <type>select</type>
                            <renderType>selectSingle</renderType>
                            <default>default</default>
                            <items>
                                <numIndex index="0"><label>Default</label><value>default</value></numIndex>
                            </items>
                        </config>
                    </settings.style>
                </el>
            </ROOT>
        </sDEF>
    </sheets>
</T3DataStructure>
`;
}

function genTypoScriptSimple(pascal, snake, ctype, kebab) {
    return `# ─────────────────────────────────────────────
# ${pascal} Content Element – conn_elements
# ─────────────────────────────────────────────

lib.contentElement.templateRootPaths.20 = EXT:conn_elements/Resources/Private/Templates/ContentElements/
lib.contentElement.partialRootPaths.20  = EXT:conn_elements/Resources/Private/Partials/

tt_content.${ctype} =< lib.contentElement
tt_content.${ctype} {
    templateName = ${pascal}
    layoutRootPaths.20 = EXT:conn_elements/Resources/Private/Layouts/
    dataProcessing {
        10 = TYPO3\\CMS\\Frontend\\DataProcessing\\FlexFormProcessor
        10 {
            fieldName = tx_conn_elements_${snake}_settings
            as = settings
        }
    }
}
`;
}

function genTypoScriptWithItems(pascal, snake, ctype) {
    return `# ─────────────────────────────────────────────
# ${pascal} Content Element – conn_elements
# ─────────────────────────────────────────────

lib.contentElement.templateRootPaths.20 = EXT:conn_elements/Resources/Private/Templates/ContentElements/
lib.contentElement.partialRootPaths.20  = EXT:conn_elements/Resources/Private/Partials/

tt_content.${ctype} =< lib.contentElement
tt_content.${ctype} {
    templateName = ${pascal}
    layoutRootPaths.20 = EXT:conn_elements/Resources/Private/Layouts/
    dataProcessing {
        10 = Connetation\\Elements\\DataProcessing\\${pascal}ItemsProcessor
        10 {
            fieldName = tx_conn_elements_${snake}_items
            as = items
        }
    }
}
`;
}

function genTemplate(pascal, snake, ctype, kebab, hasItems) {
    const assetId = `conn-elements-${kebab}`;
    const itemsBlock = hasItems ? `
    <f:if condition="{items}">
        <f:then>
            <div class="conn-${kebab}__list">
                <f:for each="{items}" as="item">
                    <f:render partial="${pascal}/Item" arguments="{item: item}" />
                </f:for>
            </div>
        </f:then>
        <f:else>
            <p class="text-muted fst-italic">
                <f:translate key="${snake}.empty" extensionName="conn_elements" />
            </p>
        </f:else>
    </f:if>` : `
    <div class="conn-${kebab}__content">
        <f:if condition="{data.bodytext}">
            {data.bodytext -> f:format.html()}
        </f:if>
    </div>`;

    return `<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
      data-namespace-typo3-fluid="true">

<f:layout name="Default" />

<f:section name="Main">

<f:asset.css identifier="${assetId}-css"
    href="EXT:conn_elements/Resources/Public/Css/${kebab}.css" />
<f:asset.script identifier="${assetId}-js"
    src="EXT:conn_elements/Resources/Public/Js/components/${kebab}.js"
    type="module"
    priority="false" />

<section class="conn-${kebab} row" id="conn-${kebab}-{data.uid}">
    <div class="container-fluid">
${itemsBlock}
    </div>
</section>

</f:section>

</html>
`;
}

function genPartialItem(pascal, kebab) {
    return `<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
      data-namespace-typo3-fluid="true">

<div class="conn-${kebab}__item">
    <h3 class="conn-${kebab}__title">{item.title}</h3>
    <f:if condition="{item.body}">
        <div class="conn-${kebab}__body">
            {item.body -> f:format.html()}
        </div>
    </f:if>
</div>

</html>
`;
}

function genScssIndex(kebab) {
    return `@use '../Shared/tokens';
@use '${kebab}';
`;
}

function genScssComponent(pascal, ctype, kebab) {
    return `.frame-type-${ctype} header {
    h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
        color: var(--conn-text);
    }
}

.conn-${kebab} {
    background: var(--conn-bg);

    &__content {
        color: var(--conn-text);
    }

    &__item {
        background: var(--conn-surface);
        border: 1px solid var(--conn-border);
        border-radius: var(--conn-radius);
        padding: 1.5rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;

        &:hover {
            transform: translateY(-3px);
            box-shadow: var(--conn-shadow-hover);
        }
    }

    &__title {
        color: var(--conn-text);
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    &__body {
        color: var(--conn-text-muted);
        font-size: 0.9rem;
        line-height: 1.6;

        p:last-child { margin-bottom: 0; }
    }
}
`;
}

function genTypeScript(pascal, kebab) {
    return `/**
 * Connetation Elements – ${pascal}
 */

class ${pascal} {

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        // TODO: implement ${pascal} behaviour
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-${kebab}', el => new ${pascal}(el));
`;
}

function genIcon(pascal) {
    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
    <rect x="1" y="1" width="14" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
    <text x="8" y="11" text-anchor="middle" font-size="7" font-family="sans-serif" font-weight="bold">${pascal.slice(0, 2).toUpperCase()}</text>
</svg>
`;
}

function genTcaModel(pascal, snake) {
    const tableKey = `tx_conn_elements_domain_model_${snake}_item`;
    return `<?php

declare(strict_types=1);

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title'          => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:model.${snake}_item',
        'label'          => 'title',
        'sortby'         => 'sorting',
        'tstamp'         => 'tstamp',
        'crdate'         => 'crdate',
        'delete'         => 'deleted',
        'enablecolumns'  => ['disabled' => 'hidden'],
        'iconfile'       => 'EXT:conn_elements/Resources/Public/Icons/conn_logo_small.svg',
        'rootLevel'      => -1,
        'security'       => ['ignorePageTypeRestriction' => true],
    ],

    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
            'config'  => ['type' => 'check', 'renderType' => 'checkboxToggle'],
        ],
        'sorting' => [
            'config' => ['type' => 'passthrough'],
        ],
        'content_element' => [
            'config' => ['type' => 'passthrough'],
        ],
        'title' => [
            'label'   => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:${snake}_item.title',
            'config'  => [
                'type'     => 'input',
                'size'     => 50,
                'max'      => 255,
                'required' => true,
            ],
        ],
        'body' => [
            'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:${snake}_item.body',
            'config' => [
                'type'           => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'rows'           => 6,
            ],
        ],
    ],

    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    title, body,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
];
`;
}

function genDataProcessor(pascal, snake) {
    const tableKey = `tx_conn_elements_domain_model_${snake}_item`;
    return `<?php

declare(strict_types=1);

namespace Connetation\\Elements\\DataProcessing;

use Doctrine\\DBAL\\ParameterType;
use TYPO3\\CMS\\Core\\Database\\ConnectionPool;
use TYPO3\\CMS\\Core\\Service\\FlexFormService;
use TYPO3\\CMS\\Core\\Utility\\GeneralUtility;
use TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer;
use TYPO3\\CMS\\Frontend\\ContentObject\\DataProcessorInterface;

final class ${pascal}ItemsProcessor implements DataProcessorInterface
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

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_${snake}_settings'] ?? ''
        );
        $settings = $this->resolveSettings($flexData['settings'] ?? []);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('${tableKey}');

        $rows = $queryBuilder
            ->select('*')
            ->from('${tableKey}')
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

        $processedData[$targetVar] = $rows;
        $processedData['settings'] = $settings;

        return $processedData;
    }

    private function resolveSettings(array $raw): array
    {
        return [
            'style' => ($raw['style'] ?? 'default'),
        ];
    }

    private function defaultSettings(): array
    {
        return [
            'style' => 'default',
        ];
    }
}
`;
}

// ─── Patch generators ─────────────────────────────────────────────────────────

function patchIcons(pascal, kebab) {
    const entry = `    'conn-ce-${kebab}' => [
        'provider' => \\TYPO3\\CMS\\Core\\Imaging\\IconProvider\\SvgIconProvider::class,
        'source'   => 'EXT:conn_elements/Resources/Public/Icons/${kebab}-ce.svg',
    ],\n`;
    patchFile(
        'Configuration/Icons.php',
        '];\n',
        entry,
        'icon entry'
    );
}

function patchBuildMjs(pascal, kebab, snake) {
    const entry = `        '${kebab}':  'Resources/Private/Components/${pascal}/${kebab}.ts',\n`;
    patchFileAppend(
        'build.mjs',
        "    },\n    bundle:",
        '',
        'skip'  // we use a different approach below
    );
    const abs = join(ROOT, 'build.mjs');
    let src = readFileSync(abs, 'utf8');
    if (src.includes(`/${kebab}.ts'`)) {
        warn(`build.mjs already has entry for ${kebab}, skipping`);
        return;
    }
    // Insert before the last entry in entryPoints (before the closing },)
    const anchor = "    },\n    bundle:";
    src = src.replace(anchor, `${entry}${anchor}`);
    writeFileSync(abs, src, 'utf8');
    ok(`patched: build.mjs (entry point)`);
}

function patchSql(pascal, snake, hasItems) {
    const abs = join(ROOT, 'ext_tables.sql');
    let src = readFileSync(abs, 'utf8');

    // Add tt_content fields — comma goes on the previous last line, NOT on the new last line
    const newFields = hasItems
        ? `    tx_conn_elements_${snake}_items     int(11) DEFAULT '0' NOT NULL,\n    tx_conn_elements_${snake}_settings   mediumtext`
        : `    tx_conn_elements_${snake}_settings   mediumtext`;
    if (!src.includes(`tx_conn_elements_${snake}_`)) {
        // Replace `\n);` — adds comma to the previous last field and appends new field(s)
        src = src.replace(/\n(\);)\n\n--/, `,\n${newFields}\n$1\n\n--`);
        ok(`patched: ext_tables.sql (tt_content fields)`);
    } else {
        warn(`ext_tables.sql tt_content already has ${snake} fields, skipping`);
    }

    // Add child table if needed
    if (hasItems) {
        const tableKey = `tx_conn_elements_domain_model_${snake}_item`;
        if (!src.includes(tableKey)) {
            const tableBlock = `\n-- ─── ${pascal} ─────────────────────────────────────────────────────────────────\n\nCREATE TABLE ${tableKey} (\n    uid             int(11) NOT NULL auto_increment,\n    pid             int(11) DEFAULT '0' NOT NULL,\n    tstamp          int(11) DEFAULT '0' NOT NULL,\n    crdate          int(11) DEFAULT '0' NOT NULL,\n    deleted         tinyint(4) DEFAULT '0' NOT NULL,\n    hidden          tinyint(4) DEFAULT '0' NOT NULL,\n    sorting         int(11) DEFAULT '0' NOT NULL,\n    content_element int(11) DEFAULT '0' NOT NULL,\n\n    title           varchar(255) DEFAULT '' NOT NULL,\n    body            mediumtext,\n\n    PRIMARY KEY (uid),\n    KEY parent (pid),\n    KEY content_element (content_element)\n);\n`;
            src += tableBlock;
            ok(`patched: ext_tables.sql (new ${tableKey} table)`);
        } else {
            warn(`ext_tables.sql already has table ${tableKey}, skipping`);
        }
    }

    writeFileSync(abs, src, 'utf8');
}

function patchLocalconf(pascal, snake, ctype, kebab, group) {
    const abs = join(ROOT, 'ext_localconf.php');
    let src = readFileSync(abs, 'utf8');

    // Add wizard entry before closing ';
    const wizardEntry = `\n     mod.wizards.newContentElement.wizardItems.${group}.elements.${ctype} {\n        iconIdentifier = conn-ce-${kebab}\n        title = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.${snake}\n        description = LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.${snake}.description\n        tt_content_defValues.CType = ${ctype}\n    }`;

    if (src.includes(`tt_content_defValues.CType = ${ctype}`)) {
        warn(`ext_localconf.php already has wizard entry for ${ctype}, skipping`);
    } else {
        // Replace the LAST '; in the file (the closing quote of the PageTSconfig string)
        const lastQuote = src.lastIndexOf("';");
        src = src.slice(0, lastQuote) + wizardEntry + src.slice(lastQuote);
        ok(`patched: ext_localconf.php (wizard entry)`);
    }

    // Update show list for the group
    const showPattern = new RegExp(`(mod\\.wizards\\.newContentElement\\.wizardItems\\.${group}\\.show = [^\\n]+)`);
    const showMatch = src.match(showPattern);
    if (showMatch && !showMatch[1].includes(ctype)) {
        src = src.replace(showMatch[1], `${showMatch[1]},${ctype}`);
        ok(`patched: ext_localconf.php (show list for ${group})`);
    } else if (showMatch) {
        warn(`ext_localconf.php show list for ${group} already has ${ctype}, skipping`);
    } else {
        info(`No show list found for group ${group} — add manually if needed`);
    }

    writeFileSync(abs, src, 'utf8');
}

function patchTtContent(pascal, snake, ctype, kebab, group, hasItems) {
    const abs = join(ROOT, 'Configuration/TCA/Overrides/tt_content.php');
    let src = readFileSync(abs, 'utf8');

    if (src.includes(`'${ctype}'`)) {
        warn(`tt_content.php already has entry for ${ctype}, skipping`);
        return;
    }

    const colsBlock = hasItems
        ? `ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_${snake}_items' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:${snake}.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_${snake}_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 200,
            'appearance'    => [
                'collapseAll'                     => true,
                'levelLinksPosition'              => 'top',
                'showSynchronizationLink'         => true,
                'showAllLocalizationLink'         => true,
                'showPossibleLocalizationRecords' => true,
                'enabledControls'                 => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_elements_${snake}_settings' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:${snake}.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => ['default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/${pascal}.xml'],
        ],
    ],
]);\n`
        : `ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_${snake}_settings' => [
        'label'  => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:${snake}.settings',
        'config' => [
            'type' => 'flex',
            'ds'   => ['default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/${pascal}.xml'],
        ],
    ],
]);\n`;

    const mainField = hasItems ? `tx_conn_elements_${snake}_items,` : '';

    const block = `
// ─── ${pascal} ─────────────────────────────────────────────────────────
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['${ctype}'] = 'conn-ce-${kebab}';

ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:ce.${snake}',
    'value' => '${ctype}',
    'icon'  => 'conn-ce-${kebab}',
    'group' => '${group}',
]);

${colsBlock}
$GLOBALS['TCA']['tt_content']['types']['${ctype}'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,--palette--;;header,${mainField}
        --div--;LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:${snake}.tab.config,
            tx_conn_elements_${snake}_settings,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,--palette--;;access,
    ',
];
`;

    src += block;
    writeFileSync(abs, src, 'utf8');
    ok(`patched: Configuration/TCA/Overrides/tt_content.php (${ctype} block)`);
}

function patchXlf(pascal, snake, ctype, kebab, hasItems) {
    const abs = join(ROOT, 'Resources/Private/Language/locallang_be.xlf');
    let src = readFileSync(abs, 'utf8');

    if (src.includes(`id="ce.${snake}"`)) {
        warn(`locallang_be.xlf already has entry for ce.${snake}, skipping`);
        return;
    }

    const itemEntries = hasItems ? `
            <trans-unit id="${snake}.items">
                <source>Items</source>
            </trans-unit>
            <trans-unit id="${snake}_item.title">
                <source>Title</source>
            </trans-unit>
            <trans-unit id="${snake}_item.body">
                <source>Content</source>
            </trans-unit>
            <trans-unit id="model.${snake}_item">
                <source>${pascal} Item</source>
            </trans-unit>` : '';

    const entries = `
            <!-- ${pascal} content element -->
            <trans-unit id="ce.${snake}">
                <source>${pascal}</source>
            </trans-unit>
            <trans-unit id="ce.${snake}.description">
                <source>${pascal} content element</source>
            </trans-unit>
            <trans-unit id="${snake}.tab.config">
                <source>${pascal} Settings</source>
            </trans-unit>
            <trans-unit id="${snake}.settings">
                <source>Display Settings</source>
            </trans-unit>${itemEntries}`;

    src = src.replace('        </body>', `${entries}\n        </body>`);
    writeFileSync(abs, src, 'utf8');
    ok(`patched: Resources/Private/Language/locallang_be.xlf (${snake} translations)`);
}

function patchTypoScriptSetup(pascal, ctype, kebab) {
    const abs = join(ROOT, 'Configuration/TypoScript/setup.typoscript');
    if (!existsSync(abs)) { return; }
    let src = readFileSync(abs, 'utf8');
    const importLine = `@import 'EXT:conn_elements/Configuration/TypoScript/ContentElements/${pascal}.typoscript'`;
    if (src.includes(importLine)) {
        warn(`setup.typoscript already imports ${pascal}.typoscript, skipping`);
        return;
    }
    src += `\n${importLine}\n`;
    writeFileSync(abs, src, 'utf8');
    ok(`patched: Configuration/TypoScript/setup.typoscript (import)`);
}

// ─── Main ─────────────────────────────────────────────────────────────────────

async function main() {
    const argName = process.argv[2] || '';

    console.log(`\n${c.bold}${c.cyan}Connetation Elements — New Content Element${c.reset}\n`);

    const rawName = argName || await ask('Element name (e.g. "Hero Banner", "Testimonial", "Pricing")');
    if (!rawName) { rl.close(); process.exit(1); }

    const pascal = toPascal(rawName);
    const kebab  = toKebab(pascal);
    const snake  = toSnake(pascal);
    const ctype  = `conn_bs_${snake}`;

    console.log(`\n  PascalCase : ${c.bold}${pascal}${c.reset}`);
    console.log(`  kebab-case : ${kebab}`);
    console.log(`  CType      : ${ctype}\n`);

    const typeChoice = await ask('Type: [1] Simple (FlexForm only)  [2] With inline child records', '1');
    const hasItems   = typeChoice === '2';

    const groups    = { '1': 'conn_bootstrap', '2': 'conn', '3': 'conn_swiper' };
    const groupList = '  [1] conn_bootstrap  [2] conn  [3] conn_swiper';
    const groupChoice = await ask(`CE group:\n${groupList}\n  Choice`, '1');
    const group = groups[groupChoice] ?? 'conn_bootstrap';

    console.log(`\n${c.bold}Generating files…${c.reset}\n`);

    // ── New files ──────────────────────────────────────────────────────────────

    writeNew(`Configuration/FlexForms/${pascal}.xml`,
        genFlexForm(pascal, snake));

    writeNew(`Configuration/TypoScript/ContentElements/${pascal}.typoscript`,
        hasItems
            ? genTypoScriptWithItems(pascal, snake, ctype)
            : genTypoScriptSimple(pascal, snake, ctype, kebab));

    writeNew(`Resources/Private/Templates/ContentElements/${pascal}.html`,
        genTemplate(pascal, snake, ctype, kebab, hasItems));

    if (hasItems) {
        writeNew(`Resources/Private/Partials/${pascal}/Item.html`,
            genPartialItem(pascal, kebab));
        writeNew(`Configuration/TCA/tx_conn_elements_domain_model_${snake}_item.php`,
            genTcaModel(pascal, snake));
        writeNew(`Classes/DataProcessing/${pascal}ItemsProcessor.php`,
            genDataProcessor(pascal, snake));
    }

    writeNew(`Resources/Private/Components/${pascal}/index.scss`,
        genScssIndex(kebab));

    writeNew(`Resources/Private/Components/${pascal}/_${kebab}.scss`,
        genScssComponent(pascal, ctype, kebab));

    writeNew(`Resources/Private/Components/${pascal}/${kebab}.ts`,
        genTypeScript(pascal, kebab));

    writeNew(`Resources/Public/Icons/${kebab}-ce.svg`,
        genIcon(pascal));

    // ── Patch existing files ───────────────────────────────────────────────────

    console.log(`\n${c.bold}Patching existing files…${c.reset}\n`);

    patchIcons(pascal, kebab);
    patchBuildMjs(pascal, kebab, snake);
    patchSql(pascal, snake, hasItems);
    patchLocalconf(pascal, snake, ctype, kebab, group);
    patchTtContent(pascal, snake, ctype, kebab, group, hasItems);
    patchXlf(pascal, snake, ctype, kebab, hasItems);
    patchTypoScriptSetup(pascal, ctype, kebab);

    // ── Summary ────────────────────────────────────────────────────────────────

    console.log(`\n${c.bold}${c.green}Done!${c.reset} Next steps ${c.dim}(in order)${c.reset}:\n`);
    info(`1. npm run build  ${c.dim}(or: ddev exec "cd /var/www/html/vendor/conn/elements && npm run build")${c.reset}`);
    info(`2. TYPO3 → Admin Tools → Maintenance → Flush all caches`);
    if (hasItems) {
        info(`3. TYPO3 → Admin Tools → Maintenance → Analyze Database Structure → apply`);
        console.log(`\n  ${c.yellow}!${c.reset}  ${c.dim}Step 3 is required — skipping it causes "Unknown column" SQL errors when saving.${c.reset}`);
    }
    console.log(`\n  ${c.dim}Edit the generated files to customize your element.${c.reset}`);
    console.log(`  ${c.dim}Icon: replace Resources/Public/Icons/${kebab}-ce.svg with your SVG.${c.reset}\n`);

    rl.close();
}

main().catch(err => {
    console.error(`\n${c.red}Error:${c.reset}`, err.message);
    rl.close();
    process.exit(1);
});
