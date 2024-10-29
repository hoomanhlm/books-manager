<?php

namespace BooksManager\Services;

defined('ABSPATH') || exit;

use BooksManager\BookMangerBoot;
use Rabbit\Contracts\BootablePluginProviderInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class MetaBoxService
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
	protected $provides = [
        'meta_box_service',
    ];

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

        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post', [$this, 'saveMetaBox']);
        add_action('before_delete_post', [$this, 'deleteMetaBoxData']);
    }

    /**
     * Add meta box to the book post type.
     * 
     * @since 1.0.0
     * @return void
     */
    public function addMetaBox( $postType ) {
        if ( 'book' !== $postType ) {
            return;
        }

        add_meta_box(
            $this->metaKey,
            esc_html__('ISBN Number', 'books-manager'),
            [$this, 'render'],
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

        if (is_null($bookInfo)) {
            return;
        }

        $data = $bookInfo->where('post_id', $post->ID)->first();
        $isbn = ! empty( $data->isbn ) ? $data->isbn : '';

        echo BookMangerBoot::view(
            'admin/isbn',
            [
                'isbn' => $isbn,
                'metaKey' => $this->metaKey
            ]
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Save the meta box data.
     * 
     * @param int $postId The post ID.
     * @since 1.0.0
     * @return void
     */
    public function saveMetaBox( $postId ) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $postId)) {
            return;
        };

        $isbn = sanitize_text_field($_POST[$this->metaKey] ?? '');
       
        if ( empty( $isbn ) ) {
            return;
        }

        $bookInfo = $this->getBookInfoTable();

        $bookInfo->updateOrInsert(
            ['post_id' => $postId],
            ['post_id' => $postId, 'isbn' => $isbn]
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
        $bookInfo->where('post_id', $postId)->delete();
    }

    /**
     * Get the book info table.
     * 
     * @since 1.0.0
     * @return Database
     */
    private function getBookInfoTable() {
        $db = BookMangerBoot::db();

        return $db->table('book_info');
    }
}