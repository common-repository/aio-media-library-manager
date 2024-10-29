<?php
/**
 * AIO Media Library Manager.
 *
 * AIO Media Library Manager plugin file.
 *
 * @package   Smackcoders\AIOMLM
 * @copyright Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: AIO Media Library Manager
 * Version:     1.0.0
 * Plugin URI:  https://www.smackcoders.com
 * Description: Organize your media mess! Use Folders, Drag & Drop for WordPress. Download AIO Media Library Manager.
 * Author:      Smackcoders
 * Author URI:  https://www.smackcoders.com/wordpress.html
 * Text Domain: aio-media-library-manager
 * Domain Path: /languages
 * License:     GPLv2
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see https://www.gnu.org/licenses/.
 */

 namespace Smackcoders\AiomlSmack;
 require_once __DIR__ . '/inc/AiomlSmack_Attachment_Taxonomies_Hooks.php';
 require_once __DIR__ . '/inc/AiomlSmack_Attachment_Category.php';
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
define( 'AiomlSmack_LICENSE', true );
global $AiomlSmack_options;

// update_option( 'AiomlSmack_settings', $settings );

//new media upload firing and updating for uncategorized
add_action( 'add_attachment', 'Smackcoders\AiomlSmack\AiomlSmack_new_media' );

function AiomlSmack_new_media( $attachment_id ){
    
    $folder_name = "UnCategorized";

    // Check if the 'UnCategorized' folder already exists
    $term = get_term_by('slug', $folder_name, 'attachment_category');

    if ($term && !is_wp_error($term)) {
        // If the term exists, assign the attachment to that folder
        $term_id = $term->term_id;
        
        // Update the post parent.
        $updated = wp_update_post(array(
            'ID'          => $attachment_id,
            'post_parent' => $term_id,
        ));

        // Set the term relationship
        $result = wp_set_object_terms($attachment_id, [$term_id], 'attachment_category');
        
        if (is_wp_error($updated) || is_wp_error($result)) {
            // Handle errors if updating post parent or setting terms fails
            error_log('Error assigning attachment to folder: ' . $updated->get_error_message() . ' | ' . $result->get_error_message());
        } else {
            // Success message
            error_log('Attachment assigned to existing folder successfully.');
        }
    } else {
        // 'UnCategorized' folder doesn't exist, create it
        $term = wp_insert_term($folder_name, 'attachment_category', array('slug' => $folder_name));

        if (is_wp_error($term)) {
            // Handle error if term insertion fails
            error_log('Error creating folder: ' . $term->get_error_message());
        } else {
            // Retrieve the newly inserted term_id
            $term_id = $term['term_id'];
            
            // Update the post parent.
            $updated = wp_update_post(array(
                'ID'          => $attachment_id,
                'post_parent' => $term_id,
            ));

            // Set the term relationship
            $result = wp_set_object_terms($attachment_id, [$term_id], 'attachment_category');

            if (is_wp_error($updated) || is_wp_error($result)) {
                // Handle errors if updating post parent or setting terms fails
                error_log('Error assigning attachment to folder: ' . $updated->get_error_message() . ' | ' . $result->get_error_message());
            } else {
                // Success message
                error_log('Attachment assigned to new folder successfully.');
            }
        }
    }
}


final class AiomlSmack_Attachment_Taxonomies {
	
