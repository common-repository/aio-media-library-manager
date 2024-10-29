<?php
 namespace Smackcoders\AiomlSmack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Taxonomies_Core' ) ) {
	return;
}


final class AiomlSmack_Attachment_Taxonomies_Core {
	
	private static $instance = null;

	
	

	
	public function __construct() {
		if ( null === self::$instance ) {
			self::$instance = $this;
		}
	}

	
	public function get_all_taxonomies() {
		return get_object_taxonomies( 'attachment', 'objects' );
	}

	
	public function get_taxonomies_to_show() {
		return array_filter(
			$this->get_all_taxonomies(),
			static function ( $taxonomy ) {
				
				return $taxonomy->show_ui && ( $taxonomy->query_var || $taxonomy->show_in_rest );
			}
		);
	}

	
	
	public function get_terms_for_taxonomy( $taxonomy_slug, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'hide_empty' => false,
			)
		);

		$args['taxonomy'] = $taxonomy_slug;
		return get_terms( $args );
	}
}
