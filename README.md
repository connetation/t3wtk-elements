# conn_elements

TYPO3 14 content elements package for Connetation. Provides the **Dashboard**, **Wiki**, and **Bootstrap Accordion** content elements, plus a scaffold for adding further elements.

## Build

Node dependencies are managed per-package. Run all commands from `packages/elements/` (or `vendor/conn/elements/` in the local repo).

```bash
npm install
```

### SCSS

```bash
# One-off build (compressed output)
npm run build:scss

# Watch mode
npm run dev:scss
```

### JavaScript (ESM)

```bash
# One-off build (minified)
npm run build:js

# Watch mode (inline sourcemap)
npm run dev:js
```

### Full build (SCSS + JS)

```bash
npm run build
```

### Dev (watch SCSS + JS in parallel)

```bash
npm run dev
```

## Inside DDEV

DDEV runs `npm install && npm run build` for this package automatically on `ddev start`.

To rebuild manually inside the container:

```bash
ddev exec "cd /var/www/html/packages/elements && npm run build:scss"
ddev exec "cd /var/www/html/packages/elements && npm run build:js"
ddev exec "cd /var/www/html/packages/elements && npm run build"
```

---

## How to add a new content element

Follow these steps every time you add a new content element to `conn_elements`. The Bootstrap Accordion (`bs_accordion`) is a complete reference implementation — study its files alongside this guide.

### 1. Plan your element

Decide on:
- **CType** — the internal identifier, e.g. `bs_carousel`. Prefix Bootstrap-based elements with `bs_`, custom Conn elements with `conn_`.
- **Group** — `conn` for custom/complex elements, `conn_bootstrap` for Bootstrap-component wrappers.
- **Data model** — does it need inline child records? A relation to an existing table? Or just FlexForm settings on `tt_content`?

---

### 2. Database schema — `ext_tables.sql`

Add any new columns to `tt_content` and create new tables for inline child records.

```sql
-- In the existing CREATE TABLE tt_content block:
tx_conn_elements_myelement_items    int(11) DEFAULT '0' NOT NULL,
tx_conn_elements_myelement_settings mediumtext,

-- New table for inline child records:
CREATE TABLE tx_conn_elements_domain_model_myelement_item (
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,
    tstamp          int(11) DEFAULT '0' NOT NULL,
    crdate          int(11) DEFAULT '0' NOT NULL,
    deleted         tinyint(4) DEFAULT '0' NOT NULL,
    hidden          tinyint(4) DEFAULT '0' NOT NULL,
    sorting         int(11) DEFAULT '0' NOT NULL,
    content_element int(11) DEFAULT '0' NOT NULL,

    title           varchar(255) DEFAULT '' NOT NULL,
    -- … your fields …

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY content_element (content_element)
);
```

After saving this file, run **Admin Tools → Database Analyser → Compare** in the TYPO3 backend and apply the schema changes.

---

### 3. TCA for child records — `Configuration/TCA/tx_conn_elements_domain_model_myelement_item.php`

One file per new database table. Mirrors the existing `accordion_item` file exactly.

```php
<?php
declare(strict_types=1);

return [
    'ctrl' => [
        'title'         => 'LLL:EXT:conn_elements/.../locallang_be.xlf:model.myelement_item',
        'label'         => 'title',
        'sortby'        => 'sorting',
        'tstamp'        => 'tstamp',
        'crdate'        => 'crdate',
        'delete'        => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile'      => 'EXT:conn_elements/Resources/Public/Icons/myelement-ce.svg',
        'rootLevel'     => -1,
        'security'      => ['ignorePageTypeRestriction' => true],
    ],
    'columns' => [
        'hidden'          => [ /* standard toggle */ ],
        'sorting'         => ['config' => ['type' => 'passthrough']],
        'content_element' => ['config' => ['type' => 'passthrough']],
        'title' => [
            'label'  => 'LLL:...',
            'config' => ['type' => 'input', 'size' => 60, 'required' => true],
        ],
        // … more columns …
    ],
    'types' => [
        '1' => ['showitem' => 'title, …'],
    ],
];
```

---

### 4. FlexForm — `Configuration/FlexForms/MyElement.xml`

Optional. Use for display-only settings that live on the `tt_content` record (not child records). Copy `Accordion.xml` as a starting point.

Fields are accessible in templates as `{settings.fieldName}` after the processor resolves them.

---

### 5. Register the CType — `Configuration/TCA/Overrides/tt_content.php`

