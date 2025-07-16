<?php
add_action('admin_menu', function () {
    add_management_page(
        'EA Widget Scanner CLI',
        'EA Widget Scanner CLI',
        'manage_options',
        'ea-widget-scanner-cli-info',
        function () {
            echo '<div class="wrap"><h1>EA Widget Scanner CLI</h1>';
            echo '<p>This tool provides WP-CLI commands to scan all posts and detect Essential Addons for Elementor widgets.</p>';
            echo '<pre><code>';
            echo "Run scan: wp ea scan-ea-widgets\n";
            echo "Delete CSV: wp ea delete-csv\n";
            echo "CSV Output: wp-content/uploads/eael-widget-usage.csv";
            echo '</code></pre></div>';
        }
    );
});