	private static $instance = null;

	
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	
	private $plugin_env;

	
	private $taxonomies = array();

	
	private function __construct() {
		
		if ( file_exists( __DIR__ . '/inc/AiomlSmack_media_plugin_Env.php' ) ) {
			require_once __DIR__ . '/inc/AiomlSmack_media_plugin_Env.php';
            
		} else {
			require_once __DIR__ . '/aio-media-library-manager/inc/AiomlSmack_media_plugin_Env.php';
		}

		$this->plugin_env = new AiomlSmack_media_plugin_Env( __FILE__ );

		$inc_dir = $this->plugin_env->path( 'inc/' );
		spl_autoload_register(
			static function ( $class_name ) use ( $inc_dir ) {
				if (
					str_starts_with( $class_name, 'AiomlSmack_Attachment_Taxonomies' ) ||
					str_starts_with( $class_name, 'AiomlSmack_Attachment_Taxonomy' ) ||
					in_array( $class_name, array( 'AiomlSmack_Attachment_Existing_Taxonomy', 'Smackcoders\AiomlSmack\AiomlSmack_Attachment_Category'), true )
				) {
					require_once "{$inc_dir}{$class_name}.php";
				}
			},
			true,
			true
		);

		$bootstrap = function () {
			$this->add_hooks();
			$this->add_default_taxonomies();
		};

		if ( $this->plugin_env->is_mu_plugin() ) {
			add_action( 'muplugins_loaded', $bootstrap, 1 );
		} else {
			add_action( 'plugins_loaded', $bootstrap, 1 );
		}
	}

	
	public function add_taxonomy(  AiomlSmack_Attachment_Taxonomy $taxonomy ) {
		$taxonomy_slug = $taxonomy->get_slug();

		if ( isset( $this->taxonomies[ $taxonomy_slug ] ) ) {
			return false;
		}

		$this->taxonomies[ $taxonomy_slug ] = $taxonomy;
		if ( doing_action( 'init' ) || did_action( 'init' ) ) {
			$this->taxonomies[ $taxonomy_slug ]->register();
		} else {
			add_action( 'init', array( $this->taxonomies[ $taxonomy_slug ], 'register' ) );
		}

		return true;
	}

	
	public function get_taxonomy( $taxonomy_slug ) {
		if ( ! isset( $this->taxonomies[ $taxonomy_slug ] ) ) {
			return null;
		}

		return $this->taxonomies[ $taxonomy_slug ];
	}

	
	public function remove_taxonomy( $taxonomy_slug ) {
		if ( ! isset( $this->taxonomies[ $taxonomy_slug ] ) ) {
			return false;
		}

		if ( doing_action( 'init' ) || did_action( 'init' ) ) {
			$this->taxonomies[ $taxonomy_slug ]->unregister();
		} else {
			remove_action( 'init', array( $this->taxonomies[ $taxonomy_slug ], 'register' ) );
		}
		unset( $this->taxonomies[ $taxonomy_slug ] );

		return true;
	}

	
	public function get_path( $rel_path ) {
		return $this->plugin_env->path( $rel_path );
	}

	
	public function get_url( $rel_path ) {
		return $this->plugin_env->url( $rel_path );
	}

	
	

	
	private function add_hooks() {
		$hooks = new AiomlSmack_Attachment_Taxonomies_Hooks( $this->plugin_env );
		$hooks->add_all();
	}

	
	private function add_default_taxonomies() {
	
		$taxonomy_class_names = apply_filters( 'AiomlSmack_attachment_taxonomy_class_names', array( 'Smackcoders\AiomlSmack\AiomlSmack_Attachment_Category' ) );

		$taxonomy_class_names = array_filter( array_unique( $taxonomy_class_names ), 'class_exists' );

		foreach ( $taxonomy_class_names as $class_name ) {
			$this->add_taxonomy( new $class_name() );
		}
	}
}

AiomlSmack_Attachment_Taxonomies::instance();

function AiomlSmack_custom_media_buttons() {
    $screen = get_current_screen();
    if ($screen && isset($screen->id) && $screen->id === 'upload') {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_style('jstree-style', plugin_dir_url(__FILE__). 'inc/assets/jstreeStyle.min.css', array(), '3.3.11');
        wp_enqueue_script('jstree', plugin_dir_url(__FILE__). 'inc/assets/jstree.min.js', array('jquery'), '3.3.11', true);
        wp_enqueue_script('bootstrap',plugin_dir_url(__FILE__). 'inc/assets/bootstrap.min.js', array('jquery'), '4.5.2', true);
        wp_enqueue_style('bootstrap', plugin_dir_url(__FILE__). 'inc/assets/bootstrap.min.css', array(), '4.5.2');
        wp_enqueue_style('style', plugin_dir_url(__FILE__) . 'inc/style.css', array(), '1.0'); 
        wp_enqueue_script('custom-media-script', plugin_dir_url(__FILE__) . 'src/custom-media-script.js', array('jquery'), '1.0', true);
        wp_localize_script('custom-media-script', 'folder_script', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('folder_nonce')
        ));
        wp_enqueue_style('toastr-css', plugin_dir_url(__FILE__) . 'inc/assets/toastify.css', array(), '2.1.4'); 
        wp_enqueue_script('toastr-js',plugin_dir_url(__FILE__) . 'inc/assets/toastify.js', array('jquery'), '2.1.4', true); 
    }
}
add_action('admin_enqueue_scripts', 'Smackcoders\AiomlSmack\AiomlSmack_custom_media_buttons');



