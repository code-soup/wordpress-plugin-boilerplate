#!/bin/bash

# Prompt the user to confirm git setup
read -rp "Do you want to run git setup? (Recommended. This will remove Boilerplate history and create new clean git repository) (y/n): " run_git_setup

if [[ "$run_git_setup" == "y" || "$run_git_setup" == "Y" ]]; then
  # Delete existing .git directory if it exists
  if [[ -d ".git" ]]; then
    rm -rf .git
  fi

  # Initialize a new git repository
  git init

  # Add all files to the repository
  git add .

  # Commit the changes with the message 'plugin setup'
  git commit -m "plugin setup"

  echo "Git setup completed."
else
  echo "Git setup skipped."
fi
