/**
 * Connetation Elements – SCSS build
 *
 * Auto-discovers every Resources/Private/Components/<Name>/index.scss and
 * compiles it to Resources/Public/Css/<kebab-name>.css. No per-component
 * registration needed — scaffolding a new element (bin/make-element.mjs)
 * only has to create the index.scss; it's picked up automatically here.
 */

import { readdirSync, statSync, existsSync } from 'node:fs';
import { join } from 'node:path';
import { spawnSync } from 'node:child_process';

const watch = process.argv.includes('--watch');

const COMPONENTS_DIR = 'Resources/Private/Components';
const OUT_DIR = 'Resources/Public/Css';

function toKebab(pascal) {
    return pascal
        .replace(/([A-Z]+)([A-Z][a-z])/g, '$1-$2')
        .replace(/([a-z\d])([A-Z])/g, '$1-$2')
        .toLowerCase()
        .replace(/^-/, '');
}

const entries = readdirSync(COMPONENTS_DIR)
    .filter((name) => statSync(join(COMPONENTS_DIR, name)).isDirectory())
    .filter((name) => existsSync(join(COMPONENTS_DIR, name, 'index.scss')))
    .sort()
    .map((name) => ({
        src: `${COMPONENTS_DIR}/${name}/index.scss`,
        dest: `${OUT_DIR}/${toKebab(name)}.css`,
    }));

// connetation/t3wtk-elements requires connetation/t3wtk-base (see composer.json), so vendor/connetation/t3wtk-base is
// always a sibling directory of this package under vendor/connetation/ — load-path
// lets Shared/_tokens.scss `@use` connetation/t3wtk-base's token mixins at compile time
// without creating a runtime dependency (each component's CSS still ships
// with the values baked in).
const BASE_SCSS = '../t3wtk-base/Resources/Private/Scss';

const pairs = entries.map((e) => `${e.src}:${e.dest}`);
const args = watch
    ? ['--watch', `--load-path=${BASE_SCSS}`, ...pairs]
    : [...pairs, '--style=compressed', `--load-path=${BASE_SCSS}`];

const result = spawnSync('npx', ['sass', ...args], { stdio: 'inherit' });
process.exit(result.status ?? 1);
