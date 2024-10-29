<?php
namespace Smackcoders\AiomlSmack;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Taxonomy_Default_Terms' ) ) {
	return;
}


final class AiomlSmack_Attachment_Taxonomy_Default_Terms {
	
	private static $instance = null;


	private $core;

	
	public function __construct( AiomlSmack_Attachment_Taxonomies_Core $core ) {
		$this->core = $core;

		if ( null === self::$instance ) {
			self::$instance = $this;
		}
	}

	public function ensure_default_attachment_taxonomy_terms( $attachment_id ) {
		$attachment = get_post( $attachment_id );
		if ( 'attachment' !== $attachment->post_type || 'auto-draft' === $attachment->post_status ) {
			return;
		}

		foreach ( $this->core->get_taxonomies_to_show() as $taxonomy ) {
			if ( 'category' !== $taxonomy->name && ( ! isset( $taxonomy->has_default ) || ! $taxonomy->has_default ) ) {
				continue;
			}

			$default_term = get_option( 'default_' . $taxonomy->name );
			if ( empty( $default_term ) ) {
				continue;
			}

			$terms = wp_get_post_terms(
				$attachment_id,
				$taxonomy->name,
				array(
					'fields'                 => 'ids',
					'update_term_meta_cache' => false,
				)
			);
			if ( is_wp_error( $terms ) ) {
				continue;
			}

			if ( ! empty( $terms ) ) {
				continue;
			}

			wp_set_post_terms( $attachment_id, array( (int) $default_term ), $taxonomy->name );
		}
	}

	
	public function register_settings() {
		foreach ( $this->core->get_taxonomies_to_show() as $taxonomy ) {
			if ( ! isset( $taxonomy->has_default ) || ! $taxonomy->has_default ) {
				continue;
			}

			if ( ! isset( $taxonomy->name ) || 'category' === $taxonomy->name ) {
				continue;
			}

			$label = $this->get_taxonomy_label_for_setting( $taxonomy );

			register_setting(
				'writing',
				'AiomlSmack_default_' . $taxonomy->name,
				array(
					'type'              => 'integer',
					/* translators: %s: taxonomy label */
					'description'       => sprintf( _x( 'Default %s.', 'REST API description', 'aio-media-library-manager' ), $label ),
					'sanitize_callback' => 'absint',
					'show_in_rest'      => true,
					'default'           => 0,
				)
			);
		}
	}

	
	public function add_settings_fields() {
		foreach ( $this->core->get_taxonomies_to_show() as $taxonomy ) {
			if ( ! isset( $taxonomy->has_default ) || ! $taxonomy->has_default ) {
				continue;
			}

			if ( ! isset( $taxonomy->name ) || 'category' === $taxonomy->name ) {
				continue;
			}

			$label = $this->get_taxonomy_label_for_setting( $taxonomy );

			/* translators: %s: taxonomy label */
			$title = sprintf( _x( 'Default %s', 'settings field title', 'aio-media-library-manager' ), $label );

			add_settings_field(
				'AiomlSmack_default_' . $taxonomy->name,
				$title,
				array( $this, 'render_settings_field' ),
				'writing',
				'default',
				array(
					'label_for' => 'AiomlSmack_default_' . $taxonomy->name,
					'taxonomy'  => $taxonomy,
				)
			);
		}
	}

	
	public function render_settings_field( $args ) {
		$taxonomy = $args['taxonomy'];

		wp_dropdown_categories(
			array(
				'id'                => ! empty( $args['label_for'] ) ? $args['label_for'] : 'AiomlSmack_default_' . $taxonomy->name,
				'name'              => 'default_' . $taxonomy->name,
				'value_field'       => 'term_id',
				'selected'          => get_option( 'AiomlSmack_default_' . $taxonomy->name ),
				'taxonomy'          => $taxonomy->name,
				'hierarchical'      => $taxonomy->hierarchical,
				'hide_empty'        => false,
				'orderby'           => 'name',
				'order'             => 'ASC',
				'show_option_none'  => _x( 'None', 'default term dropdown', 'aio-media-library-manager' ),
				'option_none_value' => 0,
			)
		);
	}

	
	private function get_taxonomy_label_for_setting( $taxonomy ) {
		// if ( 'AiomlSmack_Attachment_Category' === $taxonomy->name ) {
		// 	return __( 'Attachment Category', 'aio-media-library-manager' );
		// }

		

		return $taxonomy->labels->singular_name;
	}
}
