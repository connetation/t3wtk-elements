import * as esbuild from 'esbuild';

const watch = process.argv.includes('--watch');
const dev   = watch || process.argv.includes('--dev');

/** @type {import('esbuild').BuildOptions} */
const config = {
    entryPoints: {
        dashboard:     'Resources/Private/Components/Dashboard/dashboard.js',
        wiki:          'Resources/Private/Components/Wiki/wiki.js',
        accordion:     'Resources/Private/Components/Accordion/accordion.js',
        'card-group':  'Resources/Private/Components/CardGroup/card-group.js',
        'hero-slider': 'Resources/Private/Components/HeroSlider/hero-slider.js',
        slider:        'Resources/Private/Components/Slider/slider.js',
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
