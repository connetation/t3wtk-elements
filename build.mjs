import * as esbuild from 'esbuild';
import { readdirSync, rmSync } from 'node:fs';
import { join } from 'node:path';

const watch = process.argv.includes('--watch');
const dev   = watch || process.argv.includes('--dev');

/** @type {import('esbuild').BuildOptions} */
const config = {
    entryPoints: {
        accordion:     'Resources/Private/Components/Accordion/accordion.ts',
        'card-group':  'Resources/Private/Components/CardGroup/card-group.ts',
        'hero-slider':     'Resources/Private/Components/HeroSlider/hero-slider.ts',
        slider:            'Resources/Private/Components/Slider/slider.ts',
        'thumbs-slider':   'Resources/Private/Components/ThumbsSlider/thumbs-slider.ts',
        'parallax-slider': 'Resources/Private/Components/ParallaxSlider/parallax-slider.ts',
        'text-animation':  'Resources/Private/Components/TextAnimation/text-animation.ts',
        'card-stack':      'Resources/Private/Components/CardStack/card-stack.ts',
        'shift-carousel':   'Resources/Private/Components/ShiftCarousel/shift-carousel.ts',
        'lightbox-gallery':    'Resources/Private/Components/LightboxGallery/lightbox-gallery.ts',
        'mosaic-gallery':   'Resources/Private/Components/MosaicGallery/mosaic-gallery.ts',
        'navbar-megamenu': 'Resources/Private/Components/Navbar/navbar-megamenu.ts',
        'navbar-sidebar':  'Resources/Private/Components/Navbar/navbar-sidebar.ts',
        'timeline':        'Resources/Private/Components/Timeline/timeline.ts',
        'stats':           'Resources/Private/Components/Stats/stats.ts',
        'logo-wall':       'Resources/Private/Components/LogoWall/logo-wall.ts',
        'testimonials':    'Resources/Private/Components/Testimonials/testimonials.ts',
        'pricing':         'Resources/Private/Components/Pricing/pricing.ts',
        'feature-grid':    'Resources/Private/Components/FeatureGrid/feature-grid.ts',
        'people':          'Resources/Private/Components/People/people.ts',
        'tabs':            'Resources/Private/Components/Tabs/tabs.ts',
        'before-after':    'Resources/Private/Components/BeforeAfter/before-after.ts',
        'process':         'Resources/Private/Components/Process/process.ts',
        'alert':           'Resources/Private/Components/Alert/alert.ts',
        'video-hero':      'Resources/Private/Components/VideoHero/video-hero.ts',
        'map':             'Resources/Private/Components/Map/map.ts',
        'flip-card':       'Resources/Private/Components/FlipCard/flip-card.ts',
    },
    bundle:    true,
    splitting: true,
    format:    'esm',
    outdir:    'Resources/Public/Js/components',
    minify:    !dev,
    sourcemap: dev ? 'inline' : false,
};

// Code splitting mints new content-hashed chunk-*.js names on every build —
// remove previous chunks first so orphans don't accumulate in git.
function cleanChunks() {
    for (const file of readdirSync(config.outdir)) {
        if (/^chunk-[A-Z0-9]+\.js$/.test(file)) {
            rmSync(join(config.outdir, file));
        }
    }
}

if (watch) {
    const ctx = await esbuild.context(config);
    await ctx.watch();
    console.log('[elements] watching…');
} else {
    cleanChunks();
    await esbuild.build(config);
    console.log('[elements] built');
}
