<?php

return [

    // Element configuration
    'config' => [
        // ... you can add configuration specific to this element if needed
    ],

    // Define transforms for the element node
    'transforms' => [

        // The function is executed before the template is rendered
        'render' => function ($node, array $params) {
            // - Don't render the element if essential content is missing.
            // - Prepare data for the template (e.g., fetch dynamic content).

            // Collapsing layout: Check trigger and content
            if (empty($node->props['modal_trigger_type'])) {
                return false; // No trigger defined
            }

            // If trigger is auto-open, content must exist
            if ($node->props['modal_trigger_type'] === 'auto') {
                if (empty($node->props['modal_content_type'])) {
                    return false;
                }
                if ($node->props['modal_content_type'] === 'custom' && empty($node->props['modal_custom_content'])) {
                    return false;
                }
                if ($node->props['modal_content_type'] === 'article' && empty($node->props['modal_article_id'])) {
                    return false;
                }
                if ($node->props['modal_content_type'] === 'widget_area' && empty($node->props['modal_widget_area_id'])) {
                    return false;
                }
            }
            // If trigger is button, text must exist
            if ($node->props['modal_trigger_type'] === 'button' && empty($node->props['modal_trigger_text'])){
                // Allow if content is dynamic and mapped
                 if (empty($params['builder']->getProps('source')['modal_trigger_text'])) {
                    return false;
                 }
            }
            // If trigger is image, image must exist
            if ($node->props['modal_trigger_type'] === 'image' && empty($node->props['modal_trigger_image'])){
                 if (empty($params['builder']->getProps('source')['modal_trigger_image'])) {
                    return false;
                 }
            }
            // If trigger is link, link text must exist
            if ($node->props['modal_trigger_type'] === 'link' && empty($node->props['modal_trigger_link_text'])){
                 if (empty($params['builder']->getProps('source')['modal_trigger_link_text'])) {
                    return false;
                 }
            }
            // If trigger is custom selector, selector must exist
            if ($node->props['modal_trigger_type'] === 'custom_selector' && empty($node->props['modal_trigger_custom_selector'])){
                return false;
            }

            // Fetch dynamic content if applicable
            if (!empty($node->props['modal_content_type'])) {
                if ($node->props['modal_content_type'] === 'article' && !empty($node->props['modal_article_id'])) {
                    // WordPress: Fetch article content
                    // The actual fetching will be done by YOOtheme Pro's dynamic content system
                    // if the field is mapped. If not mapped, we might need to fetch it manually.
                    // For now, assume YOOtheme handles mapped dynamic sources.
                    // If direct (non-mapped) fetching is needed, it would be here:
                    // $article_id_or_slug = $node->props['modal_article_id'];
                    // $post = get_post(is_numeric($article_id_or_slug) ? intval($article_id_or_slug) : null, OBJECT, is_string($article_id_or_slug) ? $article_id_or_slug : null);
                    // if ($post) {
                    //     $node->props['fetched_article_content'] = apply_filters('the_content', $post->post_content);
                    // } else {
                    //     $node->props['fetched_article_content'] = 'Article not found.';
                    // }
                } elseif ($node->props['modal_content_type'] === 'widget_area' && !empty($node->props['modal_widget_area_id'])) {
                    // WordPress: Fetch widget area content
                    // Similar to articles, YOOtheme Pro handles mapped dynamic sources.
                    // For direct fetching:
                    // ob_start();
                    // dynamic_sidebar($node->props['modal_widget_area_id']);
                    // $node->props['fetched_widget_area_content'] = ob_get_clean();
                    // if (empty($node->props['fetched_widget_area_content'])) {
                    //      $node->props['fetched_widget_area_content'] = 'Widget area not found or empty.';
                    // }
                }
            }

            // Generate a unique ID for the modal if not set
            if (empty($node->props['id'])) {
                $node->props['id'] = 'modal-popup-' . uniqid();
            }


            return $node;
        },

    ],

    // Define updates for the element node (for future compatibility)
    'updates' => [
        // Example: '1.0.1' => function ($node, array $params) { ... }
    ],

];

?>
