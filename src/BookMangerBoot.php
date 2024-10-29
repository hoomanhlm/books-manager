<?php

namespace BooksManager;

defined('ABSPATH') || exit;

use Rabbit\Utils\Singleton;

/**
 * Class BookMangerBoot
 * Responsible for booting the functionality of the plugin.
 * 
 * @package BooksManager
 * @since 1.0.0
 */
class BookMangerBoot extends Singleton {
    /**
     * Application instance.
     * 
     * @var Container
     */
    protected static $continer;

    /**
     * BookMangerBoot constructor.
     * 
     * @since 1.0.0
     */
    public function __construct() {
        self::$continer = BooksManagerInit()->getApplication();
    }   

    /**
     * Get the database instance.
     * 
     * @since 1.0.0
     * @return Database
     */
    public static function db() {
        if (!self::$continer->has('database')) {
            return;
        }

        return self::$continer->get('database');
    }

    /**
     * Get the template.
     * 
     * @param string $file_name
     * @param array  $data
     * @since 1.0.0
     * @return Template
     */
    public static function view( $file_name, $data = [] ) {
        if (!self::$continer->has('template')) {
            return;
        }

        return self::$continer->template( $file_name, $data );
    }
}