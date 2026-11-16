#!/bin/bash

# Shared helpers for safe placeholder replacement in setup scripts.

# Escape string for use as sed replacement with | delimiter.
sed_escape_replacement() {
	printf '%s' "$1" | sed -e 's/[&|\\]/\\&/g'
}

# Replace first pattern in file (mac: in-place, linux: temp file).
replace_in_file() {
	local file="$1"
	local pattern="$2"
	local replacement="$3"
	local escaped
	escaped=$(sed_escape_replacement "$replacement")

	if [[ "$OS" == "Darwin" ]]; then
		sed -i '' "s|${pattern}|${escaped}|g" "$file"
	else
		sed "s|${pattern}|${escaped}|g" "$file" >"${file}.tmp" && mv "${file}.tmp" "$file"
	fi
}

# Replace wppb handle/prefix tokens without touching unrelated substrings (e.g. mywppb).
replace_plugin_prefix_tokens() {
	local file="$1"
	local prefix="$2"
	local escaped_prefix
	escaped_prefix=$(sed_escape_replacement "$prefix")

	if [[ "$OS" == "Darwin" ]]; then
		sed -i '' "s|wppb-|${escaped_prefix}-|g" "$file"
		sed -i '' "s|'wppb|'${escaped_prefix}|g" "$file"
		sed -i '' "s|\"wppb|\"${escaped_prefix}|g" "$file"
		sed -i '' "s|wppb_|${escaped_prefix}_|g" "$file"
	else
		sed "s|wppb-|${escaped_prefix}-|g" "$file" >"${file}.tmp" && mv "${file}.tmp" "$file"
		sed "s|'wppb|'${escaped_prefix}|g" "$file" >"${file}.tmp" && mv "${file}.tmp" "$file"
		sed "s|\"wppb|\"${escaped_prefix}|g" "$file" >"${file}.tmp" && mv "${file}.tmp" "$file"
		sed "s|wppb_|${escaped_prefix}_|g" "$file" >"${file}.tmp" && mv "${file}.tmp" "$file"
	fi
}