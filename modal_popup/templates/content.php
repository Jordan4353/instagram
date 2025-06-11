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
$trigger_info = '';
if (!empty($props['modal_trigger_type'])) {
    $triggerType = $props['modal_trigger_type'];
    if ($triggerType === 'button' && !empty($props['modal_trigger_text'])) {
        $trigger_info = esc_html__('Trigger:', 'modal_popup_element') . ' ' . htmlspecialchars($props['modal_trigger_text']);
    } elseif ($triggerType === 'link' && !empty($props['modal_trigger_link_text'])) {
        $trigger_info = esc_html__('Trigger:', 'modal_popup_element') . ' ' . htmlspecialchars($props['modal_trigger_link_text']);
    } elseif ($triggerType === 'image' && !empty($props['modal_trigger_image'])) {
        // For image trigger, alt text or a generic note could be used if $props['modal_trigger_text'] was also populated for alt for image trigger
        $alt_text = $props['modal_trigger_text'] ?? ''; // Assuming modal_trigger_text might be used for alt for image trigger
        $trigger_info = esc_html__('Trigger: Image', 'modal_popup_element') . ($alt_text ? ' (' . htmlspecialchars($alt_text) . ')' : '');
    } elseif ($triggerType === 'custom_selector' && !empty($props['modal_trigger_custom_selector'])){
        $trigger_info = esc_html__('Trigger: Custom Selector', 'modal_popup_element') . ' (' . htmlspecialchars($props['modal_trigger_custom_selector']) . ')';
    } elseif ($triggerType === 'auto'){
        $delay = $props['modal_auto_open_delay'] ?? 0;
        $trigger_info = sprintf(esc_html__('Trigger: Auto on Page Load (Delay: %s ms)', 'modal_popup_element'), htmlspecialchars($delay));
    } elseif ($triggerType === 'scroll'){
        $scroll_type_info = $props['modal_trigger_scroll_type'] ?? 'amount';
        if ($scroll_type_info === 'amount') {
            $amount = $props['modal_trigger_scroll_amount'] ?? '50';
            $unit = $props['modal_trigger_scroll_amount_unit'] ?? '%';
            $trigger_info = sprintf(esc_html__('Trigger: On Scroll (Amount: %s%s)', 'modal_popup_element'), htmlspecialchars($amount), htmlspecialchars($unit));
        } else {
            $selector = $props['modal_trigger_scroll_element_selector'] ?? '';
            $trigger_info = sprintf(esc_html__('Trigger: On Scroll (Element: %s)', 'modal_popup_element'), htmlspecialchars($selector));
        }
    } elseif ($triggerType === 'exit_intent'){
        $trigger_info = esc_html__('Trigger: On Exit Intent', 'modal_popup_element');
    }
}

if (!empty($trigger_info)) {
    echo "<div>" . $trigger_info . "</div>\n";
}

if (!empty($content_to_display)) {
    echo "<div>" . esc_html__('Modal Content:', 'modal_popup_element') . "</div>\n";
    echo $content_to_display; // Output the content
} elseif (empty($trigger_info)) {
    // Only show generic placeholder if there's absolutely no trigger info AND no content
    echo esc_html__('[Modal Popup Element]', 'modal_popup_element');
}

?>
