> **🚀 Quickstart:** the fastest way to start a new TYPO3 14 project with `connetation/t3wtk-base`, `connetation/t3wtk-elements` and `connetation/t3wtk-sitepackage` is **[t3wtk-quickstart-shell](https://github.com/connetation/t3wtk-quickstart-shell)** — a ready-to-use DDEV project template. Clone it and run `ddev setup-project`.

# T3WTK – Connetation TYPO3 Web Toolkit

From an empty TYPO3 installation to a complete, themeable website in hours instead of weeks.

**Want to explore first?** Visit the live demo at https://demosite.connetation.at or install `connetation/t3wtk-demosite`.

T3WTK is a modular set of TYPO3 extensions for TYPO3 14 LTS. It replaces the groundwork
that has to be redone on almost every project: sitepackage, page templates, navigation,
footer, content elements, dark mode, forms and SEO setup. Once installed via Composer,
your team can start with configuration, design and content right away.

## Packages

| Package | Purpose |
|---|---|
| `connetation/t3wtk-base` | Design system & foundation: page templates, navigation, footer, design tokens, light/dark theme, site settings, default theme |
| `connetation/t3wtk-elements` | 30+ content elements in eight categories |
| `connetation/t3wtk-sitepackage` | Project layer: theme overrides, forms, email templates, SEO configuration |
| `connetation/t3wtk-demosite` | Creates a complete demo page tree with sample content |
| `connetation/t3wtk-themes` | Theme library (in development) |

Project-specific code lives only in the sitepackage, so base and elements can be updated
via Composer like any other dependency without overwriting your customizations.

## Highlights

- **Design once, theme everywhere:** colors, logo, navbar, footer, buttons and border radius
  as TYPO3 site settings. 250+ options, editable in the Sites module.
- **Full dark mode** with its own color palette and theme toggle, per site, no extra CSS
- **30+ content elements:** hero and card sliders, video hero, before/after comparison,
  mosaic and lightbox galleries, accordion, tabs, flip cards, timeline, pricing tables,
  stats/counter, testimonials, team, map & location and more
- **Five container layouts** (2/3/4 columns, narrow/wide, wide/narrow), nestable
- **Shared design options for every element:** spacing, content width up to full-bleed,
  backgrounds (color, image, parallax), scroll and text animations
- **Built on TYPO3 standards:** Site Sets, Fluid, Composer, core "Text & Media" as a base,
  regular TYPO3 content records with no proprietary structures
- **Performance:** responsive images, lazy loading, CSS/JS loaded only for elements actually
  used on a page
- **Accessibility-minded markup:** Bootstrap 5, semantic HTML, clean heading hierarchy,
  keyboard-operable widgets
- **Included:** contact form (Form Framework) with branded email templates, SEO setup via
  cs_seo (Open Graph, Twitter Cards, structured data), favicon/branding set, three icon
  libraries (Bootstrap Icons, Font Awesome, Ionicons)

## Requirements

- TYPO3 14 LTS (tested on 14.3), Composer mode
- Uses `b13/container` and `clickstorm/cs_seo`

## Quick start

1. `composer require connetation/t3wtk-base connetation/t3wtk-elements`
2. Add the T3WTK site set to your site configuration
3. Configure colors, logo, navigation and footer in the site settings
4. Build pages with the ready-made content elements

## Feedback & contributing

T3WTK is a young open-source project. Bug reports, ideas and pull requests are welcome via
GitHub Issues and Discussions.

---

## This package: `connetation/t3wtk-elements`

30+ ready-to-use content elements for TYPO3 14 LTS: sliders, galleries, accordions, tabs, timelines, pricing tables, testimonials, team, maps and more. Part of the Connetation TYPO3 Web Toolkit (T3WTK).

### Responsive images

All image-rendering elements use `<ce:responsiveImage>` (`Classes/ViewHelpers/ResponsiveImageViewHelper.php`) — it outputs a WebP `srcset` ladder with `sizes`, capped at the source file width, honoring the backend image editor crop. SVG/GIF are served unprocessed.

#### Server-side prerequisites (Install Tool)

- `[GFX][processor_effects] = true` (sharpening after downscale — required for crisp output)
- `webp` must be in `[GFX][imagefile_ext]` (default in modern TYPO3 if processor supports it)
- **Editor guideline: hero uploads >= 2560px wide (ladder caps at source width)**

### Build

**Local** — run from `vendor/connetation/t3wtk-elements/`:
```bash
npm install
npm run build        # production build (SCSS + JS)
npm run dev          # watch mode (SCSS + JS in parallel)
npm run build:scss   # SCSS only
npm run build:js     # JS only
```

**DDEV:**
```bash
ddev exec "cd /var/www/html/vendor/connetation/t3wtk-elements && npm run build"
```

### Adding a new content element

Use the scaffold CLI — it generates every required file and patches all registration files automatically.

**Local (Node.js installed on host):**
```bash
cd vendor/connetation/t3wtk-elements
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

#### What gets generated

| File | Purpose |
|------|---------|
| `Configuration/FlexForms/<Name>.xml` | Display settings form |
| `Configuration/TypoScript/ContentElements/<Name>.typoscript` | Frontend rendering setup |
| `Configuration/TCA/tx_conn_t3wtk_elements_domain_model_<name>_item.php` | Child record TCA (with-items only) |
| `Classes/DataProcessing/<Name>ItemsProcessor.php` | DB query + FlexForm resolver (with-items only) |
| `Resources/Private/Templates/ContentElements/<Name>.html` | Main Fluid template |
| `Resources/Private/Partials/<Name>/Item.html` | Item partial (with-items only) |
| `Resources/Private/Components/<Name>/index.scss` | SCSS entry point |
| `Resources/Private/Components/<Name>/_<name>.scss` | SCSS component |
| `Resources/Private/Components/<Name>/<name>.ts` | TypeScript component |
| `Resources/Public/Icons/<name>-ce.svg` | Icon placeholder (replace with real SVG) |

#### What gets patched automatically

`Configuration/Icons.php`, `build.mjs`, `ext_tables.sql`, `ext_localconf.php`,
`Configuration/TCA/Overrides/tt_content.php`, `Resources/Private/Language/locallang_be.xlf`,
`Configuration/TypoScript/setup.typoscript`

All patches are idempotent — re-running the script on an existing element name is safe.

#### After running the scaffold

These steps must be done **in order** — skipping or reordering them causes the "Unknown column" SQL error when saving the element in the backend.

**1. Build assets**

Local:
```bash
cd vendor/connetation/t3wtk-elements && npm run build
```
DDEV:
```bash
ddev exec "cd /var/www/html/vendor/connetation/t3wtk-elements && npm run build"
```

**2. Flush TYPO3 caches** — so TYPO3 picks up the new TCA and SQL definitions

Admin Tools → Maintenance → Flush all caches

**3. Apply database schema** *(with-items elements only)*

Admin Tools → Maintenance → Analyze Database Structure → apply changes

Skipping step 3 leaves the new column missing from the database, which causes `SQL error: Unknown column '...' in 'INSERT INTO'` when saving the element.

**4. Verify** — the element should now appear in the "New Content Element" wizard under the chosen group.

#### Customising the generated element

- **FlexForm** — edit `Configuration/FlexForms/<Name>.xml` to add/change display settings
- **Child record fields** — extend `Configuration/TCA/tx_conn_t3wtk_elements_domain_model_<name>_item.php` and `ext_tables.sql`, then re-run DB analysis
- **Data Processor** — extend `resolveSettings()` and `$rows` enrichment in the generated processor
- **Template** — edit the Fluid template and partial freely
- **SCSS** — edit `_<name>.scss`; uses `var(--conn-*)` design tokens for consistent theming
- **TypeScript** — edit `<name>.ts`; the `registry.register()` call at the bottom wires it to the DOM
- **Icon** — replace `Resources/Public/Icons/<name>-ce.svg` with your SVG (16×16 viewBox recommended)

## License

GPL-2.0-or-later · Developed by Connetation Web Engineering GmbH, Salzburg, Austria
