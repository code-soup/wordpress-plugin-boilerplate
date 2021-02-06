<?php

namespace wppb;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * Helper methods
 */
trait Utils {

    /**
     * Define plugin constants that
     * 
     * @var array
     */
    private $constants = [
        'MIN_WP_VERSION_SUPPORT_TERMS' => '5.0',
        'MIN_WP_VERSION'               => '5.0',
        'MIN_PHP_VERSION'              => '7.1',
        'MIN_MYSQL_VERSION'            => '5.0.0',
        'PLUGIN_PREFIX'                => 'CS_WPPB_',
        'PLUGIN_NAME'                  => 'WordPress Plugin Boilerplate',
        'PLUGIN_VERSION'               => '1.0.0',
    ];


    /**
     * Return r
     * @return [type] [description]
     */
    private function get_plugin_dir_path( string $sub_path = '' ) {

        return plugin_dir_path( __FILE__ );
    }


    /**
     * Return r
     * @return [type] [description]
     */
    private function get_plugin_dir_url( string $sub_path = '' ) {
        
        return plugin_dir_url( __FILE__ );
    }


    /**
     * 
     */
    private function get_asset( string $filename ) {

        $assets = new Assets(
            $this->get_plugin_dir_url(),
            $this->get_plugin_dir_path()
        );

        return $assets->get( $filename );
    }


    /**
     * Returns PLUGIN_NAME constant
     * 
     * @return string
     */
    private function get_plugin_name() {

        return $this->get_constant( 'PLUGIN_NAME' );
    }


    /**
     * Returns PLUGIN_VERSION constant
     * 
     * @return string
     */
    private function get_plugin_version() {

        return $this->get_constant( 'PLUGIN_VERSION' );
    }


    /**
     * Returns PLUGIN_PREFIX constant as ID
     * Converts to-slug-like-id
     * and appends additional text at the end for custom unique id
     * 
     * @return string
     */
    private function get_plugin_id( string $append = '' ) {

        $dashed = str_replace( '_', '-', $this->get_constant( 'PLUGIN_NAME' ) );

        return sanitize_title( $dashed ) . $append;
    }
    

    /**
     * Get plugin contstant
     * @param  string $name [description]
     * @return [type]       [description]
     */
    private function get_constant( string $name ) {

        // Check if constant is defined first
        if ( ! array_key_exists( $name, $this->constants ) )
        {   
            // Force string to avoid compiler errors
            $to_string = print_r( $name, true );

            // Log to error for debugging
            $this->log_error( 'Invalid constant: ' . $to_string );

            // Exit
            return;
        }
        
        // Return value by key
        return $this->constants[ strtoupper( $name ) ];
    }


    /**
     * Save something to WordPress debug.log
     * Useful for debugging your code, this method will print_r any variable into log
     *
     * @param mixed $message
     */
    private function log_error( $variable ) {

        error_log( print_r( $variable, true ) );
    }
}