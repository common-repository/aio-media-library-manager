<?php
namespace Smackcoders\AiomlSmack;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Taxonomy_Shortcode' ) ) {
	return;
}


final class AiomlSmack_Attachment_Taxonomy_Shortcode {
	
	private static $instance = null;

	
	

	
	private $core;

	
	public function __construct( AiomlSmack_Attachment_Taxonomies_Core $core ) {
		$this->core = $core;

		if ( null === self::$instance ) {
			self::$instance = $this;
		}
	}

	
	public function support_gallery_taxonomy_attributes( $out, $pairs, $atts ) {
		$taxonomy_slugs = array_map(
			static function ( $taxonomy ) {
				return $taxonomy->name;
			},
			$this->core->get_all_taxonomies()
		);

		$all_term_ids = $this->get_all_term_ids( $taxonomy_slugs, $atts );
		if ( empty( $all_term_ids ) ) {
			return $out;
		}

		$original_ids = array();
		if ( ! empty( $out['include'] ) ) {
			$original_ids = wp_parse_id_list( $out['include'] );
		}

		$limit = ! empty( $atts['limit'] ) ? absint( $atts['limit'] ) : -1;
		if ( -1 !== $limit ) {
			$limit -= count( $original_ids );
			if ( $limit < 1 ) {
				return $out;
			}
		}

		$tax_relation = ( isset( $atts['tax_relation'] ) && 'AND' === strtoupper( $atts['tax_relation'] ) ) ? 'AND' : 'OR';

		$attachment_ids = $this->get_shortcode_attachment_ids( $all_term_ids, $limit, $original_ids, $tax_relation );
		if ( ! empty( $attachment_ids ) ) {
			$out['include'] = array_merge( $original_ids, $attachment_ids );
		}

		return $out;
	}

	
	private function get_all_term_ids( $taxonomy_slugs, $atts ) {
		$all_term_ids = array();

		foreach ( $taxonomy_slugs as $taxonomy_slug ) {
			if ( empty( $atts[ $taxonomy_slug ] ) ) {
				continue;
			}

			$term_ids = $this->get_term_ids_from_attribute( $taxonomy_slug, $atts[ $taxonomy_slug ] );
			if ( empty( $term_ids ) ) {
				continue;
			}

			$all_term_ids[ $taxonomy_slug ] = $term_ids;
		}

		return $all_term_ids;
	}

	
	private function get_term_ids_from_attribute( $taxonomy_slug, $attr ) {
		$query_arg = 'slug';
		$items     = wp_parse_slug_list( $attr );

		if ( empty( $items ) ) {
			return array();
		}

		$ids = array_filter( $items, 'is_numeric' );
		if ( count( $ids ) === count( $items ) ) {
			$query_arg = 'include';
			$items     = array_map( 'absint', $items );
		}

		$query_args               = array(
			'number'                 => 0,
			'fields'                 => 'ids',
			'update_term_meta_cache' => false,
		);
		$query_args[ $query_arg ] = $items;

		$term_ids = $this->core->get_terms_for_taxonomy( $taxonomy_slug, $query_args );
		if ( ! $term_ids || is_wp_error( $term_ids ) ) {
			return array();
		}

		return $term_ids;
	}

	
	// private function get_shortcode_attachment_ids( $all_term_ids, $limit = -1, $exclude_ids = array(), $tax_relation = 'OR' ) {
	// 	$post_ids = array();
	
	// 	foreach ( $all_term_ids as $taxonomy_slug => $term_ids ) {
	// 		$args = array(
	// 			'posts_per_page'         => $limit,
	// 			'fields'                 => 'ids',
	// 			'post_status'            => 'inherit',
	// 			'post_type'              => 'attachment',
	// 			'post_mime_type'         => 'image',
	// 			'tax_query'              => array(
	// 				array(
	// 					'taxonomy'         => $taxonomy_slug,
	// 					'field'            => 'term_id',
	// 					'terms'            => $term_ids,
	// 					'include_children' => false,
	// 					'operator'         => 'IN',
	// 				),
	// 			),
	// 			'update_post_term_cache' => false,
	// 			'update_post_meta_cache' => false,
	// 		);
	
	// 		// Retrieve post IDs based on the query arguments
	// 		$query_results = get_posts( $args );
	
	// 		if ( $query_results ) {
	// 			$post_ids = array_merge( $post_ids, $query_results );
	// 		}
	// 	}
	
	// 	// Remove excluded IDs from the post IDs list
	// 	$post_ids = array_diff( $post_ids, $exclude_ids );
	
	// 	return $post_ids;
	// }
	
	
}
