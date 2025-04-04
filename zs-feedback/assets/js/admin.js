jQuery( document ).ready( function( $ ) {
    $( '.select2' ).select2( );
    $( '#zs-fd-excluded-users' ).on( 'change', function( e ) {
        const selecteds = $( this ).find(':selected');
        const selecteds_ids = selecteds.map( function( i, el ) {
            return $( el ).val( );
        } ).get( );
        if ( selecteds_ids.length > 0 ) {
            zs_feedback.excluded_users.push( ...selecteds_ids );
            $( '#zs-fd-excluded-users-list li.hint' ).remove( );
        }
        selecteds.each( function( i, el ) {
            $( '#zs-fd-excluded-users-list' ).append( '<li><a class="dashicons-before dashicons-trash" data-deltype="excluded_user_ids" data-del="' + $( el ).val( ) + '"></a>' + $( el ).text( ) + '</li>' );
        } );
    } );
    $( '#zs-fd-excluded-user-agents' ).on( 'change', function( e ) {
        const selecteds = $( this ).find(':selected');
        const selecteds_ids = selecteds.map( function( i, el ) {
            return $( el ).val( );
        } ).get( );
        if ( selecteds_ids.length > 0 ) {
            zs_feedback.excluded_agents.push( ...selecteds_ids );
            $( '#zs-fd-excluded-user-agents-list li.hint' ).remove( );
        }
        selecteds.each( function( i, el ) {
            $( '#zs-fd-excluded-user-agents-list' ).append( '<li><a class="dashicons-before dashicons-trash" data-deltype="excluded_user_agents" data-del="' + $( el ).val( ) + '"></a>' + $( el ).text( ) + '</li>' );
        } );
    } );
    $( '#zs-fd-excluded-user-ip' ).on( 'keyup', function( e ) {
        if ( e.keyCode === 13 ) {
            e.preventDefault( );
            const ip = $( this ).val( );
            if ( ip.length > 0 ) {
                zs_feedback.excluded_ips.push( ip );
                $( '#zs-fd-excluded-user-ip-list li.hint' ).remove( );
            }
            $( '#zs-fd-excluded-user-ip-list' ).append( '<li><a class="dashicons-before dashicons-trash" data-deltype="excluded_user_ips" data-del="' + ip + '"></a>' + ip + '</li>' );
            $( this ).val( '' );
        }
    } );
    $( 'a[data-atype="del"]' ).click( function( e ) {
        e.preventDefault( );
        const type = $( this ).data( 'deltype' );
        const id = $( this ).data( 'del' );
        if ( type === 'excluded_user_ids' ) {
            zs_feedback.excluded_users = zs_feedback.excluded_users.filter( function( el ) {
                return el != id;
            } );
        } else if ( type === 'excluded_user_agents' ) {
            zs_feedback.excluded_agents = zs_feedback.excluded_agents.filter( function( el ) {
                return el != id;
            } );
        } else if ( type === 'excluded_user_ips' ) {
            zs_feedback.excluded_ips = zs_feedback.excluded_ips.filter( function( el ) {
                return el != id;
            } );
        }
        $( this ).parent( ).remove( );
    } );
    $( '#settings-sub' ).click( function( e ) {
        e.preventDefault( );
        $( '.saver' ).addClass( 'active' );
        const data = {
            action: 'zs_fd_save_settings',
            enabled: $( '#zs-fd-enable' ).is( ':checked' ) ? 1 : 0,
            excluded_user_ids: zs_feedback.excluded_users,
            excluded_user_ips: zs_feedback.excluded_ips,
            excluded_user_agents: zs_feedback.excluded_agents,
            nonce: zs_feedback.nonce
        };
        $.post( ajaxurl, data, function( response ) {
            $( '.saver' ).removeClass( 'active' );
            if ( response.success ) {
                Swal.fire( {
                    icon: 'success',
                    title: 'Settings saved!',
                    showConfirmButton: false,
                    timer: 2000
                } ).then( function( result ) {
                    if ( result.isDismissed || result.isConfirmed ) {
                        window.location.reload( );
                    }
                } );
            } else {
                Swal.fire( {
                    icon: 'error',
                    title: 'Error!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                } ).then( function( result ) {
                    if ( result.isDismissed || result.isConfirmed ) {
                        window.location.reload( );
                    }
                } );
            }
        } );
    } );
} );