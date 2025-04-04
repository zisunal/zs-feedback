<?php
global $wpdb;
if ( isset( $_GET[ 'post_id' ] ) ) {
    $post_id = intval( $_GET[ 'post_id' ] );
    $post = get_post( $post_id );
    $feedbacks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}zs_feedback WHERE page_id = %d", $post_id ) );
?>
    <div class="wrap">
        <div style="margin-bottom: 20px;">
            <h1 class="wp-heading-inline">Feedbacks for: <?php echo $post->post_title; ?></h1>
            <a href="<?php echo admin_url( 'admin.php?page=zs-feedback' ); ?>" class="page-title-action">Back to Feedbacks</a>
        </div>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Rating</th>
                    <th>Feedback</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $feedbacks as $feedback ) : ?>
                    <tr>
                        <td><?= $feedback->rating; ?></td>
                        <td><?= $feedback->feedback; ?></td>
                        <td><?= date( 'Y-m-d H:i:s', strtotime( $feedback->created_at ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>   
<?php
} else {
    require_once ZS_FEEDBACK_DIR . 'classes/FeedbackPostList.php';
    $list_table = new FeedbackPostList();
    $list_table->prepare_items();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Feedbacks</h1>
        <form method="post">
        <?php $list_table->display(); ?>
        </form>
    </div>
<?php
}
?>