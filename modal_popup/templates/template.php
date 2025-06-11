<?php
// $node, $props, $children, $builder are available

// --- Props ---
$modal_id = $props['id'] ?: 'modal-popup-' . $this->uid(); // Ensure unique ID

// Trigger specific props
$trigger_type = $props['modal_trigger_type'];
$trigger_text = $props['modal_trigger_text'];
$trigger_image_src = $props['modal_trigger_image'];
$trigger_link_text = $props['modal_trigger_link_text'];
$trigger_custom_selector = $props['modal_trigger_custom_selector'];

// Content specific props
$content_type = $props['modal_content_type'];
$custom_content = $props['modal_custom_content'];
$article_id = $props['modal_article_id'];
$widget_area_id = $props['modal_widget_area_id'];

// Settings props
$modal_width = $props['modal_width'];
$modal_height = $props['modal_height'];
$modal_padding = $props['modal_padding'];
$modal_border_radius = $props['modal_border_radius'];
$modal_overlay_bg = $props['modal_overlay_background_color'];
$modal_bg = $props['modal_background_color'];
$close_button_type = $props['modal_close_button'];
$close_button_text = $props['modal_close_button_text'] ?: esc_html__('Close', 'modal_popup_element');
$close_button_pos = $props['modal_close_button_position'];
$animation_entrance = $props['modal_animation_entrance'];
$animation_exit = $props['modal_animation_exit']; // Not directly used by uk-modal, but can be for custom JS
$auto_open_delay = $props['modal_auto_open_delay'];
$auto_close_delay = $props['modal_auto_close_delay'];
$z_index = $props['modal_z_index'];
$disable_body_scroll = $props['modal_disable_body_scroll'];

// --- Prepare Classes and Styles ---

// Modal classes
$modal_attrs = ['id' => $modal_id, 'uk-modal' => true];
if ($disable_body_scroll) {
    $modal_attrs['uk-modal'] = 'bg-close: false;'; // Keep modal open on background click if body scroll is disabled for better UX
    // Add class to prevent body scroll, handled by UIkit or custom JS if needed
    // UIkit 3 modal handles body scroll by default. 'bg-close: false' means clicking overlay won't close.
}

// Modal Dialog classes
$dialog_classes = ['uk-modal-dialog'];
$dialog_styles = [];
if ($modal_width && $modal_width !== 'auto') {
    $dialog_styles[] = "width: {$modal_width};";
}
if ($modal_height && $modal_height !== 'auto') {
    $dialog_styles[] = "height: {$modal_height};";
    $dialog_classes[] = 'uk-flex uk-flex-column'; // for handling height and scrollable body
}
if ($modal_padding) {
    $dialog_styles[] = "padding: {$modal_padding};";
}
if ($modal_border_radius) {
    $dialog_styles[] = "border-radius: {$modal_border_radius};";
}
if ($modal_bg) {
    $dialog_styles[] = "background-color: {$modal_bg};";
}

// --- Trigger Element ---
$trigger_attrs = ['uk-toggle' => "target: #{$modal_id}"];

if ($trigger_type === 'button') :
?>
    <button class="uk-button uk-button-default" <?= $this->attrs($trigger_attrs) ?> type="button">
        <?= htmlspecialchars($trigger_text) ?>
    </button>
<?php elseif ($trigger_type === 'image' && $trigger_image_src) :
    $img_attrs = ['src' => $trigger_image_src, 'alt' => $trigger_text ?: 'Open Modal', 'uk-img' => true];
?>
    <img <?= $this->attrs($trigger_attrs, $img_attrs) ?> style="cursor: pointer;" />
<?php elseif ($trigger_type === 'link') :
?>
    <a <?= $this->attrs($trigger_attrs) ?> href="#" onclick="return false;">
        <?= htmlspecialchars($trigger_link_text) ?>
    </a>
<?php elseif ($trigger_type === 'custom_selector' && $trigger_custom_selector) :
    // Trigger is handled by custom JS targeting the selector and this modal ID
    // We might add a script here to initialize it, or assume global JS handles it.
    // For now, no visual output for the trigger itself, it's external.
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const triggerElement = document.querySelector('<?= addslashes($trigger_custom_selector) ?>');
            if (triggerElement) {
                triggerElement.setAttribute('uk-toggle', 'target: #<?= $modal_id ?>');
            }
        });
    </script>
<?php endif;

