#!/bin/bash

# Prompt the user for input to generate the WordPress plugin header info
source ./setup/prompt-header.sh

# Create a new index.php file with the plugin header info
source ./setup/setup-index.sh

# Prompt the user for input to generate the .env.local file
source ./setup/prompt-env-local.sh

# Run the remaining setup code
source ./setup/setup-files.sh

# Git setup
source ./setup/setup-git.sh

# Display a message to indicate that the setup is completed
echo "Please run composer install manually before trying to activate plugin"
echo "Plugin setup completed."