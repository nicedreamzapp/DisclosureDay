<?php
/**
 * Disclosure Day - Live Updates REST API
 *
 * Add this code to your theme's functions.php OR create a simple plugin.
 *
 * INSTALLATION:
 * Option A: Paste into your child theme's functions.php
 * Option B: Create wp-content/plugins/disclosure-day-updates/disclosure-day-updates.php
 *           and add plugin header, then activate in WP Admin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the REST API endpoint
 * Endpoint: /wp-json/disclosure-day/v1/updates
 */
add_action('rest_api_init', function() {
    register_rest_route('disclosure-day/v1', '/updates', array(
        'methods'  => 'GET',
        'callback' => 'dd_get_live_updates',
        'permission_callback' => '__return_true', // Public endpoint
    ));
});

/**
 * Fetch and return curated updates
 */
function dd_get_live_updates(WP_REST_Request $request) {
    // Check cache first (5 minute cache)
    $cached = get_transient('dd_live_updates');
    if ($cached !== false) {
        return new WP_REST_Response($cached, 200);
    }

    $updates = array();

    // 1. Fetch from Google News RSS (no API key needed)
    $google_news = dd_fetch_google_news();
    $updates = array_merge($updates, $google_news);

    // 2. Add any manual/curated updates from WordPress
    $manual_updates = dd_get_manual_updates();
    $updates = array_merge($updates, $manual_updates);

    // 3. Sort by date (newest first)
    usort($updates, function($a, $b) {
        return strtotime($b['date'] ?? 'now') - strtotime($a['date'] ?? 'now');
    });

    // Limit to 10 most recent
    $updates = array_slice($updates, 0, 10);

    $response = array(
        'items' => $updates,
        'updated' => current_time('c'),
        'count' => count($updates)
    );

    // Cache for 5 minutes
    set_transient('dd_live_updates', $response, 5 * MINUTE_IN_SECONDS);

    return new WP_REST_Response($response, 200);
}

/**
 * Fetch news from Google News RSS
 */
function dd_fetch_google_news() {
    $items = array();

    // Google News RSS for "Disclosure Day Spielberg"
    $rss_url = 'https://news.google.com/rss/search?q=Disclosure+Day+Spielberg+movie&hl=en-US&gl=US&ceid=US:en';

    $response = wp_remote_get($rss_url, array(
        'timeout' => 10,
        'user-agent' => 'Mozilla/5.0 (compatible; DisclosureDayBot/1.0)'
    ));

    if (is_wp_error($response)) {
        return $items;
    }

    $body = wp_remote_retrieve_body($response);

    // Parse XML
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($body);

    if ($xml === false) {
        return $items;
    }

    // Extract items
    if (isset($xml->channel->item)) {
        $count = 0;
        foreach ($xml->channel->item as $item) {
            if ($count >= 5) break; // Limit to 5 from Google News

            $title = (string) $item->title;
            $link = (string) $item->link;
            $pubDate = (string) $item->pubDate;
            $source = (string) $item->source;

            // Clean up title (remove source suffix like " - Variety")
            $title = preg_replace('/\s*-\s*[^-]+$/', '', $title);

            $items[] = array(
                'title' => sanitize_text_field($title),
                'url' => esc_url($link),
                'source' => sanitize_text_field($source) ?: 'News',
                'date' => $pubDate,
                'when' => dd_time_ago($pubDate),
                'verified' => false,
                'type' => 'news'
            );

            $count++;
        }
    }

    return $items;
}

/**
 * Get manually curated updates (stored as WP options or custom post type)
 */
function dd_get_manual_updates() {
    $items = array();

    // Option 1: From WordPress options (simple)
    $manual = get_option('dd_manual_updates', array());

    if (!empty($manual) && is_array($manual)) {
        foreach ($manual as $update) {
            $items[] = array(
                'title' => sanitize_text_field($update['title'] ?? ''),
                'url' => esc_url($update['url'] ?? '#'),
                'source' => sanitize_text_field($update['source'] ?? 'Official'),
                'date' => $update['date'] ?? current_time('c'),
                'when' => dd_time_ago($update['date'] ?? ''),
                'verified' => !empty($update['verified']),
                'type' => 'manual'
            );
        }
    }

    // Always include these verified official sources
    $official = array(
        array(
            'title' => 'Official Disclosure Day site launched',
            'url' => 'https://www.disclosuredaymovie.com/',
            'source' => 'Official',
            'date' => '2025-12-16',
            'when' => dd_time_ago('2025-12-16'),
            'verified' => true,
            'type' => 'official'
        ),
        array(
            'title' => 'Official teaser trailer released',
            'url' => 'https://www.youtube.com/watch?v=UFe6NRgoXCM',
            'source' => 'YouTube',
            'date' => '2025-12-16',
            'when' => dd_time_ago('2025-12-16'),
            'verified' => true,
            'type' => 'official'
        )
    );

    return array_merge($official, $items);
}

