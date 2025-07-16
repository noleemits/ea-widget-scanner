# EA Widget Scanner (CLI)

Scans all public posts, pages, and custom post types for [Essential Addons for Elementor](https://essential-addons.com/elementor/) widgets. It logs usage into a CSV file using WP-CLI.

## 🔧 Installation
1. Upload this plugin to your `wp-content/plugins/` directory.
2. Activate it via the WordPress Admin Dashboard.
3. Make sure WP-CLI is installed and running properly.

## ⚙️ Usage

### 📊 Scan for EA Widgets
```bash
wp ea scan-ea-widgets
```
Scans all posts and outputs a CSV file at:
```
wp-content/uploads/eael-widget-usage.csv
```

### 🧹 Delete the CSV File
```bash
wp ea delete-csv
```
Removes the CSV file if it exists.

## 🖥️ Admin UI
You can view usage instructions and plugin status by going to:
**WP Admin → Tools → EA Widget Scanner CLI**

## 🧪 Frontend Debugging Tools

### 🔲 Highlight EA Widgets in the Browser
Paste this in your browser DevTools **Console** or as a CSS Snippet (e.g. in DevTools or Customizer):
```css
[class^="eael"] {
  border: 2px solid red !important;
}

[class*="eael"] {
  border: 2px solid red !important;
}
```

### 🔍 Log Widget Usage to Console
Paste this into your browser's **DevTools Console** to list all EA widgets present:
```js
(() => {
  const selectors = [
    '[class*="eael-"]',
    '[data-widget_type^="eael-"]'
  ];

  const widgets = new Map();

  selectors.forEach(selector => {
    document.querySelectorAll(selector).forEach(el => {
      const classes = Array.from(el.classList).filter(cls => cls.startsWith('eael-'));
      classes.forEach(cls => {
        const existing = widgets.get(cls) || [];
        existing.push(el);
        widgets.set(cls, existing);
      });
    });
  });

  if (widgets.size === 0) {
    console.log('No Essential Addons widgets detected on this page.');
  } else {
    console.log('📦 Essential Addons widgets found on this page:\n');
    widgets.forEach((els, cls) => {
      console.log(`- ${cls} (${els.length} time${els.length > 1 ? 's' : ''})`);
    });
  }
})();
```

## 📁 Output Format
| Post ID | Title | Post Type | URL | EA Widgets |
|---------|-------|-----------|-----|-------------|

## 🛡️ Security
- Includes `index.php` files to block directory access
- Does not expose or modify content; read-only scanning

---
Created with ❤️ by Stephen Lee Hernandez
