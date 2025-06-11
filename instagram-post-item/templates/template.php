<?php
// Template for a single Instagram Post Item
// $props contains the item's own properties (caption, media_url, etc.)
// $element contains the parent element's properties (layout_type, show_captions, etc.)

$item_el = $this->el('div', [
    'class' => [
        'el-item',
        // Add specific classes based on parent settings if needed
        // e.g., if ($element['some_parent_setting']) { 'my-item-class'; }
    ]
]);

$media_url = $props['media_url'] ?? '';
$thumbnail_url = $props['thumbnail_url'] ?? $media_url; // Fallback to media_url for image thumbnails
$media_type = $props['media_type'] ?? 'IMAGE';
$caption = $props['caption'] ?? '';
$permalink = $props['permalink'] ?? '';

// Parent settings
$show_captions = $element['show_captions'] ?? true;
$caption_max_length = $element['caption_max_length'] ?? null;
$show_likes_comments = $element['show_likes_comments'] ?? true;
$show_button_link = $element['show_button_link'] ?? true;
$button_style = $element['button_style'] ?? 'default';
$button_text = $element['button_text'] ?? 'View on Instagram';
$open_in_lightbox = $element['open_in_lightbox'] ?? true;
$video_controls = $element['video_controls'] ?? true;

if ($caption_max_length && iconv_strlen($caption) > $caption_max_length) {
    $caption = iconv_substr($caption, 0, $caption_max_length) . '...';
}

?>

<?= $item_el($props) // Open el-item tag ?>
    <figure class="uk-text-center">
        <?php if ($media_url): ?>
            <?php
            $lightbox_attrs = $open_in_lightbox ? [
                'uk-lightbox' => true,
                'href' => $media_url, // Link to full media for lightbox
                'data-caption' => htmlspecialchars($caption),
            ] : [];
            $media_link_attrs = $open_in_lightbox ? $lightbox_attrs : ($permalink ? ['href' => $permalink, 'target' => '_blank'] : []);
            ?>
            <a <?= $this->attrs($media_link_attrs) ?>>
                <?php if ($media_type === 'VIDEO'): ?>
                    <video src="<?= $media_url ?>" <?= $video_controls ? 'controls' : '' ?> playsinline muted loop class="uk-responsive-width" style="max-height: 300px;"></video>
                    <?php // Poster could be $thumbnail_url, but native controls might override it ?>
                <?php else: // IMAGE or CAROUSEL_ALBUM (show first image) ?>
                    <img src="<?= $thumbnail_url ?>" alt="<?= htmlspecialchars(substr($caption, 0, 50)) ?>" class="uk-responsive-width" style="max-height: 300px;">
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <?php if ($show_captions && $caption): ?>
            <figcaption class="uk-padding-small uk-text-small">
                <?= htmlspecialchars($caption) ?>
            </figcaption>
        <?php endif; ?>
    </figure>

    <div class="uk-padding-small uk-text-small">
        <?php if ($show_likes_comments): ?>
            <p>
                <?php if (isset($props['likes_count'])): ?>
                    Likes: <?= $props['likes_count'] ?>
                <?php endif; ?>
                <?php if (isset($props['comments_count'])): ?>
                    | Comments: <?= $props['comments_count'] ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if ($show_button_link && $permalink): ?>
            <p class="uk-margin-top">
                <a href="<?= $permalink ?>" class="uk-button uk-button-<?= $button_style ?>" target="_blank">
                    <?= htmlspecialchars($button_text) ?>
                </a>
            </p>
        <?php endif; ?>
    </div>

<?= $item_el->end() // Close el-item tag ?>

```
