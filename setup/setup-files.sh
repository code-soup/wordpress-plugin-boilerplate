#!/bin/bash

# Update files, let user know
echo "Updating files. Please wait"

# Check the operating system
OS=$(uname)

# Define the directories to scan
directories=("./templates" "./includes")

# Replace the old PHP namespace with the new one in all .php and .phtml files in the current directory, templates, and includes directories and all their subdirectories
for dir in "${directories[@]}"; do
  # Find .php files in the current directory and subdirectories, excluding the vendor directory
  find "$dir" -name "*.php" ! -path "./vendor/*" | while read -r filename; do
    if [[ "$OS" == "Darwin" ]]; then
      # For macOS, use sed with the '-i' option, but provide an empty string for backup
      sed -i '' "s/WPPB/$php_namespace/g" "$filename"
      sed -i '' "s/wppb/$plugin_prefix/g" "$filename"
      sed -i '' "s/__PLUGIN_TEXTDOMAIN__/$plugin_textdomain/g" "$filename"
    else
      # For Linux and Windows (including Git Bash), use dos2unix to convert line endings and then use sed without the '-i' option
      dos2unix "$filename" >/dev/null 2>&1
      sed "s/WPPB/$php_namespace/g" "$filename" >"$filename.tmp" && mv "$filename.tmp" "$filename"
      sed "s/wppb/$plugin_prefix/g" "$filename" >"$filename.tmp" && mv "$filename.tmp" "$filename"
      sed "s/__PLUGIN_TEXTDOMAIN__/$plugin_textdomain/g" "$filename" >"$filename.tmp" && mv "$filename.tmp" "$filename"
    fi
  done
done


# Update Namespace in composer.json
# Escape backslashes for JSON format - "MyName\Space" should be "MyName\\Space"
namespace_composer=("composer.json")

# Create escaped version of namespace for JSON (double backslashes)
php_namespace_escaped=$(echo "$php_namespace" | sed 's/\\/\\\\/g')

# Create sanitized plugin name for composer.json (lowercase, spaces to dashes)
plugin_name_sanitized=$(echo "$plugin_name" | tr '[:upper:]' '[:lower:]' | sed 's/ /-/g')

for file in "${namespace_composer[@]}"; do
  if [[ "$OS" == "Darwin" ]]; then
    # For macOS, use sed with the '-i' option, but provide an empty string for backup
    sed -i '' "s/WPPB/$php_namespace_escaped/g" "$file"
    sed -i '' "s/codesoup\/wppb/codesoup\/$plugin_name_sanitized/g" "$file"

    # Replace description if provided
    if [[ -n "$plugin_description" ]]; then
      sed -i '' "s/\"description\": \"WordPress Plugin Boilerplate\"/\"description\": \"$plugin_description\"/g" "$file"
    fi

    # Replace homepage if provided
    if [[ -n "$plugin_uri" ]]; then
      sed -i '' "s|\"homepage\": \"https://github.com/code-soup/wordpress-plugin-boilerplate\"|\"homepage\": \"$plugin_uri\"|g" "$file"
    fi
  else
    # For Linux and Windows (including Git Bash), use dos2unix to convert line endings and then use sed without the '-i' option
    dos2unix "$file" >/dev/null 2>&1
    sed "s/WPPB/$php_namespace_escaped/g" "$file" >"$file.tmp" && mv "$file.tmp" "$file"
    sed "s/codesoup\/wppb/codesoup\/$plugin_name_sanitized/g" "$file" >"$file.tmp" && mv "$file.tmp" "$file"

    # Replace description if provided
    if [[ -n "$plugin_description" ]]; then
      sed "s/\"description\": \"WordPress Plugin Boilerplate\"/\"description\": \"$plugin_description\"/g" "$file" >"$file.tmp" && mv "$file.tmp" "$file"
    fi

    # Replace homepage if provided
    if [[ -n "$plugin_uri" ]]; then
      sed "s|\"homepage\": \"https://github.com/code-soup/wordpress-plugin-boilerplate\"|\"homepage\": \"$plugin_uri\"|g" "$file" >"$file.tmp" && mv "$file.tmp" "$file"
    fi
  fi
done

# Update Namespace in other files (excluding index.php and composer.json)
namespace_files=("run.php" "uninstall.php" "phpcs.xml.dist")

for file in "${namespace_files[@]}"; do
  if [[ "$OS" == "Darwin" ]]; then
    # For macOS, use sed with the '-i' option, but provide an empty string for backup
    sed -i '' "s/WPPB/$php_namespace/g" "$file"
    sed -i '' "s/__PLUGIN_PREFIX__/$plugin_prefix/g" "$file"
    sed -i '' "s/__PLUGIN_TEXTDOMAIN__/$plugin_textdomain/g" "$file"
  else
    # For Linux and Windows (including Git Bash), use dos2unix to convert line endings and then use sed without the '-i' option
    dos2unix "$file" >/dev/null 2>&1
    sed "s/WPPB/$php_namespace/g" "$file" >"$file.tmp" && mv "$file.tmp" "$file"
    sed "s/__PLUGIN_PREFIX__/$plugin_prefix/g" "$file" >"$file.tmp" && mv "$file.tmp" "$file"
    sed "s/__PLUGIN_TEXTDOMAIN__/$plugin_textdomain/g" "$file" >"$file.tmp" && mv "$file.tmp" "$file"
  fi
done

# Replace the placeholders in run.php
if [[ "$OS" == "Darwin" ]]; then
  # For macOS, use sed with the '-i' option, but provide an empty string for backup
  sed -i '' "s/__PLUGIN_MIN_WP_VERSION__/$requires_wordpress/g" "run.php"
  sed -i '' "s/__PLUGIN_MIN_PHP_VERSION__/$requires_php/g" "run.php"
  sed -i '' "s/__PLUGIN_MIN_MYSQL_VERSION__/$requires_mysql/g" "run.php"
  sed -i '' "s/__PLUGIN_NAME__/$plugin_name/g" "run.php"
  sed -i '' "s/__PLUGIN_VERSION__/$plugin_version/g" "run.php"
else
  # For Linux and Windows (including Git Bash), use dos2unix to convert line endings and then use sed without the '-i' option
  dos2unix "run.php" >/dev/null 2>&1
  sed "s/__PLUGIN_MIN_WP_VERSION__/$requires_wordpress/g" "run.php" >"run.php.tmp" && mv "run.php.tmp" "run.php"
  sed "s/__PLUGIN_MIN_PHP_VERSION__/$requires_php/g" "run.php" >"run.php.tmp" && mv "run.php.tmp" "run.php"
  sed "s/__PLUGIN_MIN_MYSQL_VERSION__/$requires_mysql/g" "run.php" >"run.php.tmp" && mv "run.php.tmp" "run.php"
  sed "s/__PLUGIN_NAME__/$plugin_name/g" "run.php" >"run.php.tmp" && mv "run.php.tmp" "run.php"
  sed "s/__PLUGIN_VERSION__/$plugin_version/g" "run.php" >"run.php.tmp" && mv "run.php.tmp" "run.php"
fi