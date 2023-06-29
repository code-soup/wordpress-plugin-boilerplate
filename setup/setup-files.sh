#!/bin/bash

# Replace the old PHP namespace with the new one in all .php and .phtml files in the current directory, templates, and includes directories and all their subdirectories
# Define the directories to scan
directories=("./templates" "./includes")

# Loop over the directories and scan for .php files
for dir in "${directories[@]}"; do
  find "$dir" -name "*.php" ! -path "./vendor/*" | while read -r filename; do
    sed -i '' "s/WPPB/$php_namespace/g" "$filename"
    sed -i '' "s/wppb/$plugin_prefix/g" "$filename"
  done
done

# Replace the placeholders in includes/class-init.php
sed -i '' "s/__PLUGIN_MIN_WP_VERSION__/$requires_wordpress/g" "includes/class-init.php"
sed -i '' "s/__PLUGIN_MIN_PHP_VERSION__/$requires_php/g" "includes/class-init.php"
sed -i '' "s/__PLUGIN_MIN_MYSQL_VERSION__/$requires_mysql/g" "includes/class-init.php"
sed -i '' "s/__PLUGIN_PREFIX__/$plugin_prefix/g" "includes/class-init.php"
sed -i '' "s/__PLUGIN_NAME__/$plugin_name/g" "includes/class-init.php"
sed -i '' "s/__PLUGIN_VERSION__/$plugin_version/g" "includes/class-init.php"
