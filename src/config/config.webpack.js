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
		target: ['web', 'es5'],
		devtool: env.getEnvSpecific(isProduction, 'source-map', 'cheap-module-source-map'),
		module: moduleConfig(userConfig, { isProduction }),
		resolve: {
			modules: [userConfig.paths.src, 'node_modules'],
			extensions: ['.js', '.jsx', '.ts', '.tsx', '.json'],
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
		},
		performance: {
			hints: env.getEnvSpecific(isProduction, 'warning', false),
			maxEntrypointSize: 1000000,
			maxAssetSize: 1000000,
		},
		plugins: pluginsConfig(userConfig, { ...env, isProduction }, fileName),
		optimization: optimizationConfig(userConfig, { isProduction }),
	};

	// ------------------------------------------------------------------
	// Inject HMR bootstrap helper into every entry when running via
	// `webpack serve` (watch mode). This guarantees the helper is NEVER
	// bundled in a normal `webpack --mode development` or production build.
	// ------------------------------------------------------------------
	if ( env.isWatching ) {
		const hmrHelper = pathUtils.fromConfig( 'util/hmr-helper.js' );

		// Support both object and function forms of `entry`.
		const originalEntries =
			typeof baseConfig.entry === 'function'
				? baseConfig.entry()
				: { ...baseConfig.entry };

		Object.keys( originalEntries ).forEach( ( key ) => {
			const value = originalEntries[ key ];
			originalEntries[ key ] = Array.isArray( value ) ? [ hmrHelper, ...value ] : [ hmrHelper, value ];
		} );

		baseConfig.entry = originalEntries;
	}

	const devServerConfig = isProduction ? {} : watchConfig(userConfig, { isProduction });

	return merge(baseConfig, devServerConfig);
};

export default createWebpackConfig;
