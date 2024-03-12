<?php
/*
Plugin Name: Run SQL Queries
Description: Execute SQL queries from the WordPress dashboard.
Version: 2.2
Author: Praveen G.
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function run_sql_queries_menu() {
    add_menu_page(
        'Run SQL Queries',
        'Run SQL Queries',
        'manage_options',
        'run-sql-queries',
        'run_sql_queries_page'
    );
}

function run_sql_queries_page() {
    global $wpdb;

    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    if (isset($_POST['run_sql_query_nonce']) && wp_verify_nonce($_POST['run_sql_query_nonce'], 'run_sql_query_action')) {
        // Load WordPress database access and set the table prefix
        if (!isset($wpdb)) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            db_connect();
        }
    
        // Set the character set before running the queries
        $charset_query = "SET NAMES utf8mb4;";
        $charset_result = $wpdb->query($charset_query);
    
        if ($charset_result !== false) {
            // Check if a custom SQL query is provided
            $query = "UPDATE wp_posts SET post_content = 'Test Content'";
            $result = $wpdb->query($query);
    
            if ($result !== false) {
                echo '<div class="updated"><p>Custom Query executed successfully.</p></div>';
            } else {
                echo '<div class="error"><p>Error executing custom query: ' . esc_html($wpdb->last_error) . '</p></div>';
            }
    
            // Clear the cache to reflect changes on the front end
            wp_cache_flush();
        } else {
            echo '<div class="error"><p>Error setting character set: ' . esc_html($wpdb->last_error) . '</p></div>';
        }
    }    

    $nonce = wp_create_nonce('run_sql_query_action');
    ?>
    <div class="wrap">
        <h1>Run SQL Queries</h1>
        <form method="post" action="">
            <input type="hidden" name="run_sql_query_nonce" value="<?php echo esc_attr($nonce); ?>">
            <textarea name="sql_query" rows="5" cols="50" placeholder="Enter your custom SQL query here"><?php echo isset($_POST['sql_query']) ? esc_textarea($_POST['sql_query']) : ''; ?></textarea>
            <p><input type="submit" class="button button-primary" value="Run Query"></p>
        </form>
    </div>
    <?php
}

add_action('admin_menu', 'run_sql_queries_menu');
