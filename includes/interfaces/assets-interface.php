<?php

namespace WPPB\Interfaces;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * Interface for the Assets class.
 *
 * Defines the methods that must be implemented by any class that handles
 * plugin assets (JS, CSS, images, etc.).
 *
 * @since 1.0.0
 */
interface AssetsInterface {
    /**
     * Get full URI to single asset
     *
     * @since 1.0.0
     * @param string $filename File name
     * @return string URI to resource
     */
    public function get(string $filename = ''): string;
} 