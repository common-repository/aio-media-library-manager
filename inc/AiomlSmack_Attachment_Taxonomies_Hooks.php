<?php

namespace Smackcoders\AiomlSmack;
require_once __DIR__ . '/AiomlSmack_Attachment_Taxonomies_Core.php';
require_once __DIR__ . '/AiomlSmack_Attachment_Taxonomies_Admin.php';
require_once __DIR__ . '/AiomlSmack_Attachment_Taxonomies_REST.php';
require_once __DIR__ . '/AiomlSmack_Attachment_Taxonomy_Shortcode.php';
require_once __DIR__ . '/AiomlSmack_Attachment_Taxonomy_Default_Terms.php';
require_once __DIR__ . '/AiomlSmack_Attachment_Taxonomy_Capabilities.php';

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmackAttachment_Taxonomies_Hooks' ) ) {
	return;
}


final class AiomlSmack_Attachment_Taxonomies_Hooks {

	
	private $plugin_env;

	public function __construct( AiomlSmack_media_plugin_Env $plugin_env ) {
		$this->plugin_env = $plugin_env;
	}

	public function add_all() {
		$core = new AiomlSmack_Attachment_Taxonomies_Core();
		$admin = new AiomlSmack_Attachment_Taxonomies_Admin( $this->plugin_env, $core );
		add_action( 'edit_attachment', array( $admin, 'save_ajax_attachment_taxonomies' ), 10, 1 );
		add_action( 'add_attachment', array( $admin, 'save_ajax_attachment_taxonomies' ), 10, 1 );
		add_action( 'restrict_manage_posts', array( $admin, 'render_taxonomy_filters' ), 10, 1 );
		add_action( 'wp_enqueue_media', array( $admin, 'enqueue_script' ) );
		add_action( 'wp_enqueue_media', array( $admin, 'print_styles' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $admin, 'add_taxonomies_to_attachment_js' ), 10, 2 );
		add_filter( 'attachment_fields_to_edit', array( $admin, 'remove_taxonomies_from_attachment_compat' ), 10, 1 );
		// add_action( 'wp_enqueue_media', array( $admin, 'adjust_media_templates' ) );

		$rest = new AiomlSmack_Attachment_Taxonomies_REST( $core );
		add_filter( 'rest_request_before_callbacks', array( $rest, 'fail_permission_check_if_cannot_assign_attachment_terms' ), 10, 3 );
		add_action( 'rest_after_insert_attachment', array( $rest, 'handle_attachment_terms' ), 10, 2 );

		$capabilities = new AiomlSmack_Attachment_Taxonomy_Capabilities();
		add_filter( 'map_meta_cap', array( $capabilities, 'map_meta_cap' ), 10, 3 );

		$shortcode = new AiomlSmack_Attachment_Taxonomy_Shortcode( $core );
		add_filter( 'shortcode_atts_gallery', array( $shortcode, 'support_gallery_taxonomy_attributes' ), 10, 3 );

		$default_terms = new AiomlSmack_Attachment_Taxonomy_Default_Terms( $core );
		add_action( 'edit_attachment', array( $default_terms, 'ensure_default_attachment_taxonomy_terms' ), 100, 1 );
		add_action( 'add_attachment', array( $default_terms, 'ensure_default_attachment_taxonomy_terms' ), 100, 1 );
		add_action( 'rest_api_init', array( $default_terms, 'register_settings' ), 10, 0 );
		add_action( 'admin_init', array( $default_terms, 'register_settings' ), 10, 0 );
		add_action( 'admin_init', array( $default_terms, 'add_settings_fields' ), 10, 0 );
	}
}
