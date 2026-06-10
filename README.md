# conn/elements

TYPO3 14 content elements package for Connetation.

## Build

**Local** — run from `vendor/conn/elements/`:
```bash
npm install
npm run build        # production build (SCSS + JS)
npm run dev          # watch mode (SCSS + JS in parallel)
npm run build:scss   # SCSS only
npm run build:js     # JS only
```

**DDEV:**
```bash
ddev exec "cd /var/www/html/vendor/conn/elements && npm run build"
```

## Adding a new content element

Use the scaffold CLI — it generates every required file and patches all registration files automatically.

**Local (Node.js installed on host):**
```bash
cd vendor/conn/elements
npm run make-element
```

**Inside DDEV** (recommended — no local Node.js required):
```bash
ddev make-element
```

Or pass the element name directly to skip the first prompt:
```bash
ddev make-element "Hero Banner"
```

Under the hood `ddev make-element` is a custom host command at `.ddev/commands/host/make-element`. It opens an interactive terminal inside the web container (`ddev exec -it`) so readline prompts work correctly. Running plain `ddev exec` without `-it` would hang waiting for input.

It asks three questions:

1. **Element name** — e.g. `Hero Banner`, `Testimonial`, `Pricing Table`
2. **Type** — Simple (FlexForm settings only) or With inline child records (like Accordion / Cards)
3. **CE group** — `conn_bootstrap`, `conn`, or `conn_swiper`

### What gets generated

| File | Purpose |
|------|---------|
| `Configuration/FlexForms/<Name>.xml` | Display settings form |
| `Configuration/TypoScript/ContentElements/<Name>.typoscript` | Frontend rendering setup |
| `Configuration/TCA/tx_conn_elements_domain_model_<name>_item.php` | Child record TCA (with-items only) |
| `Classes/DataProcessing/<Name>ItemsProcessor.php` | DB query + FlexForm resolver (with-items only) |
| `Resources/Private/Templates/ContentElements/<Name>.html` | Main Fluid template |
| `Resources/Private/Partials/<Name>/Item.html` | Item partial (with-items only) |
| `Resources/Private/Components/<Name>/index.scss` | SCSS entry point |
| `Resources/Private/Components/<Name>/_<name>.scss` | SCSS component |
| `Resources/Private/Components/<Name>/<name>.ts` | TypeScript component |
| `Resources/Public/Icons/<name>-ce.svg` | Icon placeholder (replace with real SVG) |

### What gets patched automatically

`Configuration/Icons.php`, `build.mjs`, `ext_tables.sql`, `ext_localconf.php`,
`Configuration/TCA/Overrides/tt_content.php`, `Resources/Private/Language/locallang_be.xlf`,
`Configuration/TypoScript/setup.typoscript`

All patches are idempotent — re-running the script on an existing element name is safe.

### After running the scaffold

These steps must be done **in order** — skipping or reordering them causes the "Unknown column" SQL error when saving the element in the backend.

**1. Build assets**

Local:
```bash
cd vendor/conn/elements && npm run build
```
DDEV:
```bash
ddev exec "cd /var/www/html/vendor/conn/elements && npm run build"
```

**2. Flush TYPO3 caches** — so TYPO3 picks up the new TCA and SQL definitions

Admin Tools → Maintenance → Flush all caches

**3. Apply database schema** *(with-items elements only)*

Admin Tools → Maintenance → Analyze Database Structure → apply changes

Skipping step 3 leaves the new column missing from the database, which causes `SQL error: Unknown column '...' in 'INSERT INTO'` when saving the element.

**4. Verify** — the element should now appear in the "New Content Element" wizard under the chosen group.

### Customising the generated element

- **FlexForm** — edit `Configuration/FlexForms/<Name>.xml` to add/change display settings
- **Child record fields** — extend `Configuration/TCA/tx_conn_elements_domain_model_<name>_item.php` and `ext_tables.sql`, then re-run DB analysis
- **Data Processor** — extend `resolveSettings()` and `$rows` enrichment in the generated processor
- **Template** — edit the Fluid template and partial freely
- **SCSS** — edit `_<name>.scss`; uses `var(--conn-*)` design tokens for consistent theming
- **TypeScript** — edit `<name>.ts`; the `registry.register()` call at the bottom wires it to the DOM
- **Icon** — replace `Resources/Public/Icons/<name>-ce.svg` with your SVG (16×16 viewBox recommended)
