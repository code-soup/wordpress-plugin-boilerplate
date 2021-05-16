<?php

// If this file is called directly, abort.
defined('WPINC') || die;

// Autoload all classes via composer.
$autoloader = require "vendor/autoload.php";

// Custom loader function for WPPB plugin files.
spl_autoload_register(function ($class) {

    $namespace = 'WPPB\\';
    $base_dir  = __DIR__ . '/includes';
    $length    = strlen($namespace);

    if (0 !== strncmp($namespace, $class, $length))
    {
        return;
    }

    $rel_class = substr($class, $length);
    $unslashed = str_replace('\\', '/', $rel_class);

    $filename  = strtolower( sprintf('%s/%s.php', $base_dir, $unslashed) );

    // Test for original filename.
    if (file_exists($filename))
    {
        require $filename;
    }

    /**
     * Test for filenames prefixed with "class-"
     * eg: class-{$filename}.php
     */
    $filename = str_replace($unslashed, 'class-', $filename);

    if (file_exists($filename))
    {
        require $filename;
    }
    
});