/**
 * Frontend bundle bootstrap (runs on load and after webpack HMR updates).
 */
import hmrHelper from "@/config/util/hmr-helper.js";

function boot() {
	document.addEventListener("DOMContentLoaded", () => {
		// Frontend scripts entry point.
	});
}

hmrHelper(boot);