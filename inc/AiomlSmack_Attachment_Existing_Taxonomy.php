<?php
namespace Smackcoders\AiomlSmack;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Existing_Taxonomy' ) ) {
	return;
}


final class AiomlSmack_Attachment_Existing_Taxonomy extends AiomlSmack_Attachment_Taxonomy {
	
	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	
	public function register() {
		register_taxonomy_for_object_type( $this->slug, 'attachment' );
	}

	
	public function unregister() {
		unregister_taxonomy_for_object_type( $this->slug, 'attachment' );
	}
}
