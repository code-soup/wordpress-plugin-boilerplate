<?php

namespace WPPB;

use WPPB\Interfaces\AssetsInterface;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * @file
 * Get paths for assets
 *
 * @since 1.0.0
 */
class Assets implements AssetsInterface {

    use Traits\HelpersTrait;


    /**
     * Manifest file object containing list of all hashed assets
     *
     * @var array<string, string>
     * @since 1.0.0
     */
    private $manifest;


    /**
     * URI to theme 'dist' folder
     *
     * @var string
     * @since 1.0.0
     */
    private $dist_uri;


    /**
     * Initiate
     *
     * @since 1.0.0
     */
    public function __construct() {

        $this->dist_uri = $this->get_plugin_dir_url('/dist');
        $manifest_path  = $this->get_plugin_dir_path('/dist/assets.json');

        /**
         * Use built in WordPress classes
         */
        if ( ! defined('FS_CHMOD_DIR') ) {
            define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );    
        }
        
        if ( ! defined('FS_CHMOD_FILE') ) {
            define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
        }

        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        $fs = new \WP_Filesystem_Direct('');

        /**
         * Test for assets.json
         */
        $this->manifest = $fs->exists($manifest_path)
            ? json_decode( $fs->get_contents($manifest_path), true )
            : array();
    }


    /**
     * Get full URI to single asset
     *
     * @since 1.0.0
     * @param  string $filename File name
     * @return string           URI to resource
     */
    public function get( string $filename = '' ): string {

        return $this->locate( $filename );
    }



    /**
     * Fix URL for requested files
     *
     * @since 1.0.0
     * @param  string $filename Requested asset
     * @return string           URL to the asset
     */
    private function locate( string $filename = '' ):string {

        // Trim slashes just in case.
        $filename = rtrim($filename, '/');
        $filename = ltrim($filename, '/');

        // Return URL to requested file from manifest.
        if ( array_key_exists( $filename, $this->manifest ) ) {
            return sprintf( '%s/%s', $this->dist_uri, $this->manifest[ $filename ] );
        }

        switch( pathinfo($filename, PATHINFO_EXTENSION) )
        {
            case 'js':
                $filename = sprintf('scripts/%s', $filename);
            break;

            case 'css':
                $filename = sprintf('styles/%s', $filename);
            break;
        }

        // Return default file location.
        return sprintf( '%s/%s', $this->dist_uri, $filename );
    }
}
