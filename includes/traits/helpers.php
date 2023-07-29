<?php

namespace WPPB\Traits;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * Helper methods
 */
trait HelpersTrait {

    /**
     * Return absolute path to plugin dir
     * Always returns path without trailing slash
     *
     * @return [type] [description]
     */
    private function get_plugin_dir_path( string $path = '' ): string {

        // Force baseurl to be plugin root directory.
        $base = dirname( __FILE__, 3 );

        return $this->join_path($base, $path);
    }


    /**
     * Return plugin directory URL
     * Always returns URL without trailing slash
     *
     * @return [type] [description]
     */
    private function get_plugin_dir_url( string $path = '' ): string {

        // Force baseurl to be plugin root directory.
        $base = plugins_url( '/', dirname( __FILE__, 2 ) );

        return $this->join_path($base, $path, '/');
    }


    /**
     * Returns PLUGIN_NAME constant
     *
     * @return string
     */
    private function get_plugin_name(): string {

        return $this->get_constant( 'PLUGIN_NAME' );
    }


    /**
     * Returns PLUGIN_VERSION constant
     *
     * @return string
     */
    private function get_plugin_version(): string {

        return $this->get_constant( 'PLUGIN_VERSION' );
    }


    /**
     * Returns PLUGIN_PREFIX constant as ID
     * Converts to-slug-like-id
     * and appends additional text at the end for custom unique id
     *
     * @return string
     */
    private function get_plugin_id( string $append = '' ): string {

        $dashed = str_replace( '_', '-', $this->get_constant( 'PLUGIN_NAME' ) );

        return sanitize_title( $dashed ) . $append;
    }


    /**
     * Get plugin contstant by name
     *
     * @param  string $name [description]
     * @return [type]       [description]
     */
    private function get_constant( string $name ): string {

        $constant = strtoupper( $name );

        // Check if constant is defined first
        if ( ! defined( $constant ) ) {
            // Force string to avoid compiler errors
            $to_string = print_r( $constant, true );

            // Log to error for debugging
            $this->log( 'Invalid constant requested: ' . $to_string );

            // Exit
            return false;
        }

        // Return value by key
        return constant( $constant );
    }


    /**
     * Join two paths into single absolute path or URL
     * 
     * @param  string $base Base location
     * @param  string $path Path to append
     * @return string       Combined path
     */
    private function join_path( string $base = '', string $path = '', string $seperator = DIRECTORY_SEPARATOR ):string {

        // Strip slashes on both ends.
        if ( $path )
        {
            $path = rtrim($path, '/');
            $path = ltrim($path, '/');
        }

        // Strip trailingslash just in case.
        $base = untrailingslashit($base);
        $url  = array_filter(array($base,$path));
        $url  = implode($seperator,$url);

        return untrailingslashit($url);
    }


    /**
     * Save something to WordPress debug.log
     * Useful for debugging your code, this method will print_r any variable into log
     *
     * @param mixed $message
     */
    private function log( $variable ) {

        error_log( print_r( $variable, true ) );
    }
}
