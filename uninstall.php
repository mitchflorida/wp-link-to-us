<?php
	if (!defined('WP_UNINSTALL_PLUGIN')) {exit;}
    // delete a NEW table that was created
    global $wpdb;
    $table_xyz = $wpdb->prefix . 'wplinktous';
    $wpdb->query("DROP TABLE IF EXISTS wp_wplinktous");
?>