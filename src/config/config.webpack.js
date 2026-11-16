/**
 * Main webpack configuration
 */

import { fileURLToPath } from 'url';
import { merge } from 'webpack-merge';
import * as env from './util/env.js';
import * as pathUtils from './util/paths.js';
import userConfig from './config.user.js';
import moduleConfig from './webpack/config.module.js';
import pluginsConfig from './webpack/config.plugins.js';
import optimizationConfig from './webpack/config.optimization.js';
import watchConfig from './webpack/config.watch.js';

const __filename = fileURLToPath(import.meta.url);

const createWebpackConfig = (envArgs, argv) => {
	const isProduction = argv.mode === 'production';
	const fileName = isProduction ? '[name].[contenthash]' : '[name]';

	const baseConfig = {
		entry: userConfig.entry,
		context: userConfig.paths.src,
		output: {
			path: userConfig.paths.dist,
			publicPath: userConfig.publicPath,
			filename: `scripts/${fileName}.js`,
			clean: true,
			chunkFilename: `scripts/${isProduction ? '[id].[contenthash]' : '[id]'}.chunk.js`,
		},
		mode: argv.mode,
		stats: {
			assets: true,
			colors: true,
			logging: 'warn',
			modules: false,
			entrypoints: false,
		},
		cache: {
			type: 'filesystem',
			buildDependencies: {
				config: [__filename],
			},
			cacheDirectory: pathUtils.paths.cache,
			name: `${isProduction ? 'prod' : 'dev'}-cache`,
		},
		target: 'web',
		devtool: env.getEnvSpecific(isProduction, 'source-map', 'cheap-module-source-map'),
		module: moduleConfig(userConfig, { isProduction, isWatching: env.isWatching }),
		resolve: {
			modules: [userConfig.paths.src, 'node_modules'],
			extensions: ['.js', '.jsx', '.json'],
			enforceExtension: false,
			alias: {
				'@': userConfig.paths.src,
			},
			fallback: {
				"path": false,
				"fs": false,
				"os": false,
				"crypto": false,
				"stream": false,
				"buffer": false,
				"util": false,
			},
		},
		externals: {
			jquery: 'jQuery',
			// Uncomment and extend for block editor / @wordpress packages, e.g.:
			// '@wordpress/element': 'wp.element',
			// '@wordpress/i18n': 'wp.i18n',
			// See agent-skills/webpack-config/SKILL.md and docs/002-Configuration.md.
		},
		performance: {
			hints: env.getEnvSpecific(isProduction, 'warning', false),
			maxEntrypointSize: 512000,
			maxAssetSize: 512000,
		},
		plugins: pluginsConfig(userConfig, { ...env, isProduction }, fileName),
		optimization: optimizationConfig(userConfig, { isProduction }),
	};

	const devServerConfig = isProduction ? {} : watchConfig(userConfig, { isProduction });

	return merge(baseConfig, devServerConfig);
};

export default createWebpackConfig;
