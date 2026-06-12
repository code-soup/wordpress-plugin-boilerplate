// @ts-nocheck
import globals from "globals";
import js from "@eslint/js";
import pluginImport from "eslint-plugin-import-x";

export default [
	// Apply recommended defaults
	js.configs.recommended,

	// Your custom configuration
	{
		files: ["**/*.js"],
		plugins: {
			import: pluginImport,
		},
		languageOptions: {
			// Specify that we are using modern ECMAScript features
			ecmaVersion: "latest",
			// CRITICAL: This tells ESLint to parse the files as ES Modules
			sourceType: "module",
			// Define global variables available in the environment
			globals: {
				...globals.browser,
				...globals.node,
				...globals.jquery,
				wp: "readonly",
				google: "readonly",
			},
		},
		rules: {
			// Customize your rules here
			"no-unused-vars": "warn",
			"no-console": "off",
			"comma-dangle": [
				"error",
				{
					arrays: "always-multiline",
					objects: "always-multiline",
					imports: "always-multiline",
					exports: "always-multiline",
					functions: "ignore",
				},
			],
		},
		settings: {
			"import-x/ignore": [
				"node_modules",
				"\\.(coffee|scss|css|less|hbs|svg|json)$",
			],
		},
	},
];
