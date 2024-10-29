<?php

namespace BooksManager\Database;

defined('ABSPATH') || exit;

class DatabaseHandler {
    /**
     * Application instance.
     * 
     * @var Container
     */
    protected static $continer;

    /**
     * Create the book info table.
     * 
     * @since 1.0
     * @return void
     */
    public static function createInfoTable() {
        self::$continer = BooksManagerInit()->getApplication();

        if ( is_null( self::$continer ) ) {
            return;
        }

        if ( ! self::$continer->has('database') ) {
            return;
        }

        $database = self::$continer->get('database');
        $schema   = $database->schema();

        if ( $schema->hasTable('book_info') ) {
            return;
        }

        $schema->create( 'book_info', function ( $table ) {
            $table->bigIncrements('ID');
            $table->bigInteger('post_id')->unique();
            $table->string('isbn', 50);
        } );
    }
}