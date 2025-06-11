<?php

// Ensure JLoader is available (especially for Joomla context)
if (!class_exists('JLoader')) {
    if (defined('JPATH_LIBRARIES') && file_exists(JPATH_LIBRARIES . '/cms/loader.php')) {
        require_once JPATH_LIBRARIES . '/cms/loader.php';
    } elseif (defined('JPATH_PLATFORM') && file_exists(JPATH_PLATFORM . '/includes/loader.php')) { // Joomla 3.x platform
        require_once JPATH_PLATFORM . '/includes/loader.php';
    }
}
if (class_exists('JLoader')) {
    JLoader::import('joomla.http.factory'); // For Joomla HTTP client
    // JLoader::import('joomla.cache.factory'); // For Joomla Cache
}

// Helper function for simulated API call per account
function simulate_instagram_api_call($account_config_props, $parent_node_props) {
    $source_type = $account_config_props['account_source_type'] ?? 'user';
    $user_id = $account_config_props['account_user_id'] ?? null;
    $hashtag = $account_config_props['account_hashtag'] ?? null;
    // $access_token = $account_config_props['account_access_token'] ?? null; // Used in real API

    // This simulation can return slightly different data per source type for variety
    // It also includes a unique identifier suffix based on user_id or hashtag for testing merging
    $unique_suffix = $source_type === 'user' ? ($user_id ?? 'nouser') : ($hashtag ?? 'nohashtag');

    if ($source_type === 'user' && $user_id) {
        $parent_node_props['_api_debug_messages'][] = "Simulating API for User ID: {$user_id}.";
        return [
            ['id' => 'user_111_' . $unique_suffix, 'media_type' => 'IMAGE', 'media_url' => 'https://source.unsplash.com/random/800x600?nature,'.$user_id, 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?nature,'.$user_id, 'caption' => "User {$user_id} post 1 #nature", 'permalink' => '#userpost1_'.$user_id, 'timestamp' => date('Y-m-d\TH:i:sP', time() - rand(0, 86400*5)), 'username' => 'user_'.$user_id, 'likes_count' => rand(50,200), 'comments_count' => rand(5,50)],
            ['id' => 'user_222_' . $unique_suffix, 'media_type' => 'VIDEO', 'media_url' => 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4', 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?abstract,'.$user_id, 'caption' => "User {$user_id} video post", 'permalink' => '#userpost2_'.$user_id, 'timestamp' => date('Y-m-d\TH:i:sP', time() - rand(0, 86400*10)), 'username' => 'user_'.$user_id, 'likes_count' => rand(50,300), 'comments_count' => rand(10,80)],
        ];
    } elseif ($source_type === 'hashtag' && $hashtag) {
        $parent_node_props['_api_debug_messages'][] = "Simulating API for Hashtag: {$hashtag}.";
        return [
            ['id' => 'hash_777_' . $unique_suffix, 'media_type' => 'IMAGE', 'media_url' => 'https://source.unsplash.com/random/800x600?'.$hashtag.',1', 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?'.$hashtag.',1', 'caption' => "Post with #{$hashtag} and #awesome", 'permalink' => '#hashtagpost1_'.$hashtag, 'timestamp' => date('Y-m-d\TH:i:sP', time() - rand(0, 86400*2)), 'username' => 'hashtagfan_'.$hashtag, 'likes_count' => rand(20,150), 'comments_count' => rand(2,30)],
            // Potentially overlapping ID if same hashtag is added twice, or if a user post also has the hashtag
            ['id' => 'user_111_' . $unique_suffix, 'media_type' => 'IMAGE', 'media_url' => 'https://source.unsplash.com/random/800x600?nature,'.$hashtag, 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?nature,'.$hashtag, 'caption' => "A shared-ID post for #{$hashtag}", 'permalink' => '#userpost1_'.$hashtag, 'timestamp' => date('Y-m-d\TH:i:sP', time() - rand(0, 86400*3)), 'username' => 'sharer_'.$hashtag, 'likes_count' => rand(100,250), 'comments_count' => rand(15,60)],
        ];
    }
    return [];
}


return [
    'transforms' => [
        'render' => function ($node, array $params) {
            $account_configs = $node->props['instagram_accounts'] ?? [];
            $global_limit = $node->props['instagram_limit'] ?? 9;
            $cache_duration_minutes = $node->props['cache_duration'] ?? 60;
            $node->props['_api_debug_messages'] = []; // Initialize array for debug messages

            if (empty($account_configs)) {
                $node->props['_api_error_message'] = 'No Instagram accounts configured.';
                return false;
            }

            $cache_key_parts_for_accounts = [];
            foreach ($account_configs as $config_item_node) {
                if (empty($config_item_node->props) || ($config_item_node->props['status'] ?? 'published') !== 'published') {
                    continue; // Skip disabled account configurations
                }
                $config_props = $config_item_node->props;
                $acc_source_type = $config_props['account_source_type'] ?? 'user';
                $acc_user_id = $config_props['account_user_id'] ?? null;
                $acc_hashtag = $config_props['account_hashtag'] ?? null;
                $acc_token_present = !empty($config_props['account_access_token']); // Don't include actual token in key
                $cache_key_parts_for_accounts[] = $acc_source_type . '_' . ($acc_source_type === 'user' ? $acc_user_id : $acc_hashtag) . '_' . ($acc_token_present ? 'tokenyes' : 'notoken');
            }
            sort($cache_key_parts_for_accounts);
            $accounts_hash = md5(implode('|', $cache_key_parts_for_accounts));

            $transient_key_parts = [
                'instagram_feed_multi',
                $accounts_hash,
                $global_limit
            ];
            $transient_key = 'ytp_if_' . md5(implode('_', array_filter($transient_key_parts)));

            $posts_data = null;
            $loaded_from_cache = false;

            if ($cache_duration_minutes > 0) {
                if (function_exists('get_transient')) {
                    $cached_data = get_transient($transient_key);
                    if (false !== $cached_data) {
                        $posts_data = $cached_data;
                        $node->props['_api_debug_messages'][] = 'Loaded combined feed from WordPress cache.';
                        $loaded_from_cache = true;
                    }
                }
                // Joomla cache loading placeholder
            }

            if (!$loaded_from_cache) {
                $all_posts_data = [];
                $active_sources_count = 0;

                foreach ($account_configs as $config_item_node) {
                    if (empty($config_item_node->props) || ($config_item_node->props['status'] ?? 'published') !== 'published') {
                        $node->props['_api_debug_messages'][] = 'Skipping disabled source: ' . ($config_item_node->props['account_label'] ?? 'N/A');
                        continue;
                    }
                    $config_props = $config_item_node->props;
                    $account_access_token = $config_props['account_access_token'] ?? null;

                    if (empty($account_access_token)) {
                        $node->props['_api_debug_messages'][] = 'Skipping source due to missing Access Token: ' . ($config_props['account_label'] ?? 'N/A');
                        continue;
                    }
                    $active_sources_count++;

                    // Pass $node->props by reference to allow simulate_instagram_api_call to add debug messages
                    $source_posts = simulate_instagram_api_call($config_props, $node->props);
                    if (!empty($source_posts)) {
                        $all_posts_data = array_merge($all_posts_data, $source_posts);
                    }
                }

                if ($active_sources_count === 0 && empty($all_posts_data)) {
                     $node->props['_api_error_message'] = 'No active Instagram sources with Access Tokens configured.';
                     return false; // Collapse if no valid sources to fetch from
                }

                // Deduplicate posts by ID
                $unique_posts = [];
                if (!empty($all_posts_data)) {
                    foreach ($all_posts_data as $post) {
                        if (isset($post['id'])) {
                            if (!isset($unique_posts[$post['id']])) { // Keep first encountered
                                $unique_posts[$post['id']] = $post;
                            }
                        } else {
                            $unique_posts[] = $post; // Should not happen with Instagram
                        }
                    }
                    $all_posts_data = array_values($unique_posts);

                    // Sort by timestamp (newest first)
                    usort($all_posts_data, function ($a, $b) {
                        $timestamp_a = $a['timestamp'] ?? 0;
                        $timestamp_b = $b['timestamp'] ?? 0;
                        return strtotime($timestamp_b) - strtotime($timestamp_a);
                    });
                }

                // Apply global limit
                $posts_data = array_slice($all_posts_data, 0, $global_limit);

                if ($cache_duration_minutes > 0 && !empty($posts_data)) {
                    if (function_exists('set_transient')) {
                        set_transient($transient_key, $posts_data, $cache_duration_minutes * 60);
                        $node->props['_api_debug_messages'][] = 'Saved combined feed to WordPress cache.';
                    }
                    // Joomla cache storing placeholder
                }
            }

            if (empty($posts_data)) {
                $node->props['_api_error_message'] = $node->props['_api_error_message'] ?? 'No posts found for the current settings or from cache.';
                return false;
            }

            $node->children = [];
            foreach ($posts_data as $post) {
                $child_node = new stdClass();
                $child_node->type = 'instagram-post-item';
                $child_node->props = [];
                $child_node->props['post_id'] = $post['id'] ?? null;
                $child_node->props['media_type'] = $post['media_type'] ?? 'IMAGE';
                $child_node->props['media_url'] = $post['media_url'] ?? null;
                $child_node->props['thumbnail_url'] = $post['thumbnail_url'] ?? $post['media_url'];
                $child_node->props['caption'] = $post['caption'] ?? '';
                $child_node->props['permalink'] = $post['permalink'] ?? null;
                $child_node->props['timestamp'] = $post['timestamp'] ?? null;
                $child_node->props['username'] = $post['username'] ?? null;
                $child_node->props['likes_count'] = $post['likes_count'] ?? null;
                $child_node->props['comments_count'] = $post['comments_count'] ?? null;
                $child_node->props['status'] = 'published';
                $node->children[] = $child_node;
            }

            if (empty($node->children)) {
                 $node->props['_api_error_message'] = $node->props['_api_error_message'] ?? 'No children to render after processing.';
                return false;
            }

            return true;
        },
    ],
];
