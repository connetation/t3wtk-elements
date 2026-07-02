import * as esbuild from 'esbuild';

const watch = process.argv.includes('--watch');
const dev   = watch || process.argv.includes('--dev');

/** @type {import('esbuild').BuildOptions} */
const config = {
    entryPoints: {
        dashboard:            'Resources/Private/Components/Dashboard/dashboard.ts',
        'dashboard-animations': 'Resources/Private/Components/Dashboard/animations.ts',
        wiki:          'Resources/Private/Components/Wiki/wiki.ts',
        accordion:     'Resources/Private/Components/Accordion/accordion.ts',
        'card-group':  'Resources/Private/Components/CardGroup/card-group.ts',
        'hero-slider':     'Resources/Private/Components/HeroSlider/hero-slider.ts',
        slider:            'Resources/Private/Components/Slider/slider.ts',
        'thumbs-slider':   'Resources/Private/Components/ThumbsSlider/thumbs-slider.ts',
        'parallax-slider': 'Resources/Private/Components/ParallaxSlider/parallax-slider.ts',
        'text-animation':  'Resources/Private/Components/TextAnimation/text-animation.ts',
        'card-stack':      'Resources/Private/Components/CardStack/card-stack.ts',
        'flip-carousel':   'Resources/Private/Components/FlipCarousel/flip-carousel.ts',
        'flip-gallery':    'Resources/Private/Components/FlipGallery/flip-gallery.ts',
        'mosaic-gallery':   'Resources/Private/Components/MosaicGallery/mosaic-gallery.ts',
        'navbar-megamenu': 'Resources/Private/Components/Navbar/navbar-megamenu.ts',
        'navbar-sidebar':  'Resources/Private/Components/Navbar/navbar-sidebar.ts',
        'timeline':        'Resources/Private/Components/Timeline/timeline.ts',
        'stats':           'Resources/Private/Components/Stats/stats.ts',
        'logo-wall':       'Resources/Private/Components/LogoWall/logo-wall.ts',
        'testimonials':    'Resources/Private/Components/Testimonials/testimonials.ts',
        'pricing':         'Resources/Private/Components/Pricing/pricing.ts',
        'feature-grid':    'Resources/Private/Components/FeatureGrid/feature-grid.ts',
    },
    bundle:    true,
    splitting: true,
    format:    'esm',
    outdir:    'Resources/Public/Js/components',
    minify:    !dev,
    sourcemap: dev ? 'inline' : false,
};

if (watch) {
    const ctx = await esbuild.context(config);
    await ctx.watch();
    console.log('[elements] watching…');
} else {
    await esbuild.build(config);
    console.log('[elements] built');
}
