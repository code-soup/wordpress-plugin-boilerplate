#!/bin/bash

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
    else
      # For Linux and Windows (including Git Bash), use sed without the '-i' option
      sed "s/WPPB/$php_namespace/g" "$filename" > "$filename.tmp" && mv "$filename.tmp" "$filename"
      sed "s/wppb/$plugin_prefix/g" "$filename" > "$filename.tmp" && mv "$filename.tmp" "$filename"
    fi
  done
done

# Update Namespace in run.php
if [[ "$OS" == "Darwin" ]]; then
  # For macOS, use sed with the '-i' option, but provide an empty string for backup
  sed -i '' "s/WPPB/$php_namespace/g" "run.php"
else
  # For Linux and Windows (including Git Bash), use sed without the '-i' option
  sed "s/WPPB/$php_namespace/g" "run.php" > "run.php.tmp" && mv "run.php.tmp" "run.php"
fi

# Replace the placeholders in includes/class-init.php
if [[ "$OS" == "Darwin" ]]; then
  # For macOS, use sed with the '-i' option, but provide an empty string for backup
  sed -i '' "s/__PLUGIN_MIN_WP_VERSION__/$requires_wordpress/g" "includes/class-init.php"
  sed -i '' "s/__PLUGIN_MIN_PHP_VERSION__/$requires_php/g" "includes/class-init.php"
  sed -i '' "s/__PLUGIN_MIN_MYSQL_VERSION__/$requires_mysql/g" "includes/class-init.php"
  sed -i '' "s/__PLUGIN_PREFIX__/$plugin_prefix/g" "includes/class-init.php"
  sed -i '' "s/__PLUGIN_NAME__/$plugin_name/g" "includes/class-init.php"
  sed -i '' "s/__PLUGIN_VERSION__/$plugin_version/g" "includes/class-init.php"
else
  # For Linux and Windows (including Git Bash), use sed without the '-i' option
  sed "s/__PLUGIN_MIN_WP_VERSION__/$requires_wordpress/g" "includes/class-init.php" > "includes/class-init.php.tmp" && mv "includes/class-init.php.tmp" "includes/class-init.php"
  sed "s/__PLUGIN_MIN_PHP_VERSION__/$requires_php/g" "includes/class-init.php" > "includes/class-init.php.tmp" && mv "includes/class-init.php.tmp" "includes/class-init.php"
  sed "s/__PLUGIN_MIN_MYSQL_VERSION__/$requires_mysql/g" "includes/class-init.php" > "includes/class-init.php.tmp" && mv "includes/class-init.php.tmp" "includes/class-init.php"
  sed "s/__PLUGIN_PREFIX__/$plugin_prefix/g" "includes/class-init.php" > "includes/class-init.php.tmp" && mv "includes/class-init.php.tmp" "includes/class-init.php"
  sed "s/__PLUGIN_NAME__/$plugin_name/g" "includes/class-init.php" > "includes/class-init.php.tmp" && mv "includes/class-init.php.tmp" "includes/class-init.php"
  sed "s/__PLUGIN_VERSION__/$plugin_version/g" "includes/class-init.php" > "includes/class-init.php.tmp" && mv "includes/class-init.php.tmp" "includes/class-init.php"
fi
