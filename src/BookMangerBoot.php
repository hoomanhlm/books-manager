<?php
/**
 * Book Manager Boot.
 *
 * This file contains the BookMangerBoot class, responsible for initializing core
 * functionality and providing access to shared instances within the Books Manager
 * plugin. It leverages a singleton pattern for ensuring a single point of access
 * to plugin-wide services, including database and template services.
 *
 * The BookMangerBoot class extends the Singleton base class and provides methods
 * to retrieve instances from a dependency container for interacting with the
 * database and rendering templates.
 *
 * @package    BooksManager
 * @category   Boot
 * @since      1.0.0
 */

namespace BooksManager;

defined( 'ABSPATH' ) || exit;

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
		if ( ! self::$continer->has( 'database' ) ) {
			return;
		}

		return self::$continer->get( 'database' );
	}

	/**
	 * Get the template.
	 *
	 * @param string $file_name Template file name.
	 * @param array  $data      Template data.
	 * @since 1.0.0
	 * @return Template
	 */
	public static function view( $file_name, $data = array() ) {
		if ( ! self::$continer->has( 'template' ) ) {
			return;
		}

		return self::$continer->template( $file_name, $data );
	}
}
