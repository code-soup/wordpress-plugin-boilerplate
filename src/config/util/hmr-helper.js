/**
 * hmr-helper.js
 *
 * Ensures an init callback runs on first load and after every hot-update
 * when running under webpack-dev-server (watch mode).
 */

export default function hmrHelper(init) {
	const run = () => {
		try {
			init();
		} catch (e) {
			console.error('[hmr-helper] init() failed:', e);
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run, { once: true });
	} else {
		run();
	}

	if (import.meta.webpackHot) {
		import.meta.webpackHot.accept(run);
	}
} 