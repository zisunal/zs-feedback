<?php

if ( ! function_exists( 'adm_method' ) ) {
    /**
     * adm_method function.
     *
     * @param string $method Method name to call.
     *
     * @return array | null
     */
    function adm_method( $method ) {
        return [ 'ZS\FeedbackAdmin', $method ];
    }
}

if ( ! function_exists( 'zs_method' ) ) {
    /**
     * zs_method function.
     *
     * @param string $method Method name to call.
     *
     * @return array | null
     */
    function zs_method( $method ) {
        return [ 'ZS\Feedback', $method ];
    }
}

if ( ! function_exists( 'inc_page' ) ) {
    /**
     * inc_page function.
     *
     * @param string $page Page name to include.
     * @param array $args Arguments to pass to the page.
     *
     * @return void
     */
    function inc_page( $page, ...$args ) {
        if ( ! file_exists( ZS_FEEDBACK_DIR . 'pages/' . $page . '.php' ) ) {
            return;
        }
        extract( $args );
        require_once ZS_FEEDBACK_DIR . 'pages/' . $page . '.php';
    }
}