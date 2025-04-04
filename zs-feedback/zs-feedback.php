<?php
/**
 * Plugin Name:     Zs Feedback
 * Plugin URI:      https://fiverr.com/zisun1
 * Description:     A simple plugin to add feedback form to the selected pages and posts. You can choose post/page that needs to get feedback from users. If the user currently on the screen already has not submitted the feedback form earlier or said not to show that form again, he/she will be prompted to submit the feedback form. You can check the feedbacks from the wp admin without the information of the user. You can update different settings of the plugin from the wp admin
 * Author:          Zisunal
 * Author URI:      https://fiverr.com/zisun1
 * Text Domain:     zs
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Zs_Feedback
 */

// Your code starts here.

// If accessed directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No script kiddies please!' );
}

if ( !defined( 'ZS_FEEDBACK_DIR' ) ) {
    define( 'ZS_FEEDBACK_DIR', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'ZS_FEEDBACK_URL' ) ) {
    define( 'ZS_FEEDBACK_URL', plugin_dir_url( __FILE__ ) );
}

require_once ZS_FEEDBACK_DIR . 'libs/functions.php';
require_once ZS_FEEDBACK_DIR . 'libs/init.php';

register_activation_hook( __FILE__, adm_method( 'activate' ) );
register_deactivation_hook( __FILE__, adm_method( 'deactivate' ) );
register_uninstall_hook( __FILE__, adm_method( 'uninstall' ) );

