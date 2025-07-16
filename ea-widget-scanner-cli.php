<?php

/**
 * Plugin Name: EA Widget Scanner (CLI)
 * Description: Scans all posts for Essential Addons widgets and exports a CSV via WP-CLI.
 * Version: 1.4.2
 * Author: Stephen Lee Hernandez
 */

if (defined('WP_CLI') && WP_CLI) {
    class EA_Widget_Scanner_CLI {
        const FILE_NAME = 'eael-widget-usage.csv';

        /**
         * Scans all public posts for EA widgets and writes a CSV.
         *
         * ## EXAMPLES
         *
         *     wp ea scan-ea-widgets
         *
         * @when after_wp_load
         */
        public function scan() {
            $upload_dir = wp_upload_dir();
            $file_path = trailingslashit($upload_dir['basedir']) . self::FILE_NAME;

            $fh = fopen($file_path, 'w');
            if (!$fh) {
                WP_CLI::error("Failed to open file: $file_path");
            }

            fputcsv($fh, ['Post ID', 'Post Title', 'Post Type', 'URL', 'Widgets Found']);

            $post_types = get_post_types(['public' => true], 'names');
            $paged = 1;
            $per_page = 20;
            $total = 0;

            do {
                $query = new WP_Query([
                    'post_type' => $post_types,
                    'post_status' => 'publish',
                    'posts_per_page' => $per_page,
                    'paged' => $paged,
                    'no_found_rows' => true,
                    'fields' => 'all',
                ]);

                if (!$query->have_posts()) break;

                foreach ($query->posts as $post) {
                    $total++;
                    WP_CLI::log("🔍 Scanning post ID: {$post->ID}");

                    // Use Elementor renderer if available
                    if (class_exists('Elementor\Plugin')) {
                        $html = Elementor\Plugin::instance()->frontend->get_builder_content_for_display($post->ID);
                    } else {
                        $html = apply_filters('the_content', $post->post_content);
                    }

                    if (empty(trim($html))) continue;

                    // Dump HTML for inspection if this is post 2441
                    if ((int) $post->ID === 2441) {
                        file_put_contents(WP_CONTENT_DIR . '/ea-debug-2441.html', $html);
                        WP_CLI::log("📝 Dumped HTML for post 2441 to wp-content/ea-debug-2441.html");
                    }

                    $dom = new DOMDocument();
                    libxml_use_internal_errors(true);
                    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
                    libxml_clear_errors();

                    $xpath = new DOMXPath($dom);
                    $elements = $xpath->query('//*[contains(@class, "eael-")]');
                    $classes_found = [];

                    foreach ($elements as $el) {
                        $classes = explode(' ', $el->getAttribute('class'));
                        foreach ($classes as $cls) {
                            if (strpos($cls, 'eael-') === 0) {
                                $classes_found[] = $cls;
                            }
                        }
                    }

                    if (!empty($classes_found)) {
                        $success = fputcsv($fh, [
                            $post->ID,
                            $post->post_title,
                            $post->post_type,
                            get_permalink($post),
                            implode(', ', array_unique($classes_found))
                        ]);
                        if (!$success) {
                            WP_CLI::warning("⚠️ Failed to write CSV row for post ID {$post->ID}");
                        }
                    }
                }

                wp_reset_postdata();
                gc_collect_cycles();
                usleep(500000); // 0.5s pause to ease memory pressure

                $paged++;
            } while ($query->post_count > 0);

            fclose($fh);

            WP_CLI::success("✅ Scan complete. CSV created at: $file_path");
        }

        /**
         * Deletes the generated CSV file.
         *
         * ## EXAMPLES
         *
         *     wp ea delete-csv
         *
         * @when after_wp_load
         */
        public function delete_csv() {
            $upload_dir = wp_upload_dir();
            $file_path = trailingslashit($upload_dir['basedir']) . self::FILE_NAME;

            if (file_exists($file_path)) {
                unlink($file_path);
                WP_CLI::success("🗑️ Deleted: $file_path");
            } else {
                WP_CLI::warning("File not found: $file_path");
            }
        }
    }

    WP_CLI::add_command('ea scan-ea-widgets', [new EA_Widget_Scanner_CLI(), 'scan']);
    WP_CLI::add_command('ea delete-csv', [new EA_Widget_Scanner_CLI(), 'delete_csv']);
}

// Load admin UI if in WP dashboard
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/admin-ui.php';
}