```php
// Register icon
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['my_ctype'] = 'conn-ce-myelement';

// Add to CType dropdown (choose the right group)
ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
    'label' => 'LLL:...be.xlf:ce.my_ctype',
    'value' => 'my_ctype',
    'icon'  => 'conn-ce-myelement',
    'group' => 'conn_bootstrap',   // or 'conn'
]);

// Add columns to tt_content
ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_conn_elements_myelement_items' => [
        'label'  => 'LLL:...be.xlf:myelement.items',
        'config' => [
            'type'          => 'inline',
            'foreign_table' => 'tx_conn_elements_domain_model_myelement_item',
            'foreign_field' => 'content_element',
            'maxitems'      => 100,
            'appearance'    => [
                'collapseAll'        => true,
                'levelLinksPosition' => 'top',
                'enabledControls'    => ['dragdrop' => true],
            ],
        ],
    ],
    'tx_conn_elements_myelement_settings' => [
        'label'  => 'LLL:...',
        'config' => ['type' => 'flex', 'ds' => ['default' => 'FILE:EXT:conn_elements/Configuration/FlexForms/MyElement.xml']],
    ],
]);

// Backend form layout
$GLOBALS['TCA']['tt_content']['types']['my_ctype'] = [
    'showitem' => '
        --div--;LLL:EXT:core/.../locallang_tabs.xlf:general,
            --palette--;;general, header, tx_conn_elements_myelement_items,
        --div--;LLL:...:myelement.tab.config,
            tx_conn_elements_myelement_settings,
        --div--;LLL:EXT:core/.../locallang_tabs.xlf:access,
            --palette--;;hidden, --palette--;;access,
    ',
];
```

---

### 6. Register the icon — `Configuration/Icons.php`

```php
'conn-ce-myelement' => [
    'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    'source'   => 'EXT:conn_elements/Resources/Public/Icons/myelement-ce.svg',
],
```

Place the SVG at `Resources/Public/Icons/myelement-ce.svg`.

---

### 7. Add to content element wizard — `ext_localconf.php`

For an existing wizard group, append to its `.show` list and add an `.elements` block:

```php
// Existing group — add to show list:
'mod.wizards.newContentElement.wizardItems.conn_bootstrap.show = bs_accordion, my_ctype'

// New element entry:
'mod.wizards.newContentElement.wizardItems.conn_bootstrap.elements.my_ctype {
    iconIdentifier = conn-ce-myelement
    title = LLL:...:ce.my_ctype
    description = LLL:...:ce.my_ctype.description
    tt_content_defValues.CType = my_ctype
}'
```

For a brand-new wizard group also add:
```php
'mod.wizards.newContentElement.wizardItems.conn_mynewgroup.header = My Group Label'
```

---

### 8. TypoScript — `Configuration/TypoScript/ContentElements/MyElement.typoscript`

The glob import in `setup.typoscript` picks this up automatically.

```typoscript
lib.contentElement.templateRootPaths.20 = EXT:conn_elements/Resources/Private/Templates/ContentElements/
lib.contentElement.partialRootPaths.20  = EXT:conn_elements/Resources/Private/Partials/

tt_content.my_ctype =< lib.contentElement
tt_content.my_ctype {
    templateName = MyElement
    dataProcessing {
        10 = Connetation\Elements\DataProcessing\MyElementProcessor
        10 {
            fieldName = tx_conn_elements_myelement_items
            as = items
        }
    }
}
```

---

### 9. Data Processor — `Classes/DataProcessing/MyElementProcessor.php`

```php
<?php
declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class MyElementProcessor implements DataProcessorInterface
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
            return $processedData;
        }

        // Parse FlexForm settings
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            $processedData['data']['tx_conn_elements_myelement_settings'] ?? ''
        );
        $processedData['settings'] = $flexData['settings'] ?? [];

        // Query child records
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_conn_elements_domain_model_myelement_item');

        $processedData[$targetVar] = $qb
            ->select('*')
            ->from('tx_conn_elements_domain_model_myelement_item')
            ->where(
                $qb->expr()->eq('content_element', $qb->createNamedParameter($contentElementUid, ParameterType::INTEGER)),
                $qb->expr()->eq('deleted',          $qb->createNamedParameter(0, ParameterType::INTEGER)),
                $qb->expr()->eq('hidden',           $qb->createNamedParameter(0, ParameterType::INTEGER))
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $processedData;
    }
}
```

---

### 10. Fluid template — `Resources/Private/Templates/ContentElements/MyElement.html`

