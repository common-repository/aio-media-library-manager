<?php
namespace Smackcoders\AiomlSmack;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'AiomlSmack_Attachment_Taxonomies_Admin' ) ) {
	return;
}


final class AiomlSmack_Attachment_Taxonomies_Admin {
	
	private static $instance = null;

	

	
	private $plugin_env;

	
	private $core;

	
	public function __construct(  AiomlSmack_media_plugin_Env $plugin_env, AiomlSmack_Attachment_Taxonomies_Core $core ) {
		$this->plugin_env = $plugin_env;
		$this->core       = $core;

		if ( null === self::$instance ) {
			self::$instance = $this;
		}
	}

	
	public function save_ajax_attachment_taxonomies( $attachment_id ) {
		
		if ( ! doing_action( 'wp_ajax_save-attachment' ) ) {
			return;
		}
	
		if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
			return;
		}
	
		$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
	
		if ( ! wp_verify_nonce( $nonce, 'save-attachment_' . $attachment_id ) ) {
			return;
		}
	
		if ( ! isset( $_REQUEST['changes'] ) || ! is_array( $_REQUEST['changes'] ) ) {
			return;
		}
		foreach ( $this->core->get_all_taxonomies() as $taxonomy ) {
		
			if ( ! current_user_can( $taxonomy->cap->assign_terms ) ) {
				continue;
			}
	
			$taxonomy_key = 'taxonomy-' . $taxonomy->name . '-terms';
	
			if ( ! isset( $_REQUEST['changes'][ $taxonomy_key ] ) ) {
				continue;
			}
	//sanitize for terms
			$terms = array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['changes'][ $taxonomy_key ] ) );
	
			if ( is_taxonomy_hierarchical( $taxonomy->name ) ) {
				$terms_array = array_map( 'sanitize_text_field', array_filter( array_map( 'trim', explode( ',', $terms ) ) ) );
			} else {
				$terms_array = array_map( 'sanitize_text_field', (array) $terms );
			}
	
			$valid_terms = array();
			foreach ( $terms_array as $term ) {
				if ( term_exists( $term, $taxonomy->name ) ) {
					$valid_terms[] = $term;
				}
			}
	
			wp_set_post_terms( $attachment_id, $valid_terms, $taxonomy->name );
		}
	}
	
	
	public function render_taxonomy_filters( $post_type ) {
		if ( 'attachment' !== $post_type && 'upload' !== get_current_screen()->base ) {
			return;
		}

		if ( isset( $_REQUEST['attachment-filter'] ) && 'trash' === $_REQUEST['attachment-filter'] ) {
			return;
		}

		foreach ( $this->core->get_taxonomies_to_show() as $taxonomy ) {
			if ( ! $taxonomy->query_var ) {
				continue;
			}

			$value = isset( $_REQUEST[ $taxonomy->query_var ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $taxonomy->query_var ] ) ) : '';

			?>
			<label for="attachment-<?php echo sanitize_html_class( $taxonomy->name ); ?>-filter" class="screen-reader-text"><?php echo esc_html( $this->get_filter_by_label( $taxonomy ) ); ?></label>
			<select class="attachment-filters" name="<?php echo esc_attr( $taxonomy->query_var ); ?>" id="attachment-<?php echo sanitize_html_class( $taxonomy->name ); ?>-filter">
				<option value="" <?php selected( '', $value ); ?>><?php echo esc_html( $taxonomy->labels->all_items ); ?></option>
				<?php foreach ( $this->core->get_terms_for_taxonomy( $taxonomy->name ) as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $term->slug, $value ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php
		}
	}

	public function enqueue_script() {
		$taxonomies = $this->core->get_taxonomies_to_show();
		if ( ! $taxonomies ) {
			return;
		}

		

		$script_metadata['dependencies'][] = 'jquery';
		$script_metadata['dependencies'][] = 'media-views';

	// Enqueue the jQuery UI script from the CDN.
	$script_metadata = array(
		'version' => '1.0', 
		
	);


// Enqueue your custom script.
wp_register_script(
    'aio-media-library-manager',
    $this->plugin_env->url('js/myIndex.js'),
    array('jquery', 'jquery-ui-core'), // Dependencies: jQuery and jQuery UI.
    $script_metadata['version'],
    true // Load the script in the footer.
);

wp_enqueue_script('aio-media-library-manager');
		$inline_script = sprintf(
			'window._AimolSmack_attachmentTaxonomiesExtendMediaLibrary( wp.media, jQuery, %s );',
			wp_json_encode( $this->get_script_data() )
		);
		wp_add_inline_script( 'aio-media-library-manager', $inline_script );
	}


	public function print_styles() {
		$taxonomies = $this->core->get_taxonomies_to_show();
		if ( ! $taxonomies ) {
			return;
		}

		// if ( ! doing_action( 'admin_footer' ) ) {
		// 	add_action( 'admin_footer', array( $this, 'print_styles' ) );
		// 	return;
		// }

		$tax_count = count( $taxonomies );

		$pct           = (int) floor( 100 / ( $tax_count + 1 ) );
		$pct_with_type = (int) floor( 100 / ( $tax_count + 2 ) );

	
	}

	
	public function add_taxonomies_to_attachment_js( $response, $attachment ) {
		$response['taxonomies'] = array();
		foreach ( $this->core->get_taxonomies_to_show() as $taxonomy ) {
			if ( is_taxonomy_hierarchical( $taxonomy->name ) ) {
				$terms = array_map(
					static function ( $term ) {
						return (int) $term->term_id;
					},
					(array) wp_get_object_terms( $attachment->ID, $taxonomy->name )
				);
			} else {
				$terms = array_map(
					static function ( $term ) {
						return $term->slug;
					},
					(array) wp_get_object_terms( $attachment->ID, $taxonomy->name )
				);
			}
			$response[ 'taxonomy-' . $taxonomy->name . '-terms' ] = implode( ',', $terms );
		}
		return $response;
	}

	
	public function remove_taxonomies_from_attachment_compat( $form_fields ) {
		foreach ( $this->core->get_all_taxonomies() as $taxonomy ) {
			if ( isset( $form_fields[ $taxonomy->name ] ) ) {
				unset( $form_fields[ $taxonomy->name ] );
			}
		}

		return $form_fields;
	}

	
	// public function adjust_media_templates() {
	// 	$taxonomies = $this->core->get_taxonomies_to_show();
	// 	if ( ! $taxonomies ) {
	// 		return;
	// 	}

	// 	remove_action( 'admin_footer', 'wp_print_media_templates' );
	// 	remove_action( 'wp_footer', 'wp_print_media_templates' );
	// 	remove_action( 'customize_controls_print_footer_scripts', 'wp_print_media_templates' );
	// 	add_action( 'admin_footer', array( $this, 'print_media_templates' ) );
	// 	add_action( 'wp_footer', array( $this, 'print_media_templates' ) );
	// 	add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_media_templates' ) );
	// }

	
	
	// public function print_media_templates() {
	// 	ob_start();
	// 	wp_print_media_templates();
	// 	$output = ob_get_clean();
	
	// 	// $taxonomy_output = $this->get_taxonomy_media_template_output();
	
	// 	// wp_kses_post to allow common HTML within post content
	// 	$output = preg_replace(
	// 		'#<script type="text/html" id="tmpl-attachment-details">(.+)</script>#Us',
	// 		'<script type="text/html" id="tmpl-attachment-details">$1' . wp_kses_post($taxonomy_output) . '</script>',
	// 		$output
	// 	);
	
	// 	// wp_kses to allow specific HTML tags: 'a', 'div', 'span'
	// 	$output = str_replace(
	// 		'<div class="attachment-compat"></div>',
	// 		wp_kses($taxonomy_output, array(
	// 			'a' => array(
	// 				'href' => array(),
	// 				'title' => array(),
	// 				'class' => array(),
	// 				'id' => array()
	// 			),
	// 			'div' => array(
	// 				'class' => array(),
	// 				'id' => array()
	// 			),
	// 			'span' => array(
	// 				'class' => array(),
	// 				'id' => array()
	// 			),
	// 		)) . "\n" . '<div class="attachment-compat"></div>',
	// 		$output
	// 	);
	
	// 	echo wp_kses_post($output);
	// }
	
	

	
	
	
	private function get_script_data() {
		$taxonomies     = array();
		$all_items      = array();
		$filter_by_item = array();
		foreach ( $this->core->get_taxonomies_to_show() as $taxonomy ) {
			$js_slug = $this->make_js_slug( $taxonomy->name );

			$taxonomies[]               = $this->prepare_taxonomy_for_js( $taxonomy );
			$all_items[ $js_slug ]      = $taxonomy->labels->all_items;
			$filter_by_item[ $js_slug ] = $this->get_filter_by_label( $taxonomy );
		}

		return array(
			'data' => $taxonomies,
			'l10n' => array(
				'all'      => $all_items,
				'filterBy' => $filter_by_item,
			),
		);
	}

	
	private function prepare_taxonomy_for_js( $taxonomy ) {
	
		$js_slug = $this->make_js_slug( $taxonomy->name );

		return array(
			'name'     => $taxonomy->label,
			'slug'     => $js_slug,
			'slugId'   => str_replace( '_', '-', $taxonomy->name ),
			'queryVar' => $taxonomy->query_var,
			'terms'    => array_map(
				function ( $term ) {
					if ( ! $term instanceof WP_Term ) {
						return get_object_vars( $term );
					}
					return $term->to_array();
				},
				$this->core->get_terms_for_taxonomy( $taxonomy->name )
			),
		);
	}

	
	private function make_js_slug( $taxonomy_slug ) {
		return lcfirst( implode( array_map( 'ucfirst', explode( '_', $taxonomy_slug ) ) ) );
	}

	
	private function get_filter_by_label( $taxonomy ) {
		if ( isset( $taxonomy->labels->filter_by_item ) ) {
			return $taxonomy->labels->filter_by_item;
		} elseif ( $taxonomy->hierarchical ) {
			return __( 'Filter by Category', 'aio-media-library-manager' );
		} else {
			return __( 'Filter by Tag', 'aio-media-library-manager' );
		}
	}
}
