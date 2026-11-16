#!/bin/bash

# Update files, let user know
echo "Updating files. Please wait"

# Check the operating system
OS=$(uname)

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib-replace.sh
source "${SCRIPT_DIR}/lib-replace.sh"

php_namespace_sed=$(sed_escape_replacement "${php_namespace_raw}")

# Define the directories to scan
directories=("./templates" "./includes")

replace_namespace_and_domain_in_file() {
	local filename="$1"
	replace_in_file "$filename" "WPPB" "${php_namespace_raw}"
	replace_plugin_prefix_tokens "$filename" "${plugin_prefix}"
	replace_in_file "$filename" "__PLUGIN_TEXTDOMAIN__" "${plugin_textdomain}"
}

for dir in "${directories[@]}"; do
	find "$dir" -name "*.php" ! -path "./vendor/*" | while read -r filename; do
		if [[ "$OS" != "Darwin" ]]; then
			dos2unix "$filename" >/dev/null 2>&1
		fi
		replace_namespace_and_domain_in_file "$filename"
	done
done

if [[ -d "./agent-skills" ]]; then
	find "./agent-skills" -name "*.md" | while read -r filename; do
		if [[ "$OS" != "Darwin" ]]; then
			dos2unix "$filename" >/dev/null 2>&1
		fi
		replace_namespace_and_domain_in_file "$filename"
	done
fi

# Update composer.json via Node (avoids fragile sed on JSON / namespaces).
export PHP_NAMESPACE_RAW="${php_namespace_raw}"
export PLUGIN_PREFIX="${plugin_prefix}"
export PLUGIN_NAME="${plugin_name}"
export PLUGIN_DESCRIPTION="${plugin_description}"
export PLUGIN_URI="${plugin_uri}"
node "${SCRIPT_DIR}/update-composer-json.mjs"

namespace_files=(
	"index.php"
	"run.php"
	"uninstall.php"
	"phpcs.xml"
	"includes/core/class-activator.php"
	"includes/core/class-deactivator.php"
)

for file in "${namespace_files[@]}"; do
	if [[ "$OS" != "Darwin" ]]; then
		dos2unix "$file" >/dev/null 2>&1
	fi
	replace_in_file "$file" "WPPB" "${php_namespace_raw}"
	replace_in_file "$file" "__PLUGIN_PREFIX__" "${plugin_prefix}"
	replace_in_file "$file" "__PLUGIN_TEXTDOMAIN__" "${plugin_textdomain}"
done

replace_in_file "run.php" "__PLUGIN_MIN_WP_VERSION__" "${requires_wordpress}"
replace_in_file "run.php" "__PLUGIN_MIN_PHP_VERSION__" "${requires_php}"
replace_in_file "run.php" "__PLUGIN_MIN_MYSQL_VERSION__" "${requires_mysql}"
replace_in_file "run.php" "__PLUGIN_NAME__" "${plugin_name}"
replace_in_file "run.php" "__PLUGIN_VERSION__" "${plugin_version}"

replace_plugin_prefix_tokens "index.php" "${plugin_prefix}"

echo "File updates completed."