add_action('wp_ajax_AiomlSmack_save_folder_to_database', 'Smackcoders\AiomlSmack\AiomlSmack_save_folder_to_database_callback');
        function AiomlSmack_save_folder_to_database_callback() {
            // Nonce verification
            if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'folder_nonce' ) )  {
            wp_send_json_error('Nonce verification failed.');
            }
        
            if (isset($_POST['folder_name']) && isset($_POST['parent_id'])) {
            
            $folderName = sanitize_text_field($_POST['folder_name']);
            $parentId = intval($_POST['parent_id']); // Cast parent_id to integer for safety
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $folderName)) {
                wp_send_json_error('Folder name cannot contain special characters.');
            }
            $folderSlug = sanitize_title($folderName);
            
            // Check if the term is already cached
            $term = wp_cache_get('folder_term_' . $folderSlug);
            
            // If not cached, fetch it from the database and cache it
            if ($term === false) {
                $term = wp_insert_term($folderName, 'attachment_category', array('slug' => $folderSlug));
                
                // Check for errors during term insertion
                if (is_wp_error($term)) {
                    $error_message = $term->get_error_message();
                    error_log('Error saving folder to database: ' . $error_message);
                    echo 'Error saving folder to database: ' . esc_html($error_message);
                    wp_die();
                }
                
                // Cache the term
                wp_cache_set('folder_term_' . $folderSlug, $term);
            }
            
            // Retrieve the newly inserted term_id
            $term_id = $term['term_id'];
            
            // Save the folder data to the smack_Media table
            global $wpdb;
            $table_name = $wpdb->prefix . 'smack_Media';
          
            $data = array(
                'term_id' => $term_id,
                'name' => $folderName,
                'slug' => $folderSlug,
                'parent' => $parentId
            );
            
            $format = array('%d', '%s', '%s', '%d');
            
            $result = $wpdb->insert($table_name, $data, $format);
            
            if ($result === false) {
                echo 'Error saving folder to database.';
            } else {
                echo 'Folder saved to database: ' . esc_html($folderName);
            }
            } else {
            echo 'Invalid data provided.';
            }
            wp_die();
        }

function AiomlSmack_create_folder_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'smack_Media'; 

    // Set the charset and collation for the table
    $charset_collate = $wpdb->get_charset_collate();

    // SQL statement to create the table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT, 
        `term_id` int(11) NOT NULL,
        `name` varchar(250) NOT NULL,
        `slug` varchar(250) NOT NULL,
        `parent` int(11) NOT NULL DEFAULT 0,
        `created_by` int(11) NULL DEFAULT 0,
        PRIMARY KEY (`id`), 
        UNIQUE KEY `id` (`id`) 
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Register the function to run when the plugin is activated
register_activation_hook(__FILE__, 'Smackcoders\AiomlSmack\AiomlSmack_create_folder_table');



//fetch
add_action('wp_ajax_AiomlSmack_fetch_folders_from_database', 'Smackcoders\AiomlSmack\AiomlSmack_fetch_folders_from_database_callback');

