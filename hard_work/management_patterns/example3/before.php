<?php
public function removeExpiredBans(): void
{
    $banned = get_option( 'banned_data' );
    foreach ( $banned as $iphash => $value ) {
        if ( current_time( 'U', true ) > $value['expired'] ) {
            unset( $banned[$iphash] );
        }
    }
    update_option( 'banned_data', $banned );
}