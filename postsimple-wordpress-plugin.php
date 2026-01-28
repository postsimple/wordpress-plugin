<?php
/**
 * Plugin Name: PostSimple WordPress Integration
 * Plugin URI: https://postsimple.app
 * Description: Verzend WordPress posts naar PostSimple om automatisch social media content te genereren.
 * Version: 1.0.0
 * Author: PostSimple
 * Author URI: https://postsimple.app
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: postsimple
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class PostSimple_WordPress_Integration {
    
    private $api_endpoint = 'https://postsimple.link/api/plugins/create-post';
    private $postsimple_app_url = 'https://my.postsimple.app/';
    
    public function __construct() {
        // Add settings page
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Add meta box to all post types
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        
        // Handle AJAX request
        add_action('wp_ajax_postsimple_send_post', array($this, 'handle_send_to_postsimple'));
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add settings page to WordPress admin
     */
    public function add_settings_page() {
        add_options_page(
            'PostSimple Instellingen',
            'PostSimple',
            'manage_options',
            'postsimple-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('postsimple_settings', 'postsimple_api_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Check if settings were saved
        if (isset($_GET['settings-updated'])) {
            add_settings_error('postsimple_messages', 'postsimple_message', 'Instellingen opgeslagen', 'updated');
        }
        
        settings_errors('postsimple_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('postsimple_settings');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="postsimple_api_key">PostSimple API Key</label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="postsimple_api_key" 
                                name="postsimple_api_key" 
                                value="<?php echo esc_attr(get_option('postsimple_api_key')); ?>" 
                                class="regular-text"
                            />
                            <p class="description">
                                Voer je PostSimple API key in. Je kunt deze vinden in je PostSimple account instellingen.
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Instellingen opslaan'); ?>
            </form>
            
            <hr>
            
            <h2>Hoe te gebruiken</h2>
            <ol>
                <li>Voer je PostSimple API key hierboven in en sla op.</li>
                <li>Ga naar een post, pagina of ander content type.</li>
                <li>Zoek de "PostSimple" meta box in de sidebar.</li>
                <li>Klik op "Verzend naar PostSimple" om de post te verzenden.</li>
                <li>Je wordt doorverwezen naar PostSimple waar je de gegenereerde social media content kunt bekijken.</li>
            </ol>
        </div>
        <?php
    }
    
    /**
     * Add meta box to all post types
     */
    public function add_meta_box() {
        $post_types = get_post_types(array('public' => true), 'names');
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'postsimple_meta_box',
                'PostSimple',
                array($this, 'render_meta_box'),
                $post_type,
                'side',
                'default'
            );
        }
    }
    
    /**
     * Render meta box content
     */
    public function render_meta_box($post) {
        $api_key = get_option('postsimple_api_key');
        
        if (empty($api_key)) {
            ?>
            <p style="color: #d63638;">
                <strong>Let op:</strong> Stel eerst je PostSimple API key in bij 
                <a href="<?php echo admin_url('options-general.php?page=postsimple-settings'); ?>">Instellingen</a>.
            </p>
            <?php
            return;
        }
        
        // Check if post is published or has a permalink
        $post_status = get_post_status($post->ID);
        $permalink = get_permalink($post->ID);
        
        if ($post_status !== 'publish' && $post_status !== 'future' && $post_status !== 'draft' && $post_status !== 'pending') {
            ?>
            <p style="color: #d63638;">
                Deze post moet minimaal een concept zijn om naar PostSimple te kunnen verzenden.
            </p>
            <?php
            return;
        }
        
        wp_nonce_field('postsimple_send_post', 'postsimple_nonce');
        ?>
        
        <div id="postsimple-meta-box-content">
            <p>
                Verzend deze post naar PostSimple om automatisch social media content te genereren.
            </p>
            
            <p>
                <strong>Titel:</strong> <?php echo esc_html(get_the_title($post->ID)); ?>
            </p>
            
            <p>
                <strong>URL:</strong> <?php echo esc_html($permalink); ?>
            </p>
            
            <div id="postsimple-status-message" style="display: none; padding: 10px; margin: 10px 0; border-left: 4px solid #00a32a; background: #f0f6fc;"></div>
            
            <div id="postsimple-error-message" style="display: none; padding: 10px; margin: 10px 0; border-left: 4px solid #d63638; background: #fcf0f1; color: #d63638;"></div>
            
            <button 
                type="button" 
                id="postsimple-send-button" 
                class="button button-primary button-large" 
                style="width: 100%;"
                data-post-id="<?php echo esc_attr($post->ID); ?>"
            >
                <span class="dashicons dashicons-share" style="margin-top: 3px;"></span> 
                Verzend naar PostSimple
            </button>
            
            <div id="postsimple-loading" style="display: none; text-align: center; margin-top: 10px;">
                <span class="spinner is-active" style="float: none; margin: 0;"></span>
                <p>Bezig met verzenden...</p>
            </div>
        </div>
        
        <style>
            #postsimple-send-button .dashicons {
                display: inline-block;
                vertical-align: middle;
            }
        </style>
        <?php
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on post edit screens
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        wp_enqueue_script(
            'postsimple-admin',
            plugins_url('postsimple-admin.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('postsimple-admin', 'postsimpleData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('postsimple_send_post'),
            'postsimpleUrl' => $this->postsimple_app_url
        ));
    }
    
    /**
     * Handle AJAX request to send post to PostSimple
     */
    public function handle_send_to_postsimple() {
        // Verify nonce
        check_ajax_referer('postsimple_send_post', 'nonce');
        
        // Check user permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Onvoldoende rechten.'));
            return;
        }
        
        // Get post ID
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        if (!$post_id) {
            wp_send_json_error(array('message' => 'Ongeldige post ID.'));
            return;
        }
        
        // Get post data
        $post = get_post($post_id);
        
        if (!$post) {
            wp_send_json_error(array('message' => 'Post niet gevonden.'));
            return;
        }
        
        // Get API key
        $api_key = get_option('postsimple_api_key');
        
        if (empty($api_key)) {
            wp_send_json_error(array('message' => 'PostSimple API key is niet ingesteld.'));
            return;
        }
        
        // Prepare data
        $title = get_the_title($post_id);
        $url = get_permalink($post_id);
        
        // Make API request
        $response = wp_remote_post($this->api_endpoint, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-API-PostSimple-Key' => $api_key
            ),
            'body' => json_encode(array(
                'title' => $title,
                'url' => $url
            )),
            'timeout' => 30
        ));
        
        // Check for errors
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => 'Fout bij verbinden met PostSimple: ' . $response->get_error_message()
            ));
            return;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Check response status
        if ($status_code !== 200) {
            $error_message = 'Fout bij verzenden naar PostSimple.';
            
            if (isset($data['message'])) {
                $error_message .= ' ' . $data['message'];
            } elseif (isset($data['error'])) {
                $error_message .= ' ' . $data['error'];
            }
            
            wp_send_json_error(array('message' => $error_message));
            return;
        }
        
        // Check for batch_id in response
        if (!isset($data['batch_id'])) {
            wp_send_json_error(array('message' => 'Geen batch ID ontvangen van PostSimple.'));
            return;
        }
        
        // Success!
        wp_send_json_success(array(
            'batch_id' => $data['batch_id'],
            'redirect_url' => $this->postsimple_app_url . '?batch=' . $data['batch_id'],
            'message' => 'Post succesvol verzonden naar PostSimple!'
        ));
    }
}

// Initialize the plugin
new PostSimple_WordPress_Integration();