```html
<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
      data-namespace-typo3-fluid="true">

<f:asset.css identifier="conn-elements-myelement-css"
    href="EXT:conn_elements/Resources/Public/Css/myelement.css" />
<f:asset.script identifier="conn-elements-myelement-js"
    src="EXT:conn_elements/Resources/Public/Js/components/myelement.js"
    type="module" priority="false" />

<section class="conn-myelement" id="conn-myelement-{data.uid}">
    <div class="container-fluid px-4">
        <f:if condition="{data.header}">
            <h2 class="conn-myelement__title">{data.header}</h2>
        </f:if>
        <f:for each="{items}" as="item">
            <f:render partial="MyElement/Item" arguments="{item: item}" />
        </f:for>
    </div>
</section>

</html>
```

Place partials in `Resources/Private/Partials/MyElement/`.

---

### 11. SCSS — `Resources/Private/Scss/myelement.scss` + `components/_myelement.scss`

Entry point (`myelement.scss`):
```scss
@use 'tokens';
@use 'components/myelement';
```

Component file (`components/_myelement.scss`):
```scss
.conn-myelement {
    // use var(--conn-*) tokens for consistent theming
    background: var(--conn-surface);
    color:      var(--conn-text);
}
```

Add the compile command to `package.json`:
```json
"build:scss": "… && sass Resources/Private/Scss/myelement.scss Resources/Public/Css/myelement.css --style=compressed"
```

---

### 12. JavaScript — `Resources/Private/JavaScript/myelement.js`

```js
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.conn-myelement').forEach(el => {
        // initialise component
    });
});
```

Add the entry point to `build.mjs`:
```js
entryPoints: {
    // … existing …
    myelement: 'Resources/Private/JavaScript/myelement.js',
},
```

---

### 13. Language strings

**`locallang_be.xlf`** (backend):
```xml
<trans-unit id="ce.my_ctype"><source>My Element</source></trans-unit>
<trans-unit id="ce.my_ctype.description"><source>Short description for the wizard</source></trans-unit>
<trans-unit id="myelement.tab.config"><source>My Element Config</source></trans-unit>
<trans-unit id="myelement.items"><source>Items</source></trans-unit>
<trans-unit id="myelement.settings"><source>Display Settings</source></trans-unit>
<trans-unit id="model.myelement_item"><source>My Element Item</source></trans-unit>
<trans-unit id="myelement_item.title"><source>Title</source></trans-unit>
```

**`locallang.xlf`** (frontend, if needed):
```xml
<trans-unit id="myelement.empty"><source>No items found.</source></trans-unit>
```

---

### 14. Build & deploy checklist

After completing all steps above:

- [ ] `npm run build` inside the elements package (or via DDEV)
- [ ] **Admin Tools → Database Analyser → Compare** — apply schema changes
- [ ] **Admin Tools → Maintenance → Flush all caches**
- [ ] Verify element appears in the **"New Content Element" wizard** under the correct group
- [ ] Add a test instance of the element in the TYPO3 backend and confirm frontend rendering

### Files changed / created per new element (summary)

| File | Action |
|------|--------|
| `ext_tables.sql` | Add column to `tt_content` + new table |
| `Configuration/TCA/tx_conn_elements_domain_model_*.php` | New TCA model file |
| `Configuration/TCA/Overrides/tt_content.php` | Register CType, columns, showitem |
| `Configuration/FlexForms/MyElement.xml` | New FlexForm (optional) |
| `Configuration/TypoScript/ContentElements/MyElement.typoscript` | New TypoScript CE definition |
| `Configuration/Icons.php` | Register icon identifier |
| `ext_localconf.php` | Add to content element wizard |
| `Classes/DataProcessing/MyElementProcessor.php` | New data processor |
| `Resources/Private/Templates/ContentElements/MyElement.html` | New Fluid template |
| `Resources/Private/Partials/MyElement/*.html` | New Fluid partials |
| `Resources/Private/Scss/myelement.scss` | New SCSS entry point |
| `Resources/Private/Scss/components/_myelement.scss` | New SCSS component |
| `Resources/Private/JavaScript/myelement.js` | New JS source |
| `Resources/Public/Icons/myelement-ce.svg` | New icon SVG |
| `Resources/Private/Language/locallang_be.xlf` | Add BE translation strings |
| `Resources/Private/Language/locallang.xlf` | Add FE translation strings |
| `package.json` | Add SCSS compile command |
| `build.mjs` | Add JS entry point |
