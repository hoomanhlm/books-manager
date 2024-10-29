<?php
/**
 * Information List Table.
 *
 * This file contains the InfoListTable class, which extends the WP_List_Table class
 * to create a custom table for displaying book information in the WordPress admin area.
 * The class retrieves book data from the database and formats it for display, providing
 * pagination and custom columns for managing large datasets.
 *
 * The InfoListTable class is a key component of the Books Manager plugin, allowing
 * administrators to view and manage book-related information with ease.
 *
 * @package    BooksManager\Admin
 * @subpackage WP_List_Table
 * @since      1.0.0
 */

namespace BooksManager\Admin;

use BooksManager\BookMangerBoot;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class InfoListTable
 *
 * Responsible for rendering the list of book information.
 *
 * @package BooksManager\Admin
 * @since 1.0
 */
class InfoListTable extends \WP_List_Table {
	/**
	 * InfoListTable constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => esc_html__( 'Book', 'books-manager' ),
				'plural'   => esc_html__( 'Books', 'books-manager' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepare the items for the table to process.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = $this->get_total_books_count();

		$this->items           = $this->get_book_info( $per_page, $current_page );
		$this->_column_headers = array( $this->get_columns(), array(), array() );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Get the books from the database.
	 *
	 * @param int $per_page    Number of items per page.
	 * @param int $page_number Current page number.
	 *
	 * @return array
	 */
	public function get_book_info( $per_page = 10, $page_number = 1 ) {
		$offset = ( $page_number - 1 ) * $per_page;

		$rows = BookMangerBoot::db()
			->table( 'book_info' )
			->limit( $per_page )
			->offset( $offset )
			->get()
			->all();

		if ( is_null( $rows ) ) {
			return array();
		}

		$output = array();
		foreach ( $rows as $row ) {
			$output[] = array(
				'ID'      => $row->ID,
				'post_id' => $row->post_id,
				'isbn'    => $row->isbn,
			);
		}

		return $output;
	}

	/**
	 * Get the total books count.
	 *
	 * @return int
	 */
	public function get_total_books_count() {
		return BookMangerBoot::db()->table( 'book_info' )->count();
	}

	/**
	 * Handles the data of the columns.
	 *
	 * @param array  $item        The data item for the current row.
	 * @param string $column_name The name of the column.
	 *
	 * @return string
	 */
	protected function column_default( $item, $column_name ) {
		if ( isset( $item[ $column_name ] ) ) {
			return esc_html( $item[ $column_name ] );
		}

		return '';
	}

	/**
	 * Define the columns for the list table.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'ID'      => esc_html__( 'id', 'books-manager' ),
			'post_id' => esc_html__( 'post id', 'books-manager' ),
			'isbn'    => esc_html__( 'isbn number', 'books-manager' ),
		);
	}
}
