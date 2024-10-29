<?php
/**
 * Books Info List Template
 *
 * This template renders the books information table in the WordPress admin area
 * for the Books Manager plugin.
 *
 * @package BooksManager
 * @since 1.0.0
 */

?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo esc_html__( 'Books Info', 'books-manager' ); ?></h1>
	<?php echo $table->display(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
