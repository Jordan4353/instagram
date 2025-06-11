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
            $triggerType = $node->props['modal_trigger_type'] ?? null;

            if (empty($triggerType)) {
                return false; // No trigger type defined
            }

            // Content checks for triggers that open automatically (auto, scroll, exit_intent)
            if (in_array($triggerType, ['auto', 'scroll', 'exit_intent'])) {
                if (empty($node->props['modal_content_type'])) {
                    return false;
                }
                $contentType = $node->props['modal_content_type'];
                if ($contentType === 'custom' && empty($node->props['modal_custom_content'])) {
                    // Allow if content is dynamic and mapped
                    if (empty($params['builder']->getProps('source')['modal_custom_content'])) {
                        return false;
                    }
                }
                if ($contentType === 'article' && empty($node->props['modal_article_id'])) {
                    if (empty($params['builder']->getProps('source')['modal_article_id'])) {
                        return false;
                    }
                }
                if ($contentType === 'widget_area' && empty($node->props['modal_widget_area_id'])) {
                    if (empty($params['builder']->getProps('source')['modal_widget_area_id'])) {
                        return false;
                    }
                }
            }

            // Specific checks for trigger configurations
            switch ($triggerType) {
                case 'button':
                    if (empty($node->props['modal_trigger_text']) && empty($params['builder']->getProps('source')['modal_trigger_text'])) {
                        return false;
                    }
                    break;
                case 'image':
                    if (empty($node->props['modal_trigger_image']) && empty($params['builder']->getProps('source')['modal_trigger_image'])) {
                        return false;
                    }
                    break;
                case 'link':
                    if (empty($node->props['modal_trigger_link_text']) && empty($params['builder']->getProps('source')['modal_trigger_link_text'])) {
                        return false;
                    }
                    break;
                case 'custom_selector':
                    if (empty($node->props['modal_trigger_custom_selector'])) {
                        return false;
                    }
                    break;
                case 'scroll':
                    $scrollType = $node->props['modal_trigger_scroll_type'] ?? null;
                    if ($scrollType === 'amount') {
                        if (!isset($node->props['modal_trigger_scroll_amount']) || $node->props['modal_trigger_scroll_amount'] === '') {
                             // Amount can be 0, so check if it's set at all for non-dynamic cases
                             // If dynamic source is possible for scroll_amount, that check would be here too.
                             // For now, assuming scroll_amount is not dynamically sourced itself.
                            return false;
                        }
                    } elseif ($scrollType === 'element') {
                        if (empty($node->props['modal_trigger_scroll_element_selector'])) {
                            return false;
                        }
                    } else {
                        return false; // Invalid scroll type
                    }
                    break;
                case 'auto': // Auto on page load
                    // The content check at the beginning handles 'auto' already.
                    // modal_auto_open_delay can be 0, so no specific check on its value for collapsing here.
                    break;
                case 'exit_intent':
                    // The content check at the beginning handles 'exit_intent' already.
                    break;
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
