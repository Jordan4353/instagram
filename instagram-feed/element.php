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
    JLoader::import('joomla.http.factory'); // For Joomla HTTP client, if needed later
}


return [
    // Define transforms for the element node
    'transforms' => [
        // The function is executed before the template is rendered
        'render' => function ($node, array $params) {
            // $node->props contains element settings
            // We need to populate $node->children with Instagram post data

            $access_token = $node->props['instagram_access_token'] ?? null;
            $source_type = $node->props['instagram_source_type'] ?? 'user';
            $user_id = $node->props['instagram_user_id'] ?? null;
            $hashtag = $node->props['instagram_hashtag'] ?? null;
            $limit = $node->props['instagram_limit'] ?? 9;

            // If no access token, nothing to fetch.
            if (empty($access_token)) {
                // $node->props['_api_error_message'] = 'Instagram Access Token is missing.';
                return false; // Collapsing layout: don't render if no token
            }

            // ** START OF SIMULATED API CALL AND DATA PROCESSING **
            // In a real scenario, you would make an HTTP request to the Instagram API here.
            // Example using WordPress HTTP API (wp_remote_get) or Joomla HTTP Client.

            $api_url = '';
            $query_params = [
                'access_token' => $access_token,
                'limit' => $limit,
            ];

            // This is a simplified simulation. Real API endpoints and parameters differ.
            if ($source_type === 'user' && $user_id) {
                // Example for user posts (Graph API - user_media edge)
                // $api_url = "https://graph.instagram.com/{$user_id}/media";
                // $query_params['fields'] = 'id,media_type,media_url,thumbnail_url,caption,permalink,timestamp,username,children{media_url,media_type}'; // children for carousel
                $node->props['_api_debug_message'] = "Simulating API call for User ID: {$user_id}.";

            } elseif ($source_type === 'hashtag' && $hashtag) {
                // Example for hashtag posts (Graph API - ig_hashtag_search then recent_media edge)
                // This is more complex: first get hashtag ID, then get media.
                // $api_url = "https://graph.instagram.com/ig_hashtag_search?user_id={$user_id}&q={$hashtag}";
                // Then another call for media using the hashtag ID.
                // For simplicity, we'll just use a flag for simulation.
                $node->props['_api_debug_message'] = "Simulating API call for Hashtag: {$hashtag}.";
            } else {
                // $node->props['_api_error_message'] = 'Invalid source type or missing User ID/Hashtag.';
                return false; // Don't render if configuration is incomplete
            }

            // --- SIMULATED API RESPONSE ---
            $simulated_api_response_data = [];
            if ($source_type === 'user') {
                $simulated_api_response_data = [
                    ['id' => '111', 'media_type' => 'IMAGE', 'media_url' => 'https://source.unsplash.com/random/800x600?nature,1', 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?nature,1', 'caption' => 'Beautiful nature scene #sunset #mountains', 'permalink' => '#userpost1', 'timestamp' => '2023-10-26T10:00:00+0000', 'username' => 'naturelover', 'likes_count' => 150, 'comments_count' => 20],
                    ['id' => '222', 'media_type' => 'VIDEO', 'media_url' => 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4', 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?abstract,1', 'caption' => 'Cool video time!', 'permalink' => '#userpost2', 'timestamp' => '2023-10-25T12:30:00+0000', 'username' => 'videofan', 'likes_count' => 200, 'comments_count' => 30],
                    ['id' => '333', 'media_type' => 'IMAGE', 'media_url' => 'https://source.unsplash.com/random/800x600?city,1', 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?city,1', 'caption' => 'City lights.', 'permalink' => '#userpost3', 'timestamp' => '2023-10-24T18:45:00+0000', 'username' => 'cityscape', 'likes_count' => 120], // Missing comments_count
                ];
            } elseif ($source_type === 'hashtag') {
                 $simulated_api_response_data = [
                    ['id' => '777', 'media_type' => 'IMAGE', 'media_url' => 'https://source.unsplash.com/random/800x600?food,1', 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?food,1', 'caption' => 'Delicious #food photography', 'permalink' => '#hashtagpost1', 'timestamp' => '2023-10-26T11:00:00+0000', 'username' => 'foodie', 'likes_count' => 180, 'comments_count' => 25],
                    ['id' => '888', 'media_type' => 'IMAGE', 'media_url' => 'https://source.unsplash.com/random/800x600?travel,1', 'thumbnail_url' => 'https://source.unsplash.com/random/400x300?travel,1', 'caption' => 'Amazing #travel destination', 'permalink' => '#hashtagpost2', 'timestamp' => '2023-10-25T14:15:00+0000', 'username' => 'wanderlust', 'comments_count' => 40], // Missing likes_count
                ];
            }
            // --- END SIMULATED API RESPONSE ---


            // $response = wp_remote_get(add_query_arg($query_params, $api_url));
            // if (is_wp_error($response)) {
            //     $node->props['_api_error_message'] = 'API Request Failed: ' . $response->get_error_message();
            //     return false;
            // }
            // $body = wp_remote_retrieve_body($response);
            // $data = json_decode($body, true);

            // if (empty($data) || isset($data['error']) || !isset($data['data'])) {
            //     $node->props['_api_error_message'] = 'API Error or No Data: ' . ($data['error']['message'] ?? 'Unknown error');
            //     return false; // Don't render if API error or no data
            // }
            // $posts_data = $data['data'];
            // ** END OF SIMULATED API CALL AND DATA PROCESSING **

            $posts_data = array_slice($simulated_api_response_data, 0, $limit); // Use simulated data

            if (empty($posts_data)) {
                // $node->props['_api_error_message'] = 'No posts found for the current settings.';
                return false; // Collapsing layout
            }

            // Clear existing children before populating (important if settings change)
            $node->children = [];

            foreach ($posts_data as $post) {
                $child_node = new stdClass();
                $child_node->type = 'instagram-post-item'; // Name of the child element
                $child_node->props = [];

                // Map API data to child element props
                $child_node->props['post_id'] = $post['id'] ?? null;
                $child_node->props['media_type'] = $post['media_type'] ?? 'IMAGE';
                $child_node->props['media_url'] = $post['media_url'] ?? null;

                // For CAROUSEL_ALBUM, media_url is often not present at the top level.
                // The actual images/videos are in `children` field of the post.
                // For simplicity, we'll use media_url if available, or the first child's media_url.
                if ($child_node->props['media_type'] === 'CAROUSEL_ALBUM' && !empty($post['children']['data'][0]['media_url'])) {
                     // This part is complex. Real API gives children->data[0]->media_url etc.
                     // Our simulation is simpler.
                    // $child_node->props['media_url'] = $post['children']['data'][0]['media_url'];
                    // $child_node->props['thumbnail_url'] = $post['children']['data'][0]['thumbnail_url'] ?? $post['children']['data'][0]['media_url'];
                } else {
                    $child_node->props['thumbnail_url'] = $post['thumbnail_url'] ?? $post['media_url']; // Fallback for images
                }

                $child_node->props['caption'] = $post['caption'] ?? '';
                $child_node->props['permalink'] = $post['permalink'] ?? null;
                $child_node->props['timestamp'] = $post['timestamp'] ?? null;
                $child_node->props['username'] = $post['username'] ?? null;
                $child_node->props['likes_count'] = $post['likes_count'] ?? null;
                $child_node->props['comments_count'] = $post['comments_count'] ?? null;

                // Default status for items, can be overridden by advanced item settings
                $child_node->props['status'] = 'published';


                $node->children[] = $child_node;
            }

            // If after processing, there are no children, don't render.
            if (empty($node->children)) {
                return false;
            }

            // Returning true (or nothing) means the element will be rendered with its template.
            return true;
        },
    ],

    // Define updates for the element node (if needed for future versions)
    // 'updates' => [
    //     '1.0.1' => function ($node, array $params) {
    //         // Example: if a field name changed
    //         // if (isset($node->props['old_field_name'])) {
    //         //     $node->props['new_field_name'] = $node->props['old_field_name'];
    //         //     unset($node->props['old_field_name']);
    //         // }
    //     },
    // ],
];

// After creating this file, update `instagram-feed/element.json`
// to import it by adding:
// "@import": "./element.php",
// at the beginning of the JSON file (before "name": ...).
