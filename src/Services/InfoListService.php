<?php

namespace BooksManager\Services;

defined('ABSPATH') || exit;

use BooksManager\Admin\InfoListTable;
use BooksManager\BookMangerBoot;
use Rabbit\Contracts\BootablePluginProviderInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

class InfoListService extends AbstractServiceProvider implements BootablePluginProviderInterface {
    /**
	 * Services provided by the provider.
	 *
	 * @var array
	 */
	protected $provides = [
        'info_list_service',
    ];

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

        add_action('admin_menu', [$this, 'addMenu']);
    }

    /**
     * Add the menu to the admin dashboard.
     * 
     * @return void
     */
    public function addMenu() {
        add_menu_page(
            "books info",
            esc_html__('books info', 'books-manager'),
            "manage_options",
            "books-info",
            [$this, 'render'],
            "dashicons-book",
            30
        );
    }

    public function render() {
        $listTable = new InfoListTable();
		$listTable->prepare_items();

        echo BookMangerBoot::view(
            'admin/list',
            [
                'table' => $listTable,
            ]
        );
    }
}