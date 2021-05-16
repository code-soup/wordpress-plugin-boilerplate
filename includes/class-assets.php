<?php

namespace WPPB;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * @file
 * Get paths for assets
 */
class Assets {

    use Traits\HelpersTrait;


    /**
     * Manifest file object containing list of all hashed assets
     *
     * @var Object
     */
    private $manifest;


    /**
     * Manifest file object containing list of all hashed assets
     *
     * @var Object
     */
    private $manifest_path;


    /**
     * Absolut path to theme 'dist' folder
     *
     * @var string
     */
    private $dist_path;


    /**
     * URI to theme 'dist' folder
     *
     * @var string
     */
    private $dist_uri;


    /**
     * Initiate
     */
    public function __construct() {

        $this->dist_uri      = $this->get_plugin_dir_url('/dist');
        $this->dist_path     = $this->get_plugin_dir_path('/dist');
        $this->manifest_path = $this->get_plugin_dir_path('/dist/assets.json');

        /**
         * Test for assets.json
         */
        $this->manifest = file_exists($this->manifest_path)
            ? json_decode( file_get_contents($this->manifest_path), true )
            : array();
    }


    /**
     * Get full URI to single asset
     *
     * @param  string $filename File name
     * @return string           URI to resource
     */
    public function get( string $filename = '' ): string {

        return $this->locate( $filename );
    }



    /**
     * Fix URL for requested files
     *
     * @param  string $filename Requested asset
     * @return [type]           [description]
     */
    private function locate( string $filename = '' ):string {

        // Trim slashes just in case.
        $filename = rtrim($filename, '/');
        $filename = ltrim($filename, '/');

        // Return URL to requested file from manifest.
        if ( array_key_exists( $filename, $this->manifest ) ) {
            return sprintf( '%s/%s', $this->dist_uri, $this->manifest[ $filename ] );
        }

        // Return default file location.
        return sprintf( '%s/%s', $this->dist_uri, $filename );
    }
}
