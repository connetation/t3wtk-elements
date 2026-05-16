# conn_elements

TYPO3 14 content elements package for Connetation. Currently provides the **Dashboard** content element — an inline project/category showcase with filtering, search, and modal detail view.

## Build

Node dependencies are managed per-package. Run all commands from `packages/elements/`.

```bash
cd packages/elements
npm install
```

### SCSS

```bash
# One-off build (compressed output)
npm run build:scss

# Watch mode
npm run dev:scss
```

Output: `Resources/Public/Css/elements.css`

### JavaScript (ESM)

```bash
# One-off build (minified)
npm run build:js

# Watch mode (inline sourcemap)
npm run dev:js
```

Output: `Resources/Public/Js/elements.js`

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
