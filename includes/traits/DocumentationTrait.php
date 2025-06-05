<?php

declare(strict_types=1);

namespace WPPB\Traits;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Documentation methods for generating plugin metadata and documentation
 *
 * Provides methods for generating comprehensive documentation about the plugin,
 * its services, hooks, and configuration options.
 *
 * @since 1.0.0
 */
trait DocumentationTrait {

	/**
	 * Get plugin information
	 *
	 * @since 1.0.0
	 * @return array<string, mixed> Plugin information
	 */
	private function get_plugin_info(): array {
		return array(
			'name'         => $this->get_plugin_name(),
			'version'      => $this->get_plugin_version(),
			'description'  => 'WordPress Plugin Boilerplate with modern architecture',
			'author'       => 'Plugin Author',
			'text_domain'  => strtolower( str_replace( '_', '-', $this->get_plugin_name() ) ),
			'domain_path'  => '/languages',
			'requires_wp'  => '5.0',
			'requires_php' => '8.0',
			'license'      => 'GPL v2 or later',
			'license_uri'  => 'https://www.gnu.org/licenses/gpl-2.0.html',
		);
	}

	/**
	 * Get plugin features
	 *
	 * @since 1.0.0
	 * @return array<string, array> Plugin features
	 */
	private function get_plugin_features(): array {
		return array(
			'architecture' => array(
				'Dependency Injection Container',
				'Service Provider Pattern',
				'Plugin Lifecycle Management',
				'Modern PHP 8.0+ Features',
				'Strict Type Declarations',
			),
			'security'     => array(
				'Nonce Verification',
				'Capability Checks',
				'Input Sanitization',
				'Output Escaping',
				'SQL Injection Prevention',
			),
			'performance'  => array(
				'Asset Caching',
				'Transient API Usage',
				'Conditional Loading',
				'Optimized Database Queries',
				'Memory Efficient Processing',
			),
			'development'  => array(
				'Comprehensive Logging',
				'Error Handling',
				'Code Documentation',
				'WordPress Coding Standards',
				'Unit Test Ready',
			),
		);
	}

	/**
	 * Get available hooks
	 *
	 * @since 1.0.0
	 * @return array<string, array> Available hooks
	 */
	private function get_available_hooks(): array {
		$plugin_name = strtolower( $this->get_plugin_name() );

		return array(
			'actions' => array(
				"{$plugin_name}_init"               => 'Fired when plugin is initialized',
				"{$plugin_name}_activated"          => 'Fired when plugin is activated',
				"{$plugin_name}_deactivated"        => 'Fired when plugin is deactivated',
				"{$plugin_name}_before_assets_load" => 'Fired before assets are loaded',
				"{$plugin_name}_after_assets_load"  => 'Fired after assets are loaded',
				"{$plugin_name}_admin_init"         => 'Fired when admin is initialized',
				"{$plugin_name}_frontend_init"      => 'Fired when frontend is initialized',
			),
			'filters' => array(
				"{$plugin_name}_options"            => 'Filter plugin options',
				"{$plugin_name}_asset_url"          => 'Filter asset URLs',
				"{$plugin_name}_admin_capabilities" => 'Filter admin capabilities',
				"{$plugin_name}_frontend_scripts"   => 'Filter frontend scripts',
				"{$plugin_name}_admin_scripts"      => 'Filter admin scripts',
				"{$plugin_name}_log_level"          => 'Filter logging level',
			),
		);
	}