/**
 * Convert date to "time ago" format
 */
function dd_time_ago($datetime) {
    if (empty($datetime)) {
        return '';
    }

    $time = strtotime($datetime);
    $now = current_time('timestamp');
    $diff = $now - $time;

    if ($diff < 0) {
        return 'upcoming';
    }

    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $time);
    }
}

/**
 * Admin page to manage manual updates (optional)
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'options-general.php',
        'Disclosure Day Updates',
        'DD Updates',
        'manage_options',
        'dd-updates',
        'dd_admin_page'
    );
});

function dd_admin_page() {
    // Handle form submission
    if (isset($_POST['dd_save_update']) && check_admin_referer('dd_save_update_nonce')) {
        $updates = get_option('dd_manual_updates', array());

        $new_update = array(
            'title' => sanitize_text_field($_POST['dd_title'] ?? ''),
            'url' => esc_url_raw($_POST['dd_url'] ?? ''),
            'source' => sanitize_text_field($_POST['dd_source'] ?? 'Update'),
            'date' => current_time('c'),
            'verified' => !empty($_POST['dd_verified'])
        );

        if (!empty($new_update['title'])) {
            array_unshift($updates, $new_update);
            $updates = array_slice($updates, 0, 20); // Keep only 20 manual updates
            update_option('dd_manual_updates', $updates);

            // Clear cache
            delete_transient('dd_live_updates');

            echo '<div class="notice notice-success"><p>Update added!</p></div>';
        }
    }

    // Handle delete
    if (isset($_GET['dd_delete']) && check_admin_referer('dd_delete_nonce')) {
        $index = intval($_GET['dd_delete']);
        $updates = get_option('dd_manual_updates', array());
        if (isset($updates[$index])) {
            unset($updates[$index]);
            $updates = array_values($updates);
            update_option('dd_manual_updates', $updates);
            delete_transient('dd_live_updates');
            echo '<div class="notice notice-success"><p>Update deleted!</p></div>';
        }
    }

    $updates = get_option('dd_manual_updates', array());
    ?>
    <div class="wrap">
        <h1>Disclosure Day - Manual Updates</h1>
        <p>Add curated updates that appear in your Live Updates feed alongside auto-fetched news.</p>

        <h2>Add New Update</h2>
        <form method="post">
            <?php wp_nonce_field('dd_save_update_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="dd_title">Title</label></th>
                    <td><input type="text" id="dd_title" name="dd_title" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="dd_url">URL</label></th>
                    <td><input type="url" id="dd_url" name="dd_url" class="regular-text" placeholder="https://..."></td>
                </tr>
                <tr>
                    <th><label for="dd_source">Source</label></th>
                    <td><input type="text" id="dd_source" name="dd_source" class="regular-text" placeholder="Variety, Official, etc."></td>
                </tr>
                <tr>
                    <th><label for="dd_verified">Verified</label></th>
                    <td><label><input type="checkbox" id="dd_verified" name="dd_verified" value="1"> Mark as verified (green checkmark)</label></td>
                </tr>
            </table>
            <p><button type="submit" name="dd_save_update" class="button button-primary">Add Update</button></p>
        </form>

        <h2>Current Manual Updates</h2>
        <?php if (empty($updates)) : ?>
            <p>No manual updates yet. Auto-fetched news will still appear.</p>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Source</th>
                        <th>Verified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($updates as $i => $update) : ?>
                        <tr>
                            <td><a href="<?php echo esc_url($update['url']); ?>" target="_blank"><?php echo esc_html($update['title']); ?></a></td>
                            <td><?php echo esc_html($update['source']); ?></td>
                            <td><?php echo $update['verified'] ? '✓' : '—'; ?></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(admin_url('options-general.php?page=dd-updates&dd_delete=' . $i), 'dd_delete_nonce'); ?>"
                                   onclick="return confirm('Delete this update?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2>API Endpoint</h2>
        <p>Your live updates are available at:</p>
        <code><?php echo home_url('/wp-json/disclosure-day/v1/updates'); ?></code>
        <p style="margin-top:10px;">
            <a href="<?php echo home_url('/wp-json/disclosure-day/v1/updates'); ?>" target="_blank" class="button">Test Endpoint</a>
            <button type="button" class="button" onclick="fetch('<?php echo admin_url('admin-post.php?action=dd_clear_cache'); ?>').then(()=>alert('Cache cleared!'));">Clear Cache</button>
        </p>
    </div>
    <?php
}

// Cache clear action
add_action('admin_post_dd_clear_cache', function() {
    delete_transient('dd_live_updates');
    wp_redirect(admin_url('options-general.php?page=dd-updates'));
    exit;
});
