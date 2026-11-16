/**
 * Update composer.json during npm run setup (namespace-safe replacements).
 */
import fs from "fs";

const composerPath = "composer.json";
const namespaceRaw = process.env.PHP_NAMESPACE_RAW ?? "";
const pluginPrefix = process.env.PLUGIN_PREFIX ?? "";
const pluginName = process.env.PLUGIN_NAME ?? "plugin";
const pluginDescription = process.env.PLUGIN_DESCRIPTION ?? "";
const pluginUri = process.env.PLUGIN_URI ?? "";

const composer = JSON.parse(fs.readFileSync(composerPath, "utf8"));

const packageSlug = pluginName
	.toLowerCase()
	.replace(/\s+/g, "-")
	.replace(/[^a-z0-9-]/g, "-")
	.replace(/-+/g, "-")
	.replace(/^-|-$/g, "");

composer.name = `codesoup/${packageSlug || "plugin"}`;

if (pluginDescription) {
	composer.description = pluginDescription;
}

if (pluginUri) {
	composer.homepage = pluginUri;
}

if (Array.isArray(composer.keywords) && pluginPrefix) {
	composer.keywords = composer.keywords.map((keyword) =>
		keyword === "wppb" ? pluginPrefix : keyword,
	);
}

// Reserved for future PSR-4 mapping; namespace is primarily PHP/autoloader-driven today.
if (namespaceRaw) {
	composer.extra = composer.extra ?? {};
	composer.extra.php_namespace = namespaceRaw;
}

fs.writeFileSync(composerPath, JSON.stringify(composer, null, "\t") + "\n");