<?php
/**
* Plugin Name: PermaHammer
* Plugin URI: https://github.com/maldersIO/PermaHammer/
* Description: Pesky permalink errors be gone - automatically re-saves permalinks on end-user 404
* Version: 1.0.0
* Author: maldersIO
* Author URI: https://malders.io/
* License: GNU v3.0
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/* PermaHammer Start */
//______________________________________________________________________________

add_action('template_redirect', 'mldrs_permaHammer_flush_rewrite_rules_on_404');

function mldrs_permaHammer_flush_rewrite_rules_on_404() {
    if (is_404()) {
        // Sanitize HTTPS check
        $req_scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';

        // Sanitize and escape URL components
        $requested_url = esc_url_raw($req_scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        // Validate email and escape admin email
        $admin_email = is_email(get_option('admin_email')) ? get_option('admin_email') : '';

        // Sanitize and escape home URL
        $site_home_url = esc_url(get_home_url());

        // Resave Permalinks / Flush Rewrite Rules
        flush_rewrite_rules();

        // Error log that the workaround has been triggered, and include the 404'd URL
        error_log('Internal Page 404 Error encountered, flush_rewrite_rules action triggered for URL: ' . esc_url($requested_url));

        // Send email notification to admin
        wp_mail($admin_email, esc_html($site_home_url) . ' has encountered an Internal 404 Error', esc_url($requested_url) . ' on ' . esc_html($site_home_url) . ' triggered the workaround in place. Please visit internal pages and confirm working');
    }
}
