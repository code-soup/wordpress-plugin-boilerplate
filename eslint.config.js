// @ts-nocheck
import globals from 'globals';
import js from '@eslint/js';

export default [
	// Apply recommended defaults
	js.configs.recommended,
	
	// Your custom configuration
	{
		files: ['**/*.js'],
		languageOptions: {
			// Specify that we are using modern ECMAScript features
			ecmaVersion: 'latest',
			// CRITICAL: This tells ESLint to parse the files as ES Modules
			sourceType: 'module',
			// Define global variables available in the environment
			globals: {
				...globals.browser,
				...globals.node,
			},
		},
		rules: {
			// Customize your rules here
			'no-unused-vars': 'warn',
			'no-console': 'off',
		},
	},
];