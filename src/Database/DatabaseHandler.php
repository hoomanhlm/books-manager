<?php
/**
 * Database Handler for Books Manager.
 *
 * This file contains the DatabaseHandler class, responsible for managing
 * database interactions for the Books Manager plugin. Specifically, it
 * initializes and creates the `book_info` table in the database, which
 * stores additional information about books, such as ISBN and post ID.
 *
 * The DatabaseHandler class accesses the application's database service
 * to execute schema operations, ensuring that required tables are created
 * upon plugin initialization.
 *
 * @package    BooksManager\Database
 * @subpackage DatabaseHandler
 * @since      1.0.0
 */

namespace BooksManager\Database;

defined( 'ABSPATH' ) || exit;

/**
 * Class DatabaseHandler.
 *
 * Responsible for managing database interactions for the Books Manager plugin.
 *
 * @package BooksManager\Database
 * @since 1.0.0
 */
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

		if ( ! self::$continer->has( 'database' ) ) {
			return;
		}

		$database = self::$continer->get( 'database' );
		$schema   = $database->schema();

		if ( $schema->hasTable( 'book_info' ) ) {
			return;
		}

		$schema->create(
			'book_info',
			function ( $table ) {
				$table->bigIncrements( 'ID' );
				$table->bigInteger( 'post_id' )->unique();
				$table->string( 'isbn', 50 );
			}
		);
	}
}
