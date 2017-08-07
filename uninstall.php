<?php

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

// Deletes feed key stored in DB 
delete_option( 'wpfkg_feed_key' );

// Deletes feed key status options stored in DB
delete_option( 'wpfkg_feed_key_status' );

?>