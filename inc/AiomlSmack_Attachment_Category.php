<?php
namespace Smackcoders\AiomlSmack;
require_once __DIR__ . '/AiomlSmack_Attachment_Taxonomy.php';
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Category' ) ) {
	return;
}


final class AiomlSmack_Attachment_Category extends AiomlSmack_Attachment_Taxonomy {

	protected $slug = 'attachment_category';

	
	protected $labels = array(); // Empty to use WordPress Core category labels.

	
	protected $args = array(
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => false,//hide categorie menu
		'show_in_nav_menus'     => false,
		'show_tagcloud'         => false,
		'show_admin_column'     => true,
		'hierarchical'          => true,
		'has_default'           => true,
		'update_count_callback' => '_update_generic_term_count',
		'query_var'             => 'attachment_category',
		'rewrite'               => false,
		'capabilities'          => array(
			'manage_terms' => 'manage_attachment_categories',
			'edit_terms'   => 'edit_attachment_categories',
			'delete_terms' => 'delete_attachment_categories',
			'assign_terms' => 'assign_attachment_categories',
		),
		'show_in_rest'          => true,
		'rest_base'             => 'attachment_categories',
	);
}
