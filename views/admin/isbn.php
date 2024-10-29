<?php
/**
 * ISBN Meta Box Template
 *
 * This template renders the ISBN input field and label within the meta box for the Books Manager plugin.
 *
 * @package BooksManager
 * @since 1.0.0
 */

?>

<label><?php echo esc_html__( 'ISBN Number:', 'books-manager' ); ?></label>
<input type="text" name="<?php echo esc_attr( $metaKey ); ?>" value="<?php echo esc_attr( $isbn ); ?>">
