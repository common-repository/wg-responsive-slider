<?php

/*
  Plugin Name: WG Responsive Slider
  Plugin URI: http://webgrapple.com
  description: Web Grapple is the most powerful and intuitive WordPress plugin to create multiple sliders and use it into entire WordPress template system such as category.php, single.php, page.php, header.php, footer.php and even in custom templates. This plugin focuses on achieving WordPress cure functionality and provide features to end user to create simple slider, required little technical or even none technical users.
  Version: 1.0.0
  Author: Abdul baquee
  Author URI: https://twitter.com/abdulbaquee85
 */
if (!defined('ABSPATH'))
{
    exit('No direct script access allowed');
}
// Make sure we don't expose any info if called directly
if (!function_exists('add_action'))
{
    exit('Hi there!  I\'m just a plugin, not much I can do when called directly.');
}

define('WGRS_VERSION', '1.0.0');
define('WGRS_MINIMUM_WP_VERSION', '4.0');
define('WGRS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WGRS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Register hooks that are fired when the plugin is activated or deactivated.
register_activation_hook(__FILE__, 'wgrs_slider_activate');
register_deactivation_hook(__FILE__, 'wgrs_slider_deactivate');

function wgrs_slider_activate()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$wpdb->prefix}sliders (
            `id` int(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `slider_id` int(9) NOT NULL,
            `caption` varchar(255) NOT NULL,
            `description` text NOT NULL,
            `image_uri` varchar(255) NOT NULL,
            `slider_status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `inserted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP) $charset_collate;";

    $sql1 = "CREATE TABLE {$wpdb->prefix}slider_list (
            `id` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `slider_name` varchar(255) NOT NULL,
            `slider_status` enum('active','inactive') NOT NULL,
            `show_caption` enum('show','hide') NOT NULL DEFAULT 'show',
            `caption_align` enum('left_top','left_bottom', 'right_top', 'right_bottom') NOT NULL DEFAULT 'right_bottom',
            `slides_per_page` int(11) NOT NULL,
            `slide_effect` varchar(255) NOT NULL,
            `autoplay` enum('yes','no') NOT NULL DEFAULT 'yes',
            `nav` enum('yes','no') NOT NULL DEFAULT 'yes',
            `dots` enum('yes','no') NOT NULL DEFAULT 'yes',
            `loop` enum('yes','no') NOT NULL DEFAULT 'yes',
            `added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
    dbDelta($sql1);
}

function wgrs_slider_deactivate()
{
    $option_name = 'wporg_option';
    delete_option($option_name);
    //for site options in Multisite
    delete_site_option($option_name);
}

// Add menu
function wgrs_menu()
{
    add_menu_page("WG Sliders", "WG Sliders", "manage_options", "wgrs-slides", "wgrs_slider", WGRS_PLUGIN_URL . 'assets/img/icon.png');
    add_submenu_page("wgrs-slides", "Add New", "Add New Slide", "manage_options", "wgrs-uploads", "wgrs_uploads");
    add_submenu_page("wgrs-slides", "Slider Settings", "Slider Settings", "manage_options", "wgrs-settings", "wgrs_settings");
}

add_action("admin_menu", "wgrs_menu");

require_once( WGRS_PLUGIN_PATH . 'class.wgrs.slider.php' );

if (!function_exists('wp_handle_upload'))
{
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

function wgrs_slider()
{
    require_once( WGRS_PLUGIN_PATH . 'wgrs-slides.php' );
}

function wgrs_uploads()
{
    require_once( WGRS_PLUGIN_PATH . 'wgrs-upload.php' );
}

function wgrs_settings()
{
    require_once( WGRS_PLUGIN_PATH . 'wgrs-settings.php' );
}

function themeslug_enqueue_style()
{
    wp_enqueue_style('owl-carousel-min', WGRS_PLUGIN_URL . ('assets/css/owl.carousel.min.css'), false);
    wp_enqueue_style('owl-theme-default-min', WGRS_PLUGIN_URL . ('assets/css/owl.theme.default.min.css'), false);
    wp_enqueue_style('owl-animate', WGRS_PLUGIN_URL . ('assets/css/owl.animate.css'), false);
    wp_enqueue_style('wgrs-stylesheet', WGRS_PLUGIN_URL . ('assets/css/wgrs.stylesheet.css'), false);
}

function themeslug_enqueue_script()
{
    wp_enqueue_script('jquery', 'jQuery', false);
    wp_enqueue_script('owl-carousel-min', WGRS_PLUGIN_URL . ('assets/js/owl.carousel.min.js'), 'jQuery');
}

add_action('wp_enqueue_scripts', 'themeslug_enqueue_style');
add_action('wp_enqueue_scripts', 'themeslug_enqueue_script');

if (!function_exists('wgrs_custom_slider'))
{

    function wgrs_custom_slider($id, $array = false)
    {
        $obj = new Wgrs_responsive_slider();
        $id = intval($id);
        $single_slider = $obj->wgrs_single_slider($id);
        $data = array();
        if (is_array($single_slider) && count($single_slider) > 0 && isset($single_slider['slider_status']) && $single_slider['slider_status'] === 'active')
        {
            $data['show_caption'] = sanitize_text_field($single_slider['show_caption']);
            $data['caption_align'] = sanitize_text_field($single_slider['caption_align']);
            $slide_effect = (isset($single_slider['slide_effect']) && !empty($single_slider['slide_effect'])) ? sanitize_text_field($single_slider['slide_effect']) : 'fadeOut_fadeIn';
            $explode = explode("_", $slide_effect);
            $data['animateOut'] = $explode[0];
            $data['animateIn'] = $explode[1];
            $data['autoplay'] = sanitize_text_field($single_slider['autoplay']);
            $data['items'] = isset($single_slider['slides_per_page']) ? intval($single_slider['slides_per_page']) : 1;
            $data['nav'] = sanitize_text_field($single_slider['nav']);
            $data['dots'] = sanitize_text_field($single_slider['dots']);
            $data['loop'] = sanitize_text_field($single_slider['loop']);
            $data['slides'] = $obj->wgrs_slides_front_end($id);
        }

        if ($array === true)
        {
            return $data;
        }
        ob_start();
        include "wgrs-slider-template.php";
        return ob_get_clean();
    }

}

if (!function_exists('wgrs_shortcodes_init'))
{

    function wgrs_shortcodes_init()
    {
        add_shortcode('wgrs_gallery', 'wgrs_shortcode_handler');
    }

}

add_action('init', 'wgrs_shortcodes_init');

if (!function_exists('wgrs_shortcode_handler'))
{

    function wgrs_shortcode_handler($atts, $content = NULL, $tag = NULL)
    {
        $filter_tag = sanitize_text_field($tag);
        if (empty($filter_tag) || $filter_tag !== 'wgrs_gallery')
        {
            return '';
        }
        if (isset($atts['id']) && !empty($atts['id']))
        {
            $id = intval($atts['id']);
            return wgrs_custom_slider($id);
        }
    }

}

if (!function_exists('wgrs_do_output_buffer'))
{
    add_action('init', 'wgrs_do_output_buffer');

    function wgrs_do_output_buffer()
    {
        ob_start();
    }

}