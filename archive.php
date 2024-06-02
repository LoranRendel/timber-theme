<?php

/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

namespace App;

use Timber\Timber;

$templates = array('templates/archive.twig', 'templates/index.twig');

$title = 'Archive';
if (is_day()) {
	$title = 'Archive: ' . get_the_date('D M Y');
} elseif (is_month()) {
	$title = 'Archive: ' . get_the_date('M Y');
} elseif (is_year()) {
	$title = 'Archive: ' . get_the_date('Y');
} elseif (is_tag()) {
	$title = single_tag_title('', false);
} elseif (is_category()) {
	$title = single_cat_title('', false);
} elseif (is_post_type_archive()) {
	$title = post_type_archive_title('', false);
	array_unshift($templates, 'templates/archive-' . get_post_type() . '.twig');
}

$context = Timber::context([
	'title' => $title,
]);

//region based on template-loader.php
/** @noinspection DuplicatedCode */
$tag_templates  = array(
	'is_embed'             => 'get_embed_template',
	'is_404'               => 'get_404_template',
	'is_search'            => 'get_search_template',
	'is_front_page'        => 'get_front_page_template',
	'is_home'              => 'get_home_template',
	'is_privacy_policy'    => 'get_privacy_policy_template',
	'is_post_type_archive' => 'get_post_type_archive_template',
	'is_tax'               => 'get_taxonomy_template',
	'is_attachment'        => 'get_attachment_template',
	'is_single'            => 'get_single_template',
	'is_page'              => 'get_page_template',
	'is_singular'          => 'get_singular_template',
	'is_category'          => 'get_category_template',
	'is_tag'               => 'get_tag_template',
	'is_author'            => 'get_author_template',
	'is_date'              => 'get_date_template',
	'is_archive'           => 'get_archive_template',
);
$templates_list = [];
// Loop through each of the template conditionals, and find the appropriate template file.
$function = function ( $hook, ...$args ) use ( &$templates_list ) {
	if ( str_ends_with( $hook, '_template' ) ) {
		$templates = $args[2];
		foreach ( $templates as $template ) {
			$templates_list[] = preg_replace( '/\.php$/', '.twig', $template );
		}
	}

	return $hook;
};
add_action( 'all', $function, 100, 4 );
foreach ( $tag_templates as $tag => $template_getter ) {
	if ( call_user_func( $tag ) ) {
		$template = call_user_func( $template_getter );
	}
}
remove_action( 'all', $function, 100 );
$templates = array_values( array_unique( [ ...$templates_list, ...$templates ] ) );
//endregion

Timber::render($templates, $context);
