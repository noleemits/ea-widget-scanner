# EA Widget Scanner (CLI)

This WP-CLI plugin scans all public posts and pages in a WordPress site to detect usage of **Essential Addons for Elementor** widgets (identified by `eael-` classes in HTML). It exports the results into a structured CSV file, including:

- Post ID
- Post Title
- Post Type
- Permalink
- EA Widget Classes Found
- Rendered HTML Content (text only)

---

## 📦 Installation

1. Clone or copy this plugin into your WordPress site:
   ```bash
   wp-content/plugins/ea-widget-scanner-cli/
   ```

2. Activate the plugin:
   ```bash
   wp plugin activate ea-widget-scanner-cli
   ```

3. Ensure WP-CLI is installed and running properly in your environment.

---

## 🚀 Usage

### Scan for EA Widgets
```bash
wp ea scan-ea-widgets
```

The command will:
- Scan all published posts of public post types
- Render content using Elementor (if available)
- Parse for any HTML classes starting with `eael-`
- Write results to a CSV file:

```
wp-content/uploads/eael-widget-usage.csv
```

### Delete the CSV File
```bash
wp ea delete-csv
```
This will delete the previously generated CSV file.

---

## 📝 Output Example

The CSV will include rows like:

| Post ID | Title     | Type  | URL                | Widgets Found           | HTML Preview         |
|---------|-----------|-------|---------------------|--------------------------|-----------------------|
| 101     | About Us  | page  | https://...         | eael-adv-accordion       | This is the content... |

---

## 🛠 Development Notes

- The scanner uses `Elementor\Plugin::instance()->frontend->get_builder_content_for_display()` to get accurate widget output.
- Posts with no EA widgets are skipped from the CSV.
- Logs are streamed to the terminal during execution.

---

## 📁 Optional Debug

To inspect the raw HTML of a specific post (e.g. ID 2441), the plugin will write a file:

```
wp-content/ea-debug-2441.html
```

---

## ✅ Requirements
- WordPress with WP-CLI access
- Elementor and Essential Addons installed (optional, but improves results)

---

## 🔒 Disclaimer
This tool reads and parses post content but does not modify any data. Use it for auditing, QA, or optimization workflows.
