<?php
/**
 * the template for displaying the freelancer work (bid success a project)
 * # this template is loaded in template/bid-history-list.php
 *
 * @since   1.0
 * @package FreelanceEngine
 */
$author_id = get_query_var( 'author' );
global $wp_query, $ae_post_factory, $post;

$post_object = $ae_post_factory->get( BID );

$current = $post_object->current_post;

if ( ! $current || ! isset( $current->project_title ) ) {
	return;
}
?>
<li>
    <a href="<?php echo $current->project_link; ?>" class="author-project-title"
       title="<?php echo esc_attr( $current->project_title ) ?>"><?php echo $current->project_title ?></a>
</li>