function AiomlSmack_fetch_folders_from_database_callback() {
    global $wpdb;

    // Define cache key
    $cache_key = 'folders_from_database';

    // Attempt to retrieve data from cache
    $folders = wp_cache_get($cache_key);    

    // If data is not found in cache, perform database query
    if (false === $folders) {
    // Fetch terms using WordPress function
    $terms = get_terms(array(
        'taxonomy' => 'attachment_category',
        'hide_empty' => false, // Include empty terms
    ));

    // Initialize empty array to store folder data
    $folders = array();

    // Loop through terms and extract required data
    foreach ($terms as $term) {
        // Fetch additional data from custom table smack_Media based on term_id
        $media_data = $wpdb->get_row(
            $wpdb->prepare("
                SELECT 
                    sm.name, 
                    sm.term_id,     
                    sm.slug, 
                    sm.parent, 
                    tt.count AS term_count
                FROM {$wpdb->prefix}smack_Media AS sm
                LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON sm.term_id = tt.term_id
                WHERE sm.term_id = %d AND tt.taxonomy = %s
                GROUP BY sm.term_id
            ", $term->term_id, 'attachment_category'),
            ARRAY_A
        );
        

        // Add retrieved data to folders array
        if ($media_data) {
            $folders[] = array(
                'name' => $media_data['name'],
                'term_id' => $media_data['term_id'],
                'slug' => $media_data['slug'],
                'parent' => $media_data['parent'],
                'term_count' => $media_data['term_count'],
            );
        }
    }

    // Cache the results
    wp_cache_set($cache_key, $folders);
    }

    // Sanitize folder names and slugs
    foreach ($folders as &$folder) {
    $folder['name'] = esc_html($folder['name']);
    $folder['slug'] = esc_html($folder['slug']);
    }
    unset($folder);

    // Output JSON-encoded data
    echo wp_json_encode($folders);


    $folder_name = "UnCategorized";
    $term = get_term_by('slug', $folder_name, 'attachment_category');

    if (!$term) {
        // Term does not exist, so create it
        $result = wp_insert_term($folder_name, 'attachment_category', array('slug' => $folder_name));
        if (is_wp_error($result)) {
            // Handle the error, if any
            error_log('Error creating term: ' . $result->get_error_message());
        }
    }
    wp_die();
}




// Delete folder from the database
add_action('wp_ajax_AiomlSmack_delete_folder_from_database', 'Smackcoders\AiomlSmack\AiomlSmack_delete_folder_from_database_callback');

function AiomlSmack_delete_folder_from_database_callback() {
    if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'folder_nonce' ) ) {
        wp_send_json_error('Nonce verification failed.');
    }

    global $wpdb;

    if (isset($_POST['folder_name'])) {
        $folderName = sanitize_text_field($_POST['folder_name']);

        // Retrieve the term by name
        $term = get_term_by('name', $folderName, 'attachment_category');

        if ($term !== null && !is_wp_error($term)) {
            $term_id = $term->term_id;

            // Move attachments to uncategorized
            AiomlSmack_move_attachments_to_uncategorized($folderName);

            // Update the parent column in wp_smack_Media table
            $media_parent_update_result = AiomlSmackupdate_media_parent_column($term_id);

            if ($media_parent_update_result === false) {
                // Handle error if update fails
                $error_message = $wpdb->last_error;
                error_log('Error updating parent column in wp_smack_Media table: ' . $error_message);
                wp_send_json_error('Error updating parent column in wp_smack_Media table: ' . esc_html($error_message));
            }

            // Delete the term
            $delete_term_result = wp_delete_term($term_id, 'attachment_category');

            if (is_wp_error($delete_term_result)) {
                $error_message = $delete_term_result->get_error_message();
                error_log('Error deleting folder from database: ' . $error_message);
                wp_send_json_error('Error deleting folder from database: ' . esc_html($error_message));
            } else {
                // Delete the corresponding record from the custom wp_smack_Media table
                $delete_media_result = AiomlSmack_delete_media_by_term_id($term_id);

                if ($delete_media_result === false) {
                    $error_message = $wpdb->last_error;
                    error_log('Error deleting media records from wp_smack_Media table: ' . $error_message);
                    wp_send_json_error('Error deleting media records from wp_smack_Media table: ' . esc_html($error_message));
                } else {
                    wp_send_json_success('Folder deleted from database: ' . esc_html($folderName));
                }
            }
        } else {
            wp_send_json_error('Folder not found in database: ' . esc_html($folderName));
        }
    }

    wp_die();
}

