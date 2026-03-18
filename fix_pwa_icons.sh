#!/bin/bash
# fix-pwa-icons.sh
# Run from the project root: bash fix-pwa-icons.sh
#
# Fixes "Add to Home Screen" icon by:
#  1. Switching relative favicon/manifest paths to absolute
#  2. Adding iOS PWA meta tags
#  3. Adding theme-color meta tag

set -e

# Define the old and new blocks
OLD_BLOCK='    <link rel="apple-touch-icon" sizes="180x180" href="../img/fav/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/fav/favicon-16x16.png">
    <link rel="manifest" href="../site.webmanifest">
    <link rel="shortcut icon" href="../img/fav/favicon.ico">'

NEW_BLOCK='    <link rel="apple-touch-icon" sizes="180x180" href="/img/fav/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/fav/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="shortcut icon" href="/img/fav/favicon.ico">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Maxmila">
    <meta name="theme-color" content="#0a041a">'

COUNT=0

# Find all .phtml files and apply the replacement
find app/views -name "*.phtml" -type f | while read -r file; do
    if grep -q 'href="../img/fav/apple-touch-icon.png"' "$file"; then
        # Use perl for multi-line replacement (more reliable than sed)
        perl -i -0pe '
            s{    <link rel="apple-touch-icon" sizes="180x180" href="\.\./img/fav/apple-touch-icon\.png">\n    <link rel="icon" type="image/png" sizes="32x32" href="\.\./img/fav/favicon-32x32\.png">\n    <link rel="icon" type="image/png" sizes="16x16" href="\.\./img/fav/favicon-16x16\.png">\n    <link rel="manifest" href="\.\./site\.webmanifest">\n    <link rel="shortcut icon" href="\.\./img/fav/favicon\.ico">}{    <link rel="apple-touch-icon" sizes="180x180" href="/img/fav/apple-touch-icon.png">\n    <link rel="icon" type="image/png" sizes="32x32" href="/img/fav/favicon-32x32.png">\n    <link rel="icon" type="image/png" sizes="16x16" href="/img/fav/favicon-16x16.png">\n    <link rel="manifest" href="/site.webmanifest">\n    <link rel="shortcut icon" href="/img/fav/favicon.ico">\n    <meta name="apple-mobile-web-app-capable" content="yes">\n    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">\n    <meta name="apple-mobile-web-app-title" content="Maxmila">\n    <meta name="theme-color" content="#0a041a">}s
        ' "$file"
        echo "  FIXED: $file"
    else
        echo "  SKIP:  $file (no relative favicon block found)"
    fi
done

echo ""
echo "Done! Now verify the icon files exist at these paths:"
echo "  public/img/fav/apple-touch-icon.png"
echo "  public/img/fav/android-chrome-192x192.png"
echo "  public/img/fav/android-chrome-512x512.png"
echo "  public/img/fav/favicon-32x32.png"
echo "  public/img/fav/favicon-16x16.png"
echo "  public/img/fav/favicon.ico"
echo "  public/site.webmanifest"
echo ""
echo "Also make sure site.webmanifest icon paths point to /img/fav/ (not root)."