// --- Modal HTML ---
?>
<div <?= $this->attrs($modal_attrs) ?> <?php if ($z_index) { echo "style='z-index: {$z_index};'"; } ?> >
    <div class="<?= implode(' ', $dialog_classes) ?>" <?php if ($dialog_styles) { echo "style='" . implode(' ', $dialog_styles) . "'"; } ?> uk-overflow-auto>

        <?php if ($close_button_type !== 'none') : ?>
        <button class="uk-modal-close-<?= $close_button_pos === 'top-left' ? 'default' : 'outside' ?> uk-close-large" type="button" uk-close>
            <?php if ($close_button_type === 'text') : ?>
                <?= htmlspecialchars($close_button_text) ?>
            <?php endif; ?>
        </button>
        <?php endif; ?>

        <div class="uk-modal-header">
            <?php /* You can add a title field if needed in element.json and display here */ ?>
            <?php /* <h2 class="uk-modal-title">Modal Title</h2> */ ?>
        </div>

        <div class="uk-modal-body <?= ($modal_height && $modal_height !== 'auto') ? 'uk-overflow-auto' : '' ?>">
            <?php
            // --- Dynamic Content ---
            if ($content_type === 'custom') {
                echo $custom_content; // Already processed by YOOtheme Pro if it's from editor
            } elseif ($content_type === 'article' && $article_id) {
                // If using YOOtheme's dynamic content mapping, $props['modal_article_id'] might already be the content.
                // Otherwise, manual fetching (if implemented in element.php and passed via $props['fetched_article_content'])
                if (!empty($props['fetched_article_content'])) {
                    echo $props['fetched_article_content'];
                } else {
                    // Fallback or rely on YOOtheme Pro dynamic source rendering
                    // This part assumes YOOtheme Pro's dynamic content system will populate the field if mapped.
                    // If not mapped, and no manual fetch, this will be empty or show the ID.
                    echo sprintf(esc_html__('<!-- Loading article content for ID: %s -->', 'modal_popup_element'), htmlspecialchars($article_id));
                     // To actually render a post by ID directly here (less ideal than dynamic content sources):
                     // $post_to_render = get_post(is_numeric($article_id) ? intval($article_id) : $article_id);
                     // if ($post_to_render) echo apply_filters('the_content', $post_to_render->post_content);
                }
            } elseif ($content_type === 'widget_area' && $widget_area_id) {
                if (!empty($props['fetched_widget_area_content'])) {
                    echo $props['fetched_widget_area_content'];
                } else {
                    // Fallback or rely on YOOtheme Pro dynamic source rendering
                    echo sprintf(esc_html__('<!-- Loading widget area: %s -->', 'modal_popup_element'), htmlspecialchars($widget_area_id));
                    // To actually render a widget area by ID/name (less ideal):
                    // if (is_active_sidebar($widget_area_id)) { dynamic_sidebar($widget_area_id); }
                }
            }
            ?>
        </div>

        <?php /* Optional Footer
        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
            <button class="uk-button uk-button-primary" type="button">Save</button>
        </div>
        */ ?>

    </div>
</div>

<?php
// --- Auto Open/Close ---
if ($trigger_type === 'auto' && is_numeric($auto_open_delay) && $auto_open_delay >= 0) :
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const modalElement = document.getElementById('<?= $modal_id ?>');
            if (modalElement && typeof UIkit !== 'undefined') {
                UIkit.modal(modalElement).show();
            }
        }, <?= $auto_open_delay ?>);
    });
</script>
<?php
endif;

if (is_numeric($auto_close_delay) && $auto_close_delay > 0) :
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('<?= $modal_id ?>');
        if (modalElement && typeof UIkit !== 'undefined') {
            UIkit.util.on(modalElement, 'shown', function () {
                setTimeout(function() {
                    UIkit.modal(modalElement).hide();
                }, <?= $auto_close_delay ?>);
            });
        }
    });
</script>
<?php endif; ?>

<?php
// --- Custom Overlay Color ---
// UIkit modals create their overlay dynamically, making it hard to style with a simple class.
// We need to use JavaScript to find the overlay and apply the color if set.
if ($modal_overlay_bg) :
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('<?= $modal_id ?>');
    if (modalElement && typeof UIkit !== 'undefined') {
        UIkit.util.on(modalElement, 'beforeshow', function () {
            // The overlay is typically a sibling of .uk-modal-page or a direct child of body during modal open
            // It might not exist yet at beforeshow, or it might be the one from a previous modal.
            // A more robust way is to find it when it's active.
        });
        UIkit.util.on(modalElement, 'shown', function () {
            // Find the overlay: UIkit adds .uk-open to the modal and .uk-modal-page to body.
            // The overlay is usually div.uk-modal-page > div.uk-modal-overlay, or just .uk-modal-overlay if multiple modals.
            // Let's try to find the one associated with this modal.
            // This is tricky because overlays are shared or dynamically inserted.
            // A simpler approach for a single modal might be to target '.uk-modal-page > .uk-modal-container + .uk-modal-overlay'
            // However, UIkit 3 doesn't use .uk-modal-overlay. It uses the .uk-modal itself with fixed position and a background.
            // The 'bg-close' behavior is on the modal itself.
            // So, if we want an overlay color, it's the modal's own background when it's full screen, or a separate div.
            // For a standard modal, the overlay is actually the semi-transparent background of the `uk-modal` div itself.
            // Let's try setting it directly if it's not a 'container' type modal.
            const modalPage = document.querySelector('.uk-modal-page');
            if (modalPage && modalPage.contains(modalElement)) { // this modal is open
                 modalElement.style.backgroundColor = '<?= esc_js($modal_overlay_bg) ?>';
            }
        });
        UIkit.util.on(modalElement, 'hidden', function () {
            modalElement.style.backgroundColor = ''; // Reset on close
        });
    }
});
</script>
<?php endif; ?>
