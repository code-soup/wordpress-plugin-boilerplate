# Installation

This guide will walk you through the process of setting up the WordPress Plugin Boilerplate for a new project.

### Prerequisites

Before you begin, please ensure you have the following installed on your local machine:

-   PHP >= 8.1
-   Node.js >= 22.0
-   npm (comes with Node.js)
-   Composer

### 1. Clone the Repository

First, clone the boilerplate from GitHub into a new directory for your plugin. It's recommended to give the directory the name of your new plugin.

```bash
git clone https://github.com/code-soup/wordpress-plugin-boilerplate.git my-new-plugin
cd my-new-plugin
```

### 2. Run the Setup Script

The boilerplate includes an interactive setup script that will customize the files with your plugin's specific details (like name, prefix, text domain, etc.).

Before running the script, you must make it executable.

```bash
# Make the script executable
chmod u+x setup.sh

# Run the interactive setup script
sh setup.sh
```
The script will prompt you for several details. Once it's finished, the boilerplate will be configured for your project.

### 3. Install Dependencies

With the initial setup complete, you can now install the necessary PHP and Node.js dependencies.

```bash
# Install PHP dependencies via Composer
composer install

# Install Node.js dependencies via npm
npm install
```

### 4. Start Development

Your environment is now fully configured. To start the development server, which provides live-reloading and asset compilation, run the following command:

```bash
npm run dev
```

You can now begin developing your plugin.
