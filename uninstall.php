<?php

if (!defined('ABSPATH'))
{
    exit('No direct script access allowed');
}
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN'))
{
    exit('No direct access allowed to uninstall');
}

$option_name = 'wporg_option';
delete_option($option_name);
// for site options in Multisite
delete_site_option($option_name);

// drop a custom database table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sliders");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}slider_list");

