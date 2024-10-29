<?php
namespace Smackcoders\AiomlSmack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Taxonomy' ) ) {
	return;
}


abstract class AiomlSmack_Attachment_Taxonomy {
	
	protected $slug = '';

	
	protected $labels = array();

	
	protected $args = array();

	
	public function register() {
		$slug = $this->get_slug();
		$args = $this->get_args();

		register_taxonomy( $slug, 'attachment', $args );
	}

	
	public function unregister() {
		$slug = $this->get_slug();

		if ( function_exists( 'unregister_taxonomy' ) ) {
			unregister_taxonomy( $slug );
			return;
		}

		global $wp_taxonomies;

		$taxonomy_args = get_taxonomy( $this->slug );
		if ( ! $taxonomy_args || $taxonomy_args->_builtin ) {
			return;
		}

		remove_filter( 'wp_ajax_add-' . $this->slug, '_wp_ajax_add_hierarchical_term' );

		unset( $wp_taxonomies[ $this->slug ] );
	}

	
	public function get_slug() {
		return $this->slug;
	}	

	
	public function get_labels() {
		$slug = $this->get_slug();

		$labels = $this->labels;

		
		$labels = apply_filters( "AiomlSmack_attachment_taxonomy_{$slug}_labels", $labels );

		
		return apply_filters( 'AiomlSmack_attachment_taxonomy_labels', $labels, $slug );
	}

	
	public function get_args() {
		$slug = $this->get_slug();

		$args           = $this->args;
		$args['labels'] = $this->get_labels();

		
		$args = apply_filters( "AiomlSmack_attachment_taxonomy_{$slug}_args", $args );

		
		return apply_filters( 'AiomlSmack_attachment_taxonomy_args', $args, $slug );
	}
}