	/**
	 * Get service documentation
	 *
	 * @since 1.0.0
	 * @return array<string, array> Service documentation
	 */
	private function get_service_documentation(): array {
		return array(
			'container' => array(
				'class'       => 'WPPB\\Container',
				'description' => 'Dependency injection container for managing services',
				'methods'     => array(
					'register()'  => 'Register a service',
					'singleton()' => 'Register a singleton service',
					'factory()'   => 'Register a factory service',
					'get()'       => 'Resolve a service',
					'has()'       => 'Check if service exists',
				),
			),
			'hooker'    => array(
				'class'       => 'WPPB\\Hooker',
				'description' => 'WordPress hooks management system',
				'methods'     => array(
					'add_action()'  => 'Add WordPress action',
					'add_filter()'  => 'Add WordPress filter',
					'add_actions()' => 'Add multiple actions',
					'add_filters()' => 'Add multiple filters',
					'run()'         => 'Execute all registered hooks',
				),
			),
			'assets'    => array(
				'class'       => 'WPPB\\Assets',
				'description' => 'Asset management with caching and optimization',
				'methods'     => array(
					'get()'    => 'Get asset URL',
					'locate()' => 'Locate asset file',
				),
			),
			'lifecycle' => array(
				'class'       => 'WPPB\\Lifecycle',
				'description' => 'Plugin lifecycle management',
				'methods'     => array(
					'activate()'        => 'Handle plugin activation',
					'deactivate()'      => 'Handle plugin deactivation',
					'uninstall()'       => 'Handle plugin uninstallation',
					'on_activation()'   => 'Register activation callback',
					'on_deactivation()' => 'Register deactivation callback',
					'on_upgrade()'      => 'Register upgrade callback',
				),
			),
		);
	}

	/**
	 * Generate plugin documentation
	 *
	 * @since 1.0.0
	 * @return string Generated documentation
	 */
	private function generate_documentation(): string {
		$info     = $this->get_plugin_info();
		$features = $this->get_plugin_features();
		$hooks    = $this->get_available_hooks();
		$services = $this->get_service_documentation();

		$doc  = "# {$info['name']} Documentation\n\n";
		$doc .= "Version: {$info['version']}\n";
		$doc .= "Description: {$info['description']}\n\n";

		$doc .= "## Features\n\n";
		foreach ( $features as $category => $items ) {
			$doc .= '### ' . ucfirst( $category ) . "\n";
			foreach ( $items as $item ) {
				$doc .= "- {$item}\n";
			}
			$doc .= "\n";
		}

		$doc .= "## Available Hooks\n\n";
		$doc .= "### Actions\n";
		foreach ( $hooks['actions'] as $hook => $description ) {
			$doc .= "- `{$hook}`: {$description}\n";
		}
		$doc .= "\n### Filters\n";
		foreach ( $hooks['filters'] as $hook => $description ) {
			$doc .= "- `{$hook}`: {$description}\n";
		}

		$doc .= "\n## Services\n\n";
		foreach ( $services as $service => $details ) {
			$doc .= "### {$details['class']}\n";
			$doc .= "{$details['description']}\n\n";
			$doc .= "**Methods:**\n";
			foreach ( $details['methods'] as $method => $description ) {
				$doc .= "- `{$method}`: {$description}\n";
			}
			$doc .= "\n";
		}

		return $doc;
	}

	/**
	 * Get plugin requirements
	 *
	 * @since 1.0.0
	 * @return array<string, string> Plugin requirements
	 */
	private function get_plugin_requirements(): array {
		return array(
			'wordpress'  => '5.0+',
			'php'        => '8.0+',
			'memory'     => '128M',
			'extensions' => 'json, mbstring',
		);
	}

	/**
	 * Get configuration options
	 *
	 * @since 1.0.0
	 * @return array<string, array> Configuration options
	 */
	private function get_configuration_options(): array {
		return array(
			'general'  => array(
				'enabled'    => array(
					'type'        => 'boolean',
					'default'     => true,
					'description' => 'Enable/disable plugin functionality',
				),
				'debug_mode' => array(
					'type'        => 'boolean',
					'default'     => false,
					'description' => 'Enable debug logging',
				),
			),
			'features' => array(
				'admin_enhancements' => array(
					'type'        => 'boolean',
					'default'     => true,
					'description' => 'Enable admin enhancements',
				),
				'frontend_features'  => array(
					'type'        => 'boolean',
					'default'     => true,
					'description' => 'Enable frontend features',
				),
			),
		);
	}
}
