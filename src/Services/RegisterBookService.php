<?php
/**
 * Register Book Service Provider.
 *
 * This file contains the RegisterBookService class, responsible for registering a custom
 * post type for "books" along with "author" and "publisher" taxonomies in WordPress.
 * The class utilizes WordPress actions and hooks to initialize these custom structures,
 * allowing for better organization and categorization of book-related content.
 *
 * The RegisterBookService class extends AbstractServiceProvider and implements
 * BootablePluginProviderInterface, enabling the custom post type and taxonomies to be
 * registered when the plugin initializes.
 *
 * @package    BooksManager\Services
 * @category   ServiceProvider
 * @since      1.0.0
 */

namespace BooksManager\Services;

defined( 'ABSPATH' ) || exit;

use Rabbit\Contracts\BootablePluginProviderInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class RegisterBookService
 * Responsible for registering the custom post type and taxonomies for books.
 *
 * @package BooksManager
 * @since 1.0.0
 */
class RegisterBookService extends AbstractServiceProvider implements BootablePluginProviderInterface {

	/**
	 * Services provided by the provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		'register_book_service',
	);

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	private $postType = 'book';

	/**
	 * Author taxonomy name.
	 *
	 * @var string
	 */
	private $authorTaxonomy = 'author';

	/**
	 * Publisher taxonomy name.
	 *
	 * @var string
	 */
	private $publisherTaxonomy = 'publisher';

	/**
	 * Register the service in the container.
	 *
	 * @return void
	 */
	public function register() {
		$this->getContainer()->add(
			'register_book_service',
			function () {
				return $this;
			}
		);
	}

	/**
	 * Boot the plugin's custom post type and taxonomy registration.
	 * Adds actions to the 'init' hook to register the custom post type and taxonomies.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function bootPlugin() {
		add_action( 'init', array( $this, 'registerTaxonomies' ) );
		add_action( 'init', array( $this, 'registerPostType' ) );
	}

	/**
	 * Get the labels for the post type.
	 *
	 * @return array
	 */
	private function getPostTypeLabels() {
		return array(
			'name'               => esc_html__( 'Books', 'books-manager' ),
			'singular_name'      => esc_html__( 'Book', 'books-manager' ),
			'add_new'            => esc_html__( 'Add New Book', 'books-manager' ),
			'add_new_item'       => esc_html__( 'Add New Book', 'books-manager' ),
			'edit_item'          => esc_html__( 'Edit Book', 'books-manager' ),
			'new_item'           => esc_html__( 'New Book', 'books-manager' ),
			'view_item'          => esc_html__( 'View Book', 'books-manager' ),
			'search_items'       => esc_html__( 'Search Books', 'books-manager' ),
			'not_found'          => esc_html__( 'No books found', 'books-manager' ),
			'not_found_in_trash' => esc_html__( 'No books found in Trash', 'books-manager' ),
			'menu_name'          => esc_html__( 'Books', 'books-manager' ),
		);
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function registerPostType() {
		$args = array(
			'labels'       => $this->getPostTypeLabels(),
			'public'       => true,
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'menu_icon'    => 'dashicons-book',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'books' ),
			'show_in_rest' => true,
			'taxonomies'   => array( $this->publisherTaxonomy, $this->authorTaxonomy ),
		);

		register_post_type( $this->postType, $args );
	}

	/**
	 * Register the taxonomies.
	 *
	 * @return void
	 */
	public function registerTaxonomies() {
		$this->registerAuthorTaxonomy();
		$this->registerPublisherTaxonomy();
	}

	/**
	 * Get the labels for the author taxonomy.
	 *
	 * @return array
	 */
	private function getAuthorLabels() {
		return array(
			'name'              => esc_html__( 'Authors', 'books-manager' ),
			'singular_name'     => esc_html__( 'Author', 'books-manager' ),
			'search_items'      => esc_html__( 'Search Authors', 'books-manager' ),
			'all_items'         => esc_html__( 'All Authors', 'books-manager' ),
			'parent_item'       => esc_html__( 'Parent Author', 'books-manager' ),
			'parent_item_colon' => esc_html__( 'Parent Author:', 'books-manager' ),
			'edit_item'         => esc_html__( 'Edit Author', 'books-manager' ),
			'update_item'       => esc_html__( 'Update Author', 'books-manager' ),
			'add_new_item'      => esc_html__( 'Add New Author', 'books-manager' ),
			'new_item_name'     => esc_html__( 'New Author Name', 'books-manager' ),
			'menu_name'         => esc_html__( 'Authors', 'books-manager' ),
		);
	}

	/**
	 * Register the author taxonomy.
	 *
	 * @return void
	 */
	private function registerAuthorTaxonomy() {
		register_taxonomy(
			$this->authorTaxonomy,
			$this->postType,
			array(
				'labels'       => $this->getAuthorLabels(),
				'rewrite'      => array( 'slug' => 'authors' ),
				'hierarchical' => true,
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Get the labels for the publisher taxonomy.
	 *
	 * @return array
	 */
	private function getPublisherLabels() {
		return array(
			'name'              => esc_html__( 'Publishers', 'books-manager' ),
			'singular_name'     => esc_html__( 'Publisher', 'books-manager' ),
			'search_items'      => esc_html__( 'Search Publishers', 'books-manager' ),
			'all_items'         => esc_html__( 'All Publishers', 'books-manager' ),
			'parent_item'       => esc_html__( 'Parent Publisher', 'books-manager' ),
			'parent_item_colon' => esc_html__( 'Parent Publisher:', 'books-manager' ),
			'edit_item'         => esc_html__( 'Edit Publisher', 'books-manager' ),
			'update_item'       => esc_html__( 'Update Publisher', 'books-manager' ),
			'add_new_item'      => esc_html__( 'Add New Publisher', 'books-manager' ),
			'new_item_name'     => esc_html__( 'New Publisher Name', 'books-manager' ),
			'menu_name'         => esc_html__( 'Publishers', 'books-manager' ),
		);
	}

	/**
	 * Register the publisher taxonomy.
	 *
	 * @return void
	 */
	private function registerPublisherTaxonomy() {
		register_taxonomy(
			$this->publisherTaxonomy,
			$this->postType,
			array(
				'labels'       => $this->getPublisherLabels(),
				'rewrite'      => array( 'slug' => 'publishers' ),
				'hierarchical' => true,
				'show_in_rest' => true,
			)
		);
	}
}
