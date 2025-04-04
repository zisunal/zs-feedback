<?php
namespace ZS;
class Feedback
{
    public function __construct( )
    {
        $this->init();
    }
    public function init( )
    {
        add_action( 'wp_ajax_zs_feedback', zs_method( 'feedback' ) );
        add_action( 'wp_ajax_nopriv_zs_feedback', zs_method( 'feedback' ) );
        add_action( 'wp_enqueue_scripts', zs_method( 'load_assets' ) );
    }
    public static function load_assets( )
    {
        if ( self::excluded() ) {
            return;
        }
        wp_enqueue_style( 'star-rating', ZS_FEEDBACK_URL . 'assets/css/star-rating.min.css' );
        wp_enqueue_style( 'zs-feedback', ZS_FEEDBACK_URL . 'assets/css/app.css', [], ZS_FEEDBACK_VERSION );
        wp_enqueue_script( 'star-rating', ZS_FEEDBACK_URL . 'assets/js/star-rating.min.js', [ ], ZS_FEEDBACK_VERSION, true );
        wp_enqueue_script( 'zs-feedback', ZS_FEEDBACK_URL . 'assets/js/feedback.js', [ 'jquery', 'star-rating' ], ZS_FEEDBACK_VERSION, true );
        wp_localize_script( 'zs-feedback', 'zs_feedback', [
            'nonce' => wp_create_nonce( 'zs_feedback_nonce' ),
            'page_id' => get_the_ID( ),
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ] );
    }
    protected static function excluded ( ) {
        $settings = get_option( 'zs_fd_settings' );
        if (! $settings ) {
            return true;
        }
        if ( ! isset( $settings['enable'] ) || $settings['enable'] != '1' ) {
            return true;
        }
        $user = wp_get_current_user();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $current_page_or_post_id = get_the_ID();
        $zs_fd_enable_feedback = get_post_meta( $current_page_or_post_id, 'zs_fd_enable_feedback', true );
        $excluded_ips = $settings[ 'excluded_user_ips' ];
        $setteled_exclusions_ips = [ ];
        if ( ! empty( $excluded_ips ) ) {
            foreach ( $excluded_ips as $ip ) {
                if ( strpos( $ip, '-' ) !== false ) {
                    $ip_range = explode( '-', $ip );
                    if ( count( $ip_range ) == 2 ) {
                        $start_ip = ip2long( trim( $ip_range[0] ) );
                        $end_ip = ip2long( trim( $ip_range[1] ) );
                        if ( $start_ip !== false && $end_ip !== false ) {
                            for ( $i = $start_ip; $i <= $end_ip; $i++ ) {
                                $setteled_exclusions_ips[] = long2ip( $i );
                            }
                        }
                    } else {
                        $setteled_exclusions_ips[] = trim( $ip );
                    }
                } else {
                    $setteled_exclusions_ips[] = trim( $ip );
                }
            }
        }
        if ( in_array( $user->ID, $settings[ 'excluded_user_ids' ] ) ) {
            return true;
        }
        if ( in_array( $user_agent, $settings[ 'excluded_user_agents' ] ) ) {
            return true;
        }
        if ( in_array( $user_ip, $setteled_exclusions_ips ) ) {
            return true;
        }
        if ( $zs_fd_enable_feedback == '0' ) {
            return true;
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'zs_feedback';
        $feedback = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE page_id = %d AND user_ip = %s AND user_agent = %s", $current_page_or_post_id, $user_ip, $user_agent ) );
        if ( $feedback ) {
            return true;
        }
        $feedback = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE page_id = %d AND user_id = %d", $current_page_or_post_id, $user->ID ) );
        if ( $feedback ) {
            return true;
        }
        return false;
    }
    public static function feedback( )
    {
        if ( ! check_ajax_referer( 'zs_feedback_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce' ) );
        }
        if ( ! isset( $_POST[ 'feedback' ] ) || empty( $_POST[ 'feedback' ] ) ) {
            wp_send_json_error( __( 'Feedback is required' ) );
        }
        if ( ! isset( $_POST[ 'rating' ] ) || empty( $_POST[ 'rating' ] ) ) {
            wp_send_json_error( __( 'Rating is required' ) );
        }
        if ( ! isset( $_POST[ 'page_id' ] ) || empty( $_POST[ 'page_id' ] ) ) {
            wp_send_json_error( __( 'Page ID is required' ) );
        }

        $page_id = intval( $_POST[ 'page_id' ] );
        $feedback = sanitize_text_field( $_POST[ 'feedback' ] );
        $rating = floatval( $_POST[ 'rating' ] );
        if ( $rating < 1 || $rating > 5 ) {
            wp_send_json_error( __( 'Rating must be between 1 and 5' ) );
        }
        $user = wp_get_current_user( );
        $feedback_data = [
            'page_id' => $page_id,
            'feedback' => $feedback,
            'rating' => $rating,
            'created_at' => current_time( 'mysql' ),
        ];
        if ( $user->ID ) {
            $feedback_data[ 'user_id' ] = $user->ID;
        } else {
            $feedback_data[ 'user_ip' ] = $_SERVER[ 'REMOTE_ADDR' ];
            $feedback_data[ 'user_agent' ] = $_SERVER[ 'HTTP_USER_AGENT' ];
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'zs_feedback';
        $result = $wpdb->insert( $table_name, $feedback_data );
        if ( $result === false ) {
            wp_send_json_error( __( 'Failed to submit feedback' ) );
        }
        $response = [ ];
        $response[ 'status' ] = 'success';
        $response[ 'message' ] = __( 'Feedback submitted successfully' );
        wp_send_json_success( $response );
    }
}