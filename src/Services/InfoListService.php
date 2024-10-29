<?php
/**
 * Info List Service Provider.
 *
 * This file contains the InfoListService class, which is responsible for adding an admin menu
 * and rendering a list of book information in the WordPress admin area. This class integrates
 * with WordPress through the `admin_menu` action hook, allowing administrators to manage and view
 * information related to books.
 *
 * The InfoListService class extends AbstractServiceProvider and implements BootablePluginProviderInterface.
 * It registers the service in the container and provides functions to add and render the admin menu.
 *
 * @package    BooksManager\Services
 * @category   ServiceProvider
 * @since      1.0.0
 */

namespace BooksManager\Services;

defined( 'ABSPATH' ) || exit;

use BooksManager\Admin\InfoListTable;
use BooksManager\BookMangerBoot;
use Rabbit\Contracts\BootablePluginProviderInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class InfoListService.
 *
 * Responsible for adding an admin menu and rendering a list of book information.
 *
 * @package BooksManager\Services
 * @since 1.0.0
 */
class InfoListService extends AbstractServiceProvider implements BootablePluginProviderInterface {
	/**
	 * Services provided by the provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		'info_list_service',
	);

	/**
	 * Register the service in the container.
	 *
	 * @return void
	 */
	public function register() {
		$this->getContainer()->add(
			'info_list_service',
			function () {
				return $this;
			}
		);
	}

	/**
	 * Adds hooks to handle the meta box for the book post type.
	 * Handle adding, saving and deleting the meta box data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function bootPlugin() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'addMenu' ) );
	}

	/**
	 * Add the menu to the admin dashboard.
	 *
	 * @return void
	 */
	public function addMenu() {
		add_menu_page(
			'books info',
			esc_html__( 'books info', 'books-manager' ),
			'manage_options',
			'books-info',
			array( $this, 'render' ),
			'dashicons-book',
			30
		);
	}

	/**
	 * Render the list of book information.
	 *
	 * @return void
	 */
	public function render() {
		$listTable = new InfoListTable();
		$listTable->prepare_items();

		echo BookMangerBoot::view( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'admin/list',
			array(
				'table' => $listTable, // phpcs:ignore
			)
		);
	}
}
