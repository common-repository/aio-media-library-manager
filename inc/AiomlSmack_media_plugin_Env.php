<?php
 namespace Smackcoders\AiomlSmack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_media_plugin_Env' ) ) {
	return;
}


final class AiomlSmack_media_plugin_Env {

	
	private $main_file;

		private $is_mu_plugin = false;


	private $base_path_relative = '';

	
	public function __construct( string $main_file ) {
		$this->main_file = $main_file;

		$mu_plugin_dir = wp_normalize_path( WPMU_PLUGIN_DIR );
		if ( preg_match( '#^' . preg_quote( $mu_plugin_dir, '#' ) . '/#', wp_normalize_path( $main_file ) ) ) {
			$this->is_mu_plugin = true;

			// Is the plugin main file one level above the actual plugin's directory?
			// if ( file_exists( dirname( $this->main_file ) . '/aio-media-library-manager/inc/aio_media_plugin_Env.php' ) ) {
			// 	$this->base_path_relative = 'aio-media-library-manager/';
			// }
		}
	}

	public function main_file(): string {
		return $this->main_file;
	}

	public function is_mu_plugin(): bool {
		return $this->is_mu_plugin;
	}

	
	public function basename(): string {
		return plugin_basename( $this->main_file );
	}

	
	public function path( string $relative_path = '/' ): string {
		return plugin_dir_path( $this->main_file ) . $this->base_path_relative . ltrim( $relative_path, '/' );
	}

	
	public function url( string $relative_path = '/' ): string {
		return plugin_dir_url( $this->main_file ) . $this->base_path_relative . ltrim( $relative_path, '/' );
	}
}
