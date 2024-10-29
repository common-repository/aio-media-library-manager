<?php
namespace Smackcoders\AiomlSmack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Taxonomy_Capabilities' ) ) {
	return;
}


final class AiomlSmack_Attachment_Taxonomy_Capabilities {
	
	private static $instance = null;



	
	public function __construct() {
		if ( null === self::$instance ) {
			self::$instance = $this;
		}
	}

	
	public function map_meta_cap( $caps, $cap, $user_id ) {
		switch ( $cap ) {
			case 'manage_attachment_categories':
			case 'edit_attachment_categories':
			case 'delete_attachment_categories':
				return $this->get_manage_base_caps();
			case 'assign_attachment_categories':
				return $this->get_assign_base_caps( $user_id );
		}

		return $caps;
	}

	
	private function get_manage_base_caps() {
		return array( 'upload_files', 'manage_categories' );
	}

	
	private function get_assign_base_caps( $user_id ) {
		$post_type = get_post_type_object( 'attachment' );
		if ( ! $post_type ) {
			// This should never happen.
			return array( 'do_not_allow' );
		}

		$caps   = map_meta_cap( $post_type->cap->edit_posts, $user_id );
		$caps[] = 'upload_files';
		return $caps;
	}
}
