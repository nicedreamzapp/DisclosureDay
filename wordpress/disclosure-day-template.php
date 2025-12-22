<?php
/**
 * Template Name: Disclosure Day Campaign
 * Description: Full-width cinematic template for Disclosure Day marketing hub
 *
 * INSTALLATION:
 * 1. Copy this file to your child theme directory
 * 2. In WordPress Admin, edit the Disclosure Day page
 * 3. Select "Disclosure Day Campaign" from Page Attributes > Template
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Disclosure Day - A Steven Spielberg Film starring Emily Blunt. Coming June 12, 2026. The truth has been waiting.">
    <meta name="keywords" content="Disclosure Day, Steven Spielberg, Emily Blunt, UFO movie, 2026, Amblin Entertainment">

    <!-- Open Graph / Social Sharing -->
    <meta property="og:title" content="Disclosure Day - A Steven Spielberg Film">
    <meta property="og:description" content="The truth has been waiting. We weren't ready. Now we have no choice. Coming June 12, 2026.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo get_permalink(); ?>">
    <!-- Add poster image when available -->
    <!-- <meta property="og:image" content="URL_TO_POSTER"> -->

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Disclosure Day - A Steven Spielberg Film">
    <meta name="twitter:description" content="The truth has been waiting. Coming June 12, 2026.">

    <?php wp_head(); ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    <style>
        /* Override Astra theme styles for full-width */
        .ast-separate-container .ast-article-single,
        .ast-separate-container .comments-area,
        .ast-separate-container .ast-author-meta,
        .ast-separate-container .ast-comment-list li {
            background-color: transparent;
            padding: 0;
        }

        .ast-separate-container {
            background-color: #0a0a0f;
        }

        #primary {
            padding: 0;
            margin: 0;
        }

        .site-content {
            padding: 0;
        }

        .entry-content {
            margin: 0;
        }

        /* Hide default page title */
        .entry-title {
            display: none;
        }

        /* Full width override */
        .ast-container {
            max-width: 100%;
            padding: 0;
        }
    </style>
</head>

<body <?php body_class('disclosure-day-page'); ?>>

<?php
// Include the main Disclosure Day HTML content
// You can either include the file or paste the HTML directly

// Option 1: Include external file
$disclosure_day_html = get_stylesheet_directory() . '/disclosure-day-page.html';
if (file_exists($disclosure_day_html)) {
    include($disclosure_day_html);
} else {
    // Option 2: Direct HTML (paste contents of disclosure-day-page.html here)
    ?>
    <!-- PASTE DISCLOSURE-DAY-PAGE.HTML CONTENT HERE IF NOT USING INCLUDE -->
    <div style="padding: 100px; text-align: center; color: #fff; background: #0a0a0f;">
        <h1>Disclosure Day</h1>
        <p>Upload disclosure-day-page.html to your theme folder or paste its contents here.</p>
    </div>
    <?php
}
?>

<?php wp_footer(); ?>

<!-- Custom Analytics (if not using plugin) -->
<script>
// Track page views with custom events
if (typeof gtag === 'function') {
    gtag('event', 'page_view', {
        'page_title': 'Disclosure Day Hub',
        'page_location': window.location.href,
        'page_path': '/disclosure-day/'
    });
}
</script>

</body>
</html>

<?php get_footer(); ?>
