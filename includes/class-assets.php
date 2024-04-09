<?php

namespace WPTester;

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
        $this->manifest      = array();
        
        if ( file_exists($this->manifest_path) )
        {
            $response = wp_remote_get($this->manifest_path);   

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                error_log("Error retrieving manifest: $error_message");
            }
            else {
                $this->manifest = json_decode(wp_remote_retrieve_body($response), true);
            }
        }
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