function AiomlSmackupdate_media_parent_column($term_id) {
    global $wpdb;
    $update_result = wp_cache_get('media_parent_update_result_' . $term_id);
    if ($update_result === false) {
        $update_result = $wpdb->update(
            "{$wpdb->prefix}smack_Media",
            array('parent' => 0),
            array('parent' => $term_id),
            array('%d'),
            array('%d')
        );

        wp_cache_set('media_parent_update_result_' . $term_id, $update_result);
    }
    return $update_result;
}



function AiomlSmack_delete_media_by_term_id($term_id) {
    global $wpdb;

    // Attempt to retrieve the result from cache
    $delete_result = wp_cache_get('delete_media_term_id_' . $term_id);

    // If the result is not found in cache, perform the deletion
    if ($delete_result === false) {
        // Perform the deletion action
        $delete_result = $wpdb->delete(
            "{$wpdb->prefix}smack_Media",
            array('term_id' => $term_id),
            array('%d')
        );

        // Cache the result for future use
        wp_cache_set('delete_media_term_id_' . $term_id, $delete_result);
    }

    // Return the result of the deletion action
    return $delete_result;
}



// When folder deleted, folder media moved to uncategorized area
function AiomlSmack_move_attachments_to_uncategorized($folderName) {
    if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'folder_nonce' ) )  {
        wp_send_json_error('Nonce verification failed.');
    }

    global $wpdb;

    // Attempt to retrieve term ID from cache
    $term_id = wp_cache_get('term_id_' . $folderName);

    // If term ID is not found in cache, fetch it from the database
    if ($term_id === false) {
        // Get term ID by name
        $term_id = $wpdb->get_var($wpdb->prepare(
            "SELECT term_id FROM {$wpdb->terms} WHERE name = %s",
            $folderName
        ));

        if (!$term_id) {
            error_log('Error retrieving term: Term not found.');
            return;
        }

        // Cache the term ID
        wp_cache_set('term_id_' . $folderName, $term_id);
    }

    // Attempt to retrieve attachment IDs from cache
    $attachment_ids = wp_cache_get('attachment_ids_' . $term_id);

    // If attachment IDs are not found in cache, fetch them from the database
    if ($attachment_ids === false) {
        // Fetch attachment IDs associated with the term directly from the database
        $attachment_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT p.ID 
            FROM {$wpdb->posts} p 
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id 
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
            WHERE tt.term_id = %d 
            AND p.post_type = 'attachment'",
            $term_id
        ));

        // Cache the attachment IDs
        wp_cache_set('attachment_ids_' . $term_id, $attachment_ids);
    }

    foreach ($attachment_ids as $attachment_id) {
        // Update the post parent.
        $updated = wp_update_post(array(
            'ID'          => $attachment_id,
            // 'post_parent' => 0, // if needed to make the media unattached
        ));

        if (is_wp_error($updated)) {
            error_log('Error updating post parent for attachment ' . $attachment_id . ': ' . $updated->get_error_message());
        } else {
            // Set the term relationship to "uncategorized" and check for errors.
            $result = wp_set_object_terms($attachment_id, 'uncategorized', 'attachment_category', true); // Set true to append term
            if (is_wp_error($result)) {
                error_log('Error setting object terms for attachment ' . $attachment_id . ': ' . $result->get_error_message());
            } else {
                error_log('Attachment ' . $attachment_id . ' moved to "uncategorized" category successfully.');
            }
        }
    }
}


add_action('wp_ajax_AiomlSmackupdate_folder_in_database', 'Smackcoders\AiomlSmack\AiomlSmackupdate_folder_in_database_callback');

