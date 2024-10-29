<?php

namespace Smackcoders\AiomlSmack;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Taxonomies_REST' ) ) {
	return;
}


final class AiomlSmack_Attachment_Taxonomies_REST {

	
	private $core;

	
	public function __construct( AiomlSmack_Attachment_Taxonomies_Core $core ) {
		
		$this->core = $core;
	}

	
	public function fail_permission_check_if_cannot_assign_attachment_terms( $response, $handler, $request ) {
		if ( ! isset( $handler['permission_callback'] ) || ! is_array( $handler['permission_callback'] ) ) {
			return $response;
		}

		if (
			! $handler['permission_callback'][0] instanceof WP_REST_Attachments_Controller ||
			! in_array( $handler['permission_callback'][1], array( 'create_item_permissions_check', 'update_item_permissions_check' ), true )
		) {
			return $response;
		}

		$assign_terms_check = $this->check_assign_terms_permission( $request );
		if ( is_wp_error( $assign_terms_check ) ) {
			return $assign_terms_check;
		}

		return $response;
	}

	
	public function handle_attachment_terms( $attachment, $request ) {
		$this->handle_terms( $attachment->ID, $request );
	}

	
	private function check_assign_terms_permission( $request ) {
		$taxonomies = wp_list_filter( $this->core->get_all_taxonomies(), array( 'show_in_rest' => true ) );
		foreach ( $taxonomies as $taxonomy ) {
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

			if ( ! isset( $request[ $base ] ) ) {
				continue;
			}

			foreach ( (array) $request[ $base ] as $term_id ) {
				// Invalid terms will be rejected later.
				if ( ! get_term( $term_id, $taxonomy->name ) ) {
					return new WP_Error(
						'rest_invalid_term_id',
						__( 'Invalid term ID.', 'default' ),
						array( 'status' => 400 )
					);
				}

				if ( ! current_user_can( 'assign_term', (int) $term_id ) ) {
					return new WP_Error(
						'rest_cannot_assign_term',
						__( 'Sorry, you are not allowed to assign the provided terms.', 'default' ),
						array( 'status' => rest_authorization_required_code() )
					);
				}
			}
		}

		return true;
	}

	
	private function handle_terms( $attachment_id, $request ) {
		$taxonomies = wp_list_filter( $this->core->get_all_taxonomies(), array( 'show_in_rest' => true ) );

		foreach ( $taxonomies as $taxonomy ) {
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

			if ( ! isset( $request[ $base ] ) ) {
				continue;
			}

			$result = wp_set_object_terms( $attachment_id, (array) $request[ $base ], $taxonomy->name );

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return null;
	}
}
