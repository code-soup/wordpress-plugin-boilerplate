// @ts-nocheck
import globals from 'globals';
import js from '@eslint/js';
import pluginImport from 'eslint-plugin-import';

export default [
	// Global ignores
	{
		ignores: [
			'node_modules/**',
			'vendor/**',
			'dist/**',
			'build/**',
		],
	},

	// Main configuration for Browser/ESM source code
	{
		files: ['src/entry/**/*.js', 'src/scripts/**/*.js'],
		plugins: {
			import: pluginImport,
		},
		languageOptions: {
			ecmaVersion: 'latest',
			sourceType: 'module',
			globals: {
				...globals.browser,
				...globals.jquery,
				// Custom Globals
				wp: true,
				cs: true,
				google: true,
			},
		},
		rules: {
			...js.configs.recommended.rules,
			'comma-dangle': [
				'error',
				{
					arrays: 'always-multiline',
					objects: 'always-multiline',
					imports: 'always-multiline',
					exports: 'always-multiline',
					functions: 'ignore',
				},
			],
			'no-unused-vars': [
				'error',
				{
					varsIgnorePattern: '^_',
					argsIgnorePattern: '^_',
					caughtErrorsIgnorePattern: '^_',
				},
			],
		},
	},

	// Separate configuration for Webpack/Node.js (CommonJS) config files
	{
		files: ['src/config/**/*.js'],
		languageOptions: {
			sourceType: 'commonjs',
			globals: {
				...globals.node,
			},
		},
		rules: {
			...js.configs.recommended.rules,
		},
	},
];