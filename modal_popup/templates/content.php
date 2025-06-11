<?php
// $node, $props, $children, $builder are available

// Output the main content of the modal for search indexing and fallback.

$content_to_display = '';

if (!empty($props['modal_content_type'])) {
    switch ($props['modal_content_type']) {
        case 'custom':
            if (!empty($props['modal_custom_content'])) {
                // Sanitize or strip tags if needed for plain text representation,
                // but for content.php, raw content is often fine as it's for YOOtheme's systems.
                $content_to_display = $props['modal_custom_content'];
            }
            break;
        case 'article':
            if (!empty($props['modal_article_id'])) {
                $content_to_display = sprintf(esc_html__('[Modal content loaded from article: %s]', 'modal_popup_element'), htmlspecialchars($props['modal_article_id']));
            }
            break;
        case 'widget_area':
            if (!empty($props['modal_widget_area_id'])) {
                $content_to_display = sprintf(esc_html__('[Modal content loaded from widget area: %s]', 'modal_popup_element'), htmlspecialchars($props['modal_widget_area_id']));
            }
            break;
    }
}

// Also consider trigger text if it's relevant
$trigger_text = '';
if (!empty($props['modal_trigger_type'])) {
    if ($props['modal_trigger_type'] === 'button' && !empty($props['modal_trigger_text'])) {
        $trigger_text = $props['modal_trigger_text'];
    } elseif ($props['modal_trigger_type'] === 'link' && !empty($props['modal_trigger_link_text'])) {
        $trigger_text = $props['modal_trigger_link_text'];
    }
}

if (!empty($trigger_text)) {
    echo "<div>" . esc_html__('Trigger:', 'modal_popup_element') . " " . htmlspecialchars($trigger_text) . "</div>\n";
}

if (!empty($content_to_display)) {
    echo "<div>" . esc_html__('Modal Content:', 'modal_popup_element') . "</div>\n";
    echo $content_to_display; // Output the content
} elseif (empty($trigger_text)) {
    // If both trigger and content are empty, maybe a generic placeholder
    echo esc_html__('[Modal Popup Element]', 'modal_popup_element');
}

?>
