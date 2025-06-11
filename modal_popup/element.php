<?php

return [

    // Element configuration
    'config' => [
        // ... you can add configuration specific to this element if needed
    ],

    // Define transforms for the "element" node
    'transforms' => [

        // The function is executed before the template is rendered
        'render' => function ($node, array $params) {
            // - Don't render the element if essential content is missing.
            // - Prepare data for the template (e.g., fetch dynamic content).

            $triggerType = $node->props['modal_trigger_type'] ?? null;

            if (empty($triggerType)) {
                return false; // No trigger type defined
            }

            // Content checks for triggers that open automatically (auto, scroll, exit_intent)
            // These triggers require the modal to have some content to display.
            if (in_array($triggerType, ['auto', 'scroll', 'exit_intent'])) {
                $contentType = $node->props['modal_content_type'] ?? null;
                if (empty($contentType)) {
                    return false;
                }
                if ($contentType === 'custom' && empty($node->props['modal_custom_content'])) {
                    return false;
                }
                if ($contentType === 'article' && empty($node->props['modal_article_id'])) {
                    return false;
                }
                if ($contentType === 'widget_area' && empty($node->props['modal_widget_area_id'])) {
                    return false;
                }
            }

            // Specific checks for trigger configurations if the trigger itself needs content
            switch ($triggerType) {
                case 'button':
                    if (empty($node->props['modal_trigger_text'])) {
                        return false;
                    }
                    break;
                case 'image':
                    if (empty($node->props['modal_trigger_image'])) {
                        return false;
                    }
                    break;
                case 'link':
                    if (empty($node->props['modal_trigger_link_text'])) {
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
                // No specific additional collapsing logic for 'auto' or 'exit_intent' beyond the content check above.
            }

            // Note: ID generation is handled in template.php with a fallback if empty.

            return true; // Return true to render
        },

    ],

    // Define updates for the element node (for future compatibility)
    'updates' => [
        // Example: '1.0.1' => function ($node, array $params) { ... }
    ],

];
