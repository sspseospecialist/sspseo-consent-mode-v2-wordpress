<?php
/**
 * SSPSEO BACKEND PRIVACY CONTROLLER
 * Author: Silviu Petru Stetco (SSPSEO)
 * Description: Secure storage for the immutable Proof of Consent audit log database.
 */

if (!defined('ABSPATH')) exit; // Sandbox protection

// 1. DATABASE HOOK - RUNS AUTOMATICALLY ON INIT
add_action('init', 'sspseo_create_cookie_registry_table');
function sspseo_create_cookie_registry_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sspseo_cookie_consents';
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            consent_id varchar(50) NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            analytics varchar(10) NOT NULL,
            marketing varchar(10) NOT NULL,
            personalization varchar(10) NOT NULL,
            user_agent text NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY consent_id (consent_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// 2. SECURE ASYNCHRONOUS ENDPOINT FOR INCOMING PRIVACY PAYLOADS
add_action('wp_ajax_sspseo_save_cookie_consent', 'sspseo_save_cookie_consent_callback');
add_action('wp_ajax_nopriv_sspseo_save_cookie_consent', 'sspseo_save_cookie_consent_callback');

function sspseo_save_cookie_consent_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sspseo_cookie_consents';
    
    $consent_id      = sanitize_text_field($_POST['consent_id'] ?? '');
    $analytics       = sanitize_text_field($_POST['analytics'] ?? 'denied');
    $marketing       = sanitize_text_field($_POST['marketing'] ?? 'denied');
    $personalization = sanitize_text_field($_POST['personalization'] ?? 'denied');
    $user_agent      = sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? '');
    
    if (empty($consent_id)) {
        wp_send_json_error('Missing Consent Unique Key UUID', 400);
    }
    
    // Structured insert query (Strict data scrubbing — IP omitted intentionally for GDPR vectoring)
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'consent_id'      => $consent_id,
            'analytics'       => $analytics,
            'marketing'       => $marketing,
            'personalization' => $personalization,
            'user_agent'      => $user_agent
        ),
        array('%s', '%s', '%s', '%s', '%s')
    );
    
    if ($inserted) {
        wp_send_json_success('Proof of Consent successfully logged into the database.');
    } else {
        wp_send_json_error('Database failure upon logging transaction.', 500);
    }
}