function AiomlSmackupdate_folder_in_database_callback() {
    // Nonce verification
    check_ajax_referer('folder_nonce', 'security');

    global $wpdb;
    if (isset($_POST['folder_name']) && isset($_POST['new_name'])) {
    // Sanitize input data
    
    $folderName = sanitize_text_field($_POST['folder_name']);
    $newName = sanitize_text_field($_POST['new_name']);
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $newName)) {
        wp_send_json_error('Folder name cannot contain special characters.');
    }

    // Get term object by name
    $term = get_term_by('name', $folderName, 'attachment_category');

    if ($term) {
        $term_id = $term->term_id;

        // Update term name
        $term_update_result = wp_update_term($term_id, 'attachment_category', array('name' => $newName));

        if (!is_wp_error($term_update_result)) {
            // Clear term cache
            wp_cache_delete($term_id, 'terms');

            // Update the folder name in the wp_smack_Media table
            $update_media_result = $wpdb->update(
                $wpdb->prefix . 'smack_Media',
                array('name' => $newName),
                array('term_id' => $term_id)
            );

            if ($update_media_result !== false) {
                wp_send_json_success('Folder name updated to: ' . esc_html($newName));
            } else {
                $error_message = $wpdb->last_error;
                error_log('Error updating folder name in wp_smack_Media table: ' . $error_message);
                wp_send_json_error('Error updating folder name in wp_smack_Media table: ' . esc_html($error_message));
            }
        } else {
            $error_message = $term_update_result->get_error_message();
            error_log('Error updating folder name in wp_terms table: ' . $error_message);
            wp_send_json_error('Error updating folder name in wp_terms table: ' . esc_html($error_message));
        }
    } else {
        wp_send_json_error('Folder not found in database: ' . esc_html($folderName));
    }
    } else {
    wp_send_json_error('Missing folder name or new name.');
    }
    wp_die();
}


// folder drag and drop
add_action('wp_ajax_AiomlSmackfolder_DragDrop_database', 'Smackcoders\AiomlSmack\AiomlSmackfolder_DragDrop_database_callback');

function AiomlSmackfolder_DragDrop_database_callback() {
    if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'folder_nonce' ) )  {
    wp_send_json_error('Nonce verification failed.');
    } 

    global $wpdb;
    
    if (isset($_POST['dragged']) && isset($_POST['target'])) {
    // Sanitize input data
    $draggedFolder = sanitize_text_field($_POST['dragged']);
    $targetFolder = sanitize_text_field($_POST['target']);

    // Update the database table
    $table_name = $wpdb->prefix . 'smack_Media'; 
    $data = array('parent' => $targetFolder); 
    $where = array('term_id' => $draggedFolder); 
    
    // Use wp_cache_delete() to clear cached data before updating the database
    $cache_key = 'folder_data_' . $draggedFolder; // Adjust cache key based on your requirement
    wp_cache_delete($cache_key, 'folder_data');

    // Update the database
    $result = $wpdb->update($table_name, $data, $where);

    if ($result === false) {
        // Update failed
        echo "Error updating folder in the database.";
    } else {
        // Update successful
        echo "Folder updated successfully.";
    }
    }

    wp_die();
}




// making relation with media and taxo
add_action('wp_ajax_AiomlSmack_move_attachments_to_category', 'Smackcoders\AiomlSmack\AiomlSmack_move_attachments_to_category_callback');

function AiomlSmack_move_attachments_to_category_callback() { 
    if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'folder_nonce' ) )  {
        wp_send_json_error('Nonce verification failed.');
       
    } 
    // Check if the required parameters are set.
    if (isset($_POST['attachment_id'], $_POST['folder_name'])) {
        $attachment_id = absint($_POST['attachment_id']);
        $folder_name = sanitize_text_field($_POST['folder_name']);

        // Retrieve the term by slug from the specified taxonomy.
        $term = get_term_by('slug', $folder_name, 'attachment_category');

        if ($term && !is_wp_error($term)) {
            // If the term exists and there is no error.
            $term_id = $term->term_id;
            
            // Update the post parent.
            $updated = wp_update_post(array(
                'ID'          => $attachment_id,
                'post_parent' => $term_id,
            ));

            if (is_wp_error($updated)) {
                wp_send_json_error('Error updating post parent: ' . $updated->get_error_message());
            } else {
                // Set the term relationship and check for errors.
                $result = wp_set_object_terms($attachment_id, [$term_id], 'attachment_category');
                if (is_wp_error($result)) {
                    wp_send_json_error('Error setting object terms: ' . $result->get_error_message());
                } else {
                    wp_send_json_success('Media moved to category successfully.');
                }
            }
        } else {
            wp_send_json_error('Error retrieving term: Term not found.');
        }
    } else {
        wp_send_json_error('Missing parameters.');
    }   
}