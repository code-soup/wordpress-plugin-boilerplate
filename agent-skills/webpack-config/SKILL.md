---
name: webpack-config
description: Modify webpack configuration in src/config/ - add entry points, loaders, plugins, aliases. Use when user needs to customize webpack build, add React/TypeScript, modify Babel, add PostCSS plugins, or change dev server settings.
---

# Webpack Configuration

Modular webpack config in `src/config/`. Never create monolithic webpack.config.js.

## Structure

- `config.user.js` - Entry points (user edits this)
- `config.webpack.js` - Main config
- `webpack/config.module.js` - Module rules
- `webpack/config.plugins.js` - Plugins
- `webpack/config.watch.js` - Dev server
- `webpack/loaders/*.js` - Individual loaders
- `util/*.js` - Utilities

## Add Entry Point

Edit `src/config/config.user.js`:

```javascript
entry: {
	"admin-common": ["./scripts/admin.js", "./styles/admin.scss"],
	"main-common": ["./scripts/main.js", "./styles/main.scss"],
	"new-bundle": ["./scripts/new.js", "./styles/new.scss"],
},
```

Output: `dist/scripts/new-bundle.js` and `dist/styles/new-bundle.css`

## Add Custom Loader

1. Create `src/config/webpack/loaders/custom.js`:

```javascript
export default (config, env) => ({
	test: /\.custom$/,
	use: [{ loader: 'custom-loader', options: {} }],
});
```

2. Import in `src/config/webpack/config.module.js`:

```javascript
import customLoaders from './loaders/custom.js';

export default (config, env) => ({
	rules: [
		preLoaders(config, env),
		scriptLoaders(config, env),
		styleLoaders(config, env),
		assetLoaders(config, env),
		customLoaders(config, env),
	],
});
```

## Add Plugin

Edit `src/config/webpack/config.plugins.js`:

```javascript
import CustomPlugin from 'custom-webpack-plugin';

const contextPlugins = conditionalPlugins([
	// ... existing plugins
	{
		condition: env.isProduction, // or true, env.isWatching, etc.
		factory: () => new CustomPlugin({ option: 'value' }),
	},
]);
```

Use `conditionalPlugins` utility. Available conditions: `env.isProduction`, `env.isWatching`, `env.isAnalyzing`, `env.hasSvgIcons`

## Add React/TypeScript

Edit `src/config/webpack/loaders/scripts.js`:

```javascript
options: {
	cacheDirectory: true,
	presets: [
		'@babel/preset-env',
		'@babel/preset-react',      // React
		'@babel/preset-typescript',  // TypeScript
	],
},
```

## Add PostCSS Plugin

Edit `src/config/webpack/loaders/styles.js`, find `postcss-loader`:

```javascript
postcssOptions: {
	plugins: [
		['postcss-preset-env', { stage: 3 }],
		'autoprefixer',
		'postcss-custom-media',
	],
},
```

## Add Alias

Edit `src/config/config.webpack.js`, find `resolve.alias`:

```javascript
alias: {
	'@': userConfig.paths.src,
	'@components': pathUtils.fromSrc('scripts/components'),
	'@utils': pathUtils.fromSrc('scripts/utils'),
},
```

Use: `import Component from '@components/Component';`

## Modify Dev Server

Edit `src/config/webpack/config.watch.js`:

```javascript
devServer: {
	// Add custom headers
	headers: { 'Access-Control-Allow-Origin': '*' },

	// Watch additional files
	watchFiles: {
		paths: [
			`${config.paths.root}/templates/**/*.php`,
			`${config.paths.root}/custom/**/*.php`,
		],
	},
},
```

## Add Environment Variable

1. Add to `.env.local`: `CUSTOM_MODE=true`
2. Use in `src/config/util/env.js`: `export const isCustomMode = !!process.env.CUSTOM_MODE;`
3. Use in configs: `if (env.isCustomMode) { ... }`

## Rules

- Use modular config (never monolithic webpack.config.js)
- Add loaders to `loaders/` directory
- Use `conditionalPlugins` utility
- Use path utilities from `util/paths.js`
- Use env utilities from `util/env.js`
- Test both dev and production modes
