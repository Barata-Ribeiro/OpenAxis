import inertia from "@inertiajs/vite";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import babel from "@rolldown/plugin-babel";
import tailwindcss from "@tailwindcss/vite";
import react, { reactCompilerPreset } from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import { bunny } from "laravel-vite-plugin/fonts";
import { defineConfig, lazyPlugins } from "vite-plus";

export default defineConfig({
    plugins: lazyPlugins(() => [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.tsx"],
            detectTls: !process.env.CI,
            refresh: true,
        }),
        inertia({ ssr: { cluster: true } }),
        react(),
        babel({ presets: [reactCompilerPreset()] }),
        tailwindcss(),
        wayfinder({ formVariants: true }),
    ]),
    build: {
        target: "esnext",
        minify: "oxc",
        chunkSizeWarningLimit: 1000,
        rolldownOptions: {
            output: {
                entryFileNames: "[hash].js",
                chunkFileNames: "[hash].js",
                assetFileNames: "[hash].[ext]",
                minify: true,
            },
        },
        cssCodeSplit: true,
        sourcemap: false,
        assetsInlineLimit: 4096,
    },
    server: {
        host: 'openaxis.test',
        port: 5173,
        hmr: { host: 'openaxis.test', overlay: false },
        watch: {
            usePolling: false,
            ignored: ['**/.agents/**', '**/.claude/**', '**/.cursor/**', '**/.junie/**', '**/vendor/**'],
        },
    lint: {
        jsPlugins: [
            { name: 'vite-plus', specifier: 'vite-plus/oxlint-plugin' },
            { name: '@shadcn/lint', specifier: '@shadcn/lint' },
        ],
        rules: {
            'vite-plus/prefer-vite-plus-imports': 'error',
            '@shadcn/lint/no-arbitrary-values': 'error',
            'typescript/consistent-type-imports': [
                'error',
                {
                    disallowTypeAnnotations: true,
                    fixStyle: 'separate-type-imports',
                    prefer: 'type-imports',
                },
            ],
        },
        ignorePatterns: [
            'vendor/**',
            'node_modules/**',
            'public/**',
            'bootstrap/ssr/**',
            'tailwind.config.js',
            'resources/js/actions/**',
            'resources/js/components/ui/*',
            'resources/js/routes/**',
            'resources/js/wayfinder/**',
        ],
        options: { denyWarnings: true, typeAware: true },
    },
    fmt: {
        printWidth: 120,
        tabWidth: 4,
        singleQuote: true,
        semi: true,
        singleAttributePerLine: false,
        htmlWhitespaceSensitivity: "css",
        ignorePatterns: [
            ".github/**",
            "composer.json",
            "resources/js/components/ui/*",
            "resources/views/mail/*",
        ],
        sortTailwindcss: {
            functions: ["clsx", "cn", "cva"],
            stylesheet: "resources/css/app.css",
        },
    },
});
