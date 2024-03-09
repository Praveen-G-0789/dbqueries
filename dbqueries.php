<?php
/*
Plugin Name: Run SQL Queries
Description: Execute SQL queries from the WordPress dashboard.
Version: 1.0
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
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    if (isset($_POST['run_sql_query_nonce']) && wp_verify_nonce($_POST['run_sql_query_nonce'], 'run_sql_query_action')) {
        // Set the character set before running the queries
        $charset_query = "SET NAMES utf8mb4;";
        $charset_result = $wpdb->query($charset_query);

        if ($charset_result !== false) {
            $query = $_POST['sql_query'];

            if (!empty($query)) {
                global $wpdb;
                $result = $wpdb->query($query);

                if ($result !== false) {
                    echo '<div class="updated"><p>Query executed successfully.</p></div>';
                } else {
                    echo '<div class="error"><p>Error executing query: ' . $wpdb->last_error . '</p></div>';
                }
            } else {
                echo '<div class="error"><p>Please enter a valid SQL query.</p></div>';
            }
        } else {
            echo '<div class="error"><p>Error setting character set: ' . $wpdb->last_error . '</p></div>';
        }
    }

    $nonce = wp_create_nonce('run_sql_query_action');
    ?>
    <div class="wrap">
        <h1>Run SQL Queries</h1>
        <form method="post" action="">
            <input type="hidden" name="run_sql_query_nonce" value="<?php echo esc_attr($nonce); ?>">
            <textarea name="sql_query" rows="5" cols="50" placeholder="Enter your SQL query here"></textarea>
            <p><input type="submit" class="button button-primary" value="Run Query"></p>
        </form>
    </div>
    <?php
}

add_action('admin_menu', 'run_sql_queries_menu');
