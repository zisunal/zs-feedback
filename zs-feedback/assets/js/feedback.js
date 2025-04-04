jQuery( document ).ready( function( $ ) {
    if ( localStorage.getItem( 'zs_fd_deny_list' ) ) {
        let denyList = JSON.parse( localStorage.getItem( 'zs_fd_deny_list' ) );
        if ( denyList.includes( zs_feedback.page_id ) ) {
            return;
        }
    } else {
        var stars = buildFeedbackForm( );
        setTimeout( function( ) {
            showFeedbackForm( );
        }, 5000 );
        $( document ).on( 'click', '#zs-feedback-close', function( ) {
            $( '#zs-feedback-form' ).removeClass( 'zs-feedback-form-open' );
            addToDenyList( );
        } );
        $( '#zs-fd-submit' ).on( 'click', function( ) {
            const rating = $( '#rating' ).val( );
            const feedback = $( '#feedback' ).val( );
            if ( rating === '' || feedback === '' ) {
                alert( 'Please fill in all fields.' );
                return;
            }
            const nonce = $( 'input[name="nonce"]' ).val( );
            const data = {
                action  : 'zs_feedback',
                nonce   : nonce,
                rating  : rating,
                feedback: feedback,
                page_id : zs_feedback.page_id
            };
            $( '#zs-feedback-form' ).removeClass( 'zs-feedback-form-open' );
            $.post( zs_feedback.ajax_url, data, function( response ) {
                if ( response.success ) {
                    alert( 'Thank you for your feedback!' );
                } else {
                    alert( 'There was an error submitting your feedback. Please try again.' );
                }
            } );
        } );
    }

    function buildFeedbackForm( ) {
        document.body.insertAdjacentHTML( 'beforeend', '<div id="zs-feedback-form"></div>' );
        const feedbackForm = $( '#zs-feedback-form' );
        const formHtml = `<div class="zs-feedback-form-container">
            <div class="zs-feedback-form-header">
                <h2>Your Feedback Matters</h2>
                <button id="zs-feedback-close" class="zs-feedback-close">X</button>
            </div>
            <div class="zs-feedback-form-body">
                <form action="javascript:;">
                    <input type="hidden" name="nonce" value="${zs_feedback.nonce}">
                    <label for="rating">Rate This Page</label>
                    <select class="star-rating" id="rating" name="rating">
                        <option value="">Select a rating</option>
                        <option value="5">Excellent</option>
                        <option value="4.5">Very Good</option>
                        <option value="4">Good</option>
                        <option value="3.5">Average</option>
                        <option value="3">So So</option>
                        <option value="2.5">Poor</option>
                        <option value="2">Bad</option>
                        <option value="1.5">Worse</option>
                        <option value="1">Terrible</option>
                    </select>
                    <label for="feedback">Your Feedback for this page</label>
                    <textarea id="feedback" name="feedback"></textarea>
                    <button type="button" id="zs-fd-submit">SUBMIT</button>
                </form>
            </div>
        </div>`;
        feedbackForm.html( formHtml );
        return new StarRating( '.star-rating', {
            maxStars: 10,
            stars: function (el, item, index) {
                el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect class="gl-star-full" width="19" height="19" x="2.5" y="2.5"/><polygon fill="#FFF" points="12 5.375 13.646 10.417 19 10.417 14.665 13.556 16.313 18.625 11.995 15.476 7.688 18.583 9.333 13.542 5 10.417 10.354 10.417"/></svg>';
            },
            onStarClick: function( rating ) {
                $( '#rating' ).val( rating );
            }
        } );
    }
    function showFeedbackForm( ) {
        $( '#zs-feedback-form' ).addClass( 'zs-feedback-form-open' );
        $( '#zs-feedback-close' ).click( function( ) {
            $( '#zs-feedback-form' ).removeClass( 'zs-feedback-form-open' );
            addToDenyList( );
        } );
    }
    function addToDenyList( ) {
        let denyList = JSON.parse( localStorage.getItem( 'zs_fd_deny_list' ) ) || [ ];
        denyList.push( zs_feedback.page_id );
        localStorage.setItem( 'zs_feedback_deny_list', JSON.stringify( denyList ) );
    }
} );