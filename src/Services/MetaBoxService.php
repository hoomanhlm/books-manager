<?php
/**
 * Meta Box Service Provider.
 *
 * This file contains the MetaBoxService class, responsible for adding a meta box
 * to the "book" post type in the WordPress admin area. The class registers, manages,
 * and displays the meta box for capturing book-specific data (e.g., ISBN).
 *
 * The MetaBoxService class extends AbstractServiceProvider and implements the
 * BootablePluginProviderInterface, enabling it to hook into WordPress actions.
 * It provides functionality to render, save, and delete meta box data, making it
 * a core part of the BooksManager plugin's admin features.
 *
 * @package    BooksManager\Services
 * @category   ServiceProvider
 * @since      1.0.0
 */

namespace BooksManager\Services;

defined( 'ABSPATH' ) || exit;

use BooksManager\BookMangerBoot;
use Rabbit\Contracts\BootablePluginProviderInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class MetaBoxService.
 *
 * Responsible for adding meta box to the book post type.
 *
 * @package BooksManager\Providers
 * @since 1.0.0
 */
class MetaBoxService extends AbstractServiceProvider implements BootablePluginProviderInterface {

	/**
	 * Services provided by the provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		'meta_box_service',
	);

	/**
	 * Meta key.
	 *
	 * @var string
	 */
	private $metaKey = 'isbn-number';

	/**
	 * Register the service in the container.
	 *
	 * @return void
	 */
	public function register() {
		$this->getContainer()->add(
			'meta_box_service',
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

		add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
		add_action( 'save_post', array( $this, 'saveMetaBox' ) );
		add_action( 'before_delete_post', array( $this, 'deleteMetaBoxData' ) );
	}

	/**
	 * Add meta box to the book post type.
	 *
	 * @param string $postType The post type.
	 * @since 1.0.0
	 * @return void
	 */
	public function addMetaBox( $postType ) {
		if ( 'book' !== $postType ) {
			return;
		}

		add_meta_box(
			$this->metaKey,
			esc_html__( 'ISBN Number', 'books-manager' ),
			array( $this, 'render' ),
			'book',
			'normal',
			'default'
		);
	}

	/**
	 * Render the meta box.
	 *
	 * @param WP_Post $post The post object.
	 * @since 1.0.0
	 * @return void
	 */
	public function render( $post ) {
		$bookInfo = $this->getBookInfoTable();

		if ( is_null( $bookInfo ) ) {
			return;
		}

		wp_nonce_field( 'save_isbn_meta_box', 'isbn_meta_box_nonce' );

		$data = $bookInfo->where( 'post_id', $post->ID )->first();
		$isbn = ! empty( $data->isbn ) ? $data->isbn : '';

		echo BookMangerBoot::view( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'admin/isbn',
			array(
				'isbn'    => esc_attr( $isbn ),
				'metaKey' => esc_attr( $this->metaKey ),
			)
		);
	}

	/**
	 * Save the meta box data.
	 *
	 * @param int $postId The post ID.
	 * @since 1.0.0
	 * @return void
	 */
	public function saveMetaBox( $postId ) {
		if (
			! isset( $_POST['isbn_meta_box_nonce'] ) ||
			! wp_verify_nonce( wp_unslash( $_POST['isbn_meta_box_nonce'] ), 'save_isbn_meta_box' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $postId ) ) {
			return;
		}

		$isbn = isset( $_POST[ $this->metaKey ] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->metaKey ] ) ) : '';

		if ( empty( $isbn ) ) {
			return;
		}

		$bookInfo = $this->getBookInfoTable();

		$bookInfo->updateOrInsert(
			array( 'post_id' => $postId ),
			array(
				'post_id' => $postId,
				'isbn'    => $isbn,
			)
		);
	}


	/**
	 * Delete the meta box data.
	 *
	 * @param int $postId The post ID.
	 * @since 1.0.0
	 * @return void
	 */
	public function deleteMetaBoxData( $postId ) {
		if ( get_post_type( $postId ) !== 'book' ) {
			return;
		}

		$bookInfo = $this->getBookInfoTable();
		$bookInfo->where( 'post_id', $postId )->delete();
	}

	/**
	 * Get the book info table.
	 *
	 * @since 1.0.0
	 * @return Database
	 */
	private function getBookInfoTable() {
		$db = BookMangerBoot::db();

		return $db->table( 'book_info' );
	}
}
