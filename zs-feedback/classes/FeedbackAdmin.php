<?php
namespace ZS;
class FeedbackAdmin
{
    public function __construct( )
    {
        $this->init();
    }
    public function init( )
    {
        add_action( 'admin_menu', adm_method( 'menu' ) );
        add_action( 'admin_menu', adm_method( 'sub_menu' ) );
        add_action( 'admin_enqueue_scripts', adm_method( 'load_assets' ) );
        add_action( 'wp_ajax_zs_fd_save_settings', adm_method( 'settings' ) );
        add_action( 'add_meta_boxes', adm_method( 'add_meta_boxes' ) );
        add_action( 'save_post', adm_method( 'save_meta_box' ) );
    }
    public static function activate( )
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zs_feedback';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            page_id BIGINT(20) UNSIGNED NOT NULL,
            user_id BIGINT(20) UNSIGNED NULL,
            user_ip varchar(100) NULL,
            user_agent varchar(255) NULL,
            feedback text NULL,
            rating ENUM('1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5') NOT NULL DEFAULT '3',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (page_id) REFERENCES {$wpdb->prefix}posts(ID) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $settings = [
            'enable'                => 1,
            'excluded_user_ids'     => [ ],
            'excluded_user_ips'     => [ ],
            'excluded_user_agents'  => [ ],
        ];
        $possible_user_agents = [
            'browsers' => [
                'chrome' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                'firefox' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:122.0) Gecko/20100101 Firefox/122.0',
                'safari' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Version/17.0 Safari/537.36',
                'edge' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0',
                'opera' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 OPR/121.0.0.0',
            ],
            // Mobile Browsers
            'mobile_browsers' => [
                'android_chrome' => 'Mozilla/5.0 (Linux; Android 14; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
                'iphone_safari' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/537.36 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/537.36',
                'samsung_browser' => 'Mozilla/5.0 (Linux; Android 13; Samsung Galaxy S23) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36 SamsungBrowser/21.0',
                'opera_mobile' => 'Mozilla/5.0 (Linux; Android 13; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36 OPR/79.0.0.0',
            ],
            // Search Engine Bots
            'bots' => [
                'googlebot' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
                'bingbot' => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
                'duckduckbot' => 'DuckDuckBot/1.0; (+http://duckduckgo.com/duckduckbot.html)',
                'facebook_bot' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
                'twitter_bot' => 'Mozilla/5.0 (compatible; Twitterbot/1.0)',
            ],
            // Command Line Tools & Scrapers
            'scrapers' => [
                'curl' => 'curl/7.68.0',
                'wget' => 'Wget/1.21.1 (linux-gnu)',
                'postman' => 'PostmanRuntime/7.32.2',
                'python_requests' => 'python-requests/2.31.0',
            ],
            // Electron Apps
            'electron_apps' => [
                'discord' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Electron/24.0.0 Chrome/120.0.0.0 Safari/537.36',
                'slack' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Electron/24.0.0 Chrome/120.0.0.0 Safari/537.36',
            ],
        ];
        add_option( 'zs_fd_settings', $settings );
        add_option( 'zs_fd_user_agents', $possible_user_agents );
        add_option( 'zs_fd_version', ZS_FEEDBACK_VERSION );
    }
    public static function deactivate( )
    {
        // Deactivation code here
    }
    public static function uninstall( )
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zs_feedback';
        $sql = "DROP TABLE IF EXISTS $table_name;";
        $wpdb->query( $sql );
        delete_option( 'zs_fd_settings' );
        delete_option( 'zs_fd_version' );
        delete_option( 'zs_fd_user_agents' );
    }
    public static function load_assets( )
    {
        wp_enqueue_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_style( 'zs-feedback-admin', ZS_FEEDBACK_URL . 'assets/css/admin.css', [ ], WP_DEBUG ? time() : ZS_FEEDBACK_VERSION );
        wp_enqueue_script( 'swal2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11' );
        wp_enqueue_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', [ 'jquery' ] );
        wp_enqueue_script( 'zs-feedback-admin', ZS_FEEDBACK_URL . 'assets/js/admin.js', [ 'jquery', 'select2', 'swal2' ], WP_DEBUG ? time() : ZS_FEEDBACK_VERSION, true );
        wp_localize_script( 'zs-feedback-admin', 'zs_feedback', [
            'nonce'    => wp_create_nonce( 'zs_feedback_nonce' ),
            'excluded_users' => get_option( 'zs_fd_settings', [ ] )[ 'excluded_user_ids' ],
            'excluded_ips' => get_option( 'zs_fd_settings', [ ] )[ 'excluded_user_ips' ],
            'excluded_agents' => get_option( 'zs_fd_settings', [ ] )[ 'excluded_user_agents' ],
        ] );
    }
    public static function menu( )
    {
        add_menu_page(
            'Zs Feedback',
            'Zs Feedback',
            'manage_options',
            'zs-feedback',
            adm_method( 'feedback_page' ),
            'dashicons-feedback',
            6
        );
    }
    public static function sub_menu( )
    {
        add_submenu_page(
            'zs-feedback',
            'Settings',
            'Settings',
            'manage_options',
            'zs-feedback-settings',
            adm_method( 'settings_page' )
        );
    }
    public static function feedback_page( )
    {
        inc_page( 'feedbacks' );
    }
    public static function settings_page( )
    {
        inc_page( 'settings' );
    }
    public static function settings( ) {
        check_ajax_referer( 'zs_feedback_nonce', 'nonce' );
        $settings = get_option( 'zs_fd_settings', [ ] );
        $settings[ 'enable' ] = isset( $_POST[ 'enabled' ] ) ? $_POST[ 'enabled' ] : 1;
        $settings[ 'excluded_user_ids' ] = isset( $_POST[ 'excluded_user_ids' ] ) ? array_map( 'intval', $_POST[ 'excluded_user_ids' ] ) : [ ];
        $settings[ 'excluded_user_ips' ] = isset( $_POST[ 'excluded_user_ips' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'excluded_user_ips' ] ) : [ ];
        $settings[ 'excluded_user_agents' ] = isset( $_POST[ 'excluded_user_agents' ] ) ? array_map( 'sanitize_text_field', $_POST[ 'excluded_user_agents' ] ) : [ ];
        update_option( 'zs_fd_settings', $settings );
        wp_send_json_success( [
            'message' => __( 'Settings saved successfully.' ),
            'data'    => $settings,
        ] );
    }
    public static function add_meta_boxes( $post_type )
    {
        add_meta_box(
            'zs_fd_meta_box',
            __( 'ZS Feedback' ),
            adm_method( 'render_meta_box' ),
            $post_type,
            'side',
            'high'
        );
    }
    public static function render_meta_box( $post )
    {
        $is_page = get_post_type( $post ) === 'page';
        ?>
            <div class="zs_fd_meta_box">
                <input type="hidden" name="zs_fd_meta_box_nonce" value="<?php echo wp_create_nonce( 'zs_fd_meta_box_nonce' ); ?>" />
                <label for="<?php echo $is_page ? 'page' : 'post' . '_id'; ?>">
                    <input type="checkbox" id="<?php echo $is_page ? 'page' : 'post' . '_id'; ?>" name="<?php echo $is_page ? 'page' : 'post' . '_id'; ?>" <?php checked( get_post_meta( $post->ID, 'zs_fd_enable_feedback', true ), "1" ); ?> />
                    <?php _e( 'Enable Feedback', 'zs-feedback' ); ?>
                </label>
            </div>
        <?php
    }
    public static function save_meta_box( $post_id )
    {
        if ( ! isset( $_POST[ 'zs_fd_meta_box_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'zs_fd_meta_box_nonce' ], 'zs_fd_meta_box_nonce' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        $is_page = get_post_type( $post_id ) === 'page';
        update_post_meta( $post_id, 'zs_fd_enable_feedback', isset( $_POST[ $is_page ? 'page' : 'post' . '_id' ] ) ? 1 : 0 );
    }
}