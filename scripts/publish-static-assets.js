import { copyFile, mkdir, readFile, writeFile } from 'node:fs/promises';
import { basename, join } from 'node:path';

const root = process.cwd();
const buildPath = join(root, 'public', 'build');
const manifest = JSON.parse(await readFile(join(buildPath, 'manifest.json'), 'utf8'));
const cssFiles = [
    ...(manifest['resources/js/app.js']?.css ?? []),
    manifest['resources/css/app.css']?.file,
];
const fontStylesheet = Object.entries(manifest).find(([key]) => key.startsWith('_fonts-'))?.[1]?.file;

if (!manifest['resources/js/app.js']?.file || !manifest['resources/css/app.css']?.file) {
    throw new Error('Vite manifest is missing the application CSS or JavaScript entry.');
}

if (fontStylesheet) {
    cssFiles.push(fontStylesheet);
}

const publicCss = join(root, 'public', 'css');
const publicFonts = join(root, 'public', 'fonts');
const publicJs = join(root, 'public', 'js');
await Promise.all([mkdir(publicCss, { recursive: true }), mkdir(publicFonts, { recursive: true }), mkdir(publicJs, { recursive: true })]);

const stylesheet = (await Promise.all(
    [...new Set(cssFiles.filter(Boolean))].map((file) => readFile(join(buildPath, file), 'utf8'))
)).join('\n');
await writeFile(join(publicCss, 'app.css'), stylesheet.replaceAll('/build/assets/', '/fonts/'));
await copyFile(join(buildPath, manifest['resources/js/app.js'].file), join(publicJs, 'app.js'));

const fontFiles = Object.values(manifest)
    .map((entry) => entry.file)
    .filter((file) => /\.(woff2?|ttf|otf)$/i.test(file));

await Promise.all(fontFiles.map((file) => copyFile(
    join(buildPath, file),
    join(publicFonts, basename(file))
)));
