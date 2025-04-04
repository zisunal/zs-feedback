<?php
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class FeedbackPostList extends WP_List_Table
{
    public function __construct( ) {
        parent::__construct( [
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false
        ] );
    }
    public function get_columns( ) {
        return [
            'cb'        => '<input type="checkbox" />',
            'title'     => 'Post Title',
            'ratings'  => 'Overall Rating',
            'date'      => 'Date',
            'see'       => 'See Feedbacks'
        ];
    }
    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="post[]" value="%s" />', $item->ID );
    }
    public function column_title( $item ) {
        return sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=zs-feedback&post_id=' . $item->ID ), esc_html( $item->post_title ) );
    }
    public function column_ratings( $item ) {
        global $wpdb;
        $ratings = $wpdb->get_results( $wpdb->prepare( "SELECT rating FROM {$wpdb->prefix}zs_feedback WHERE page_id = %d", $item->ID ) );
        if ( ! $ratings ) {
            return 'No Ratings Yet';
        }
        $total_ratings = count( $ratings );
        $sum_ratings = 0;
        foreach ( $ratings as $rating ) {
            $sum_ratings += intval( $rating->rating );
        }
        $average_rating = $sum_ratings / $total_ratings;
        return sprintf( '%.2f (%d ratings)', $average_rating, $total_ratings );
    }
    public function column_date( $item ) {
        return get_the_date( 'M d, Y', $item->ID );
    }
    public function column_see( $item ) {
        return sprintf( '<a href="%s">See Feedbacks</a>', admin_url( 'admin.php?page=zs-feedback&post_id=' . $item->ID ) );
    }
    public function column_default( $item, $column_name ) {
        $method = 'column_' . $column_name;
        if ( method_exists( $this, $method ) ) {
            return $this->$method( $item );
        }
        return '';
    }
    public function get_sortable_columns( ) {
        return [
            'title'     => 'title',
            'date'      => 'date'
        ];
    }
    public function get_bulk_actions( ) {
        return [
            'delete' => 'Delete',
            'enable' => 'Enable Feedback',
            'disable' => 'Disable Feedback'
        ];
    }
    public function process_bulk_action( ) {
        if ( 'delete' === $this->current_action( ) ) {
            $post_ids = isset( $_POST[ 'post' ] ) ? array_map( 'intval', $_POST[ 'post' ] ) : [ ];
            foreach ( $post_ids as $post_id ) {
                wp_delete_post( $post_id, true );
            }
        } else if ( 'enable' === $this->current_action( ) ) {
            $post_ids = isset( $_POST[ 'post' ] ) ? array_map( 'intval', $_POST[ 'post' ] ) : [ ];
            foreach ( $post_ids as $post_id ) {
                update_post_meta( $post_id, 'zs_fd_enable_feedback', 1 );
            }
        } else if ( 'disable' === $this->current_action( ) ) {
            $post_ids = isset( $_POST[ 'post' ] ) ? array_map( 'intval', $_POST[ 'post' ] ) : [ ];
            foreach ( $post_ids as $post_id ) {
                update_post_meta( $post_id, 'zs_fd_enable_feedback', 0 );
            }
        }
    }
    public function get_items_per_page( $option, $default_value = 20 ) {
        $per_page = get_user_option( $option );
        if ( ! $per_page ) {
            $per_page = $default_value;
        }
        return $per_page;
    }
    public function prepare_items( ) {
        $this->process_bulk_action();
        $per_page = $this->get_items_per_page( 'posts_per_page' );
        $current_page = $this->get_pagenum( );
        $post_types = get_post_types(['public' => true], 'names');
        if (!in_array('page', $post_types)) {
            $post_types[] = 'page';
        }
        $query = new WP_Query( [
            'post_type'      => $post_types,
            'meta_key'       => 'zs_fd_enable_feedback',
            'meta_value'     => ['1', 1, true],
            'posts_per_page' => $per_page,
            'paged'          => $current_page
        ] );
        $this->items = $query->posts;
        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [ $columns, $hidden, $sortable ];
        $this->set_pagination_args( [
            'total_items' => $query->found_posts,
            'per_page'    => $per_page,
            'total_pages' => $query->max_num_pages
        ] );
    }
}