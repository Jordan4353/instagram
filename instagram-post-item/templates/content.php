<?php
// Content template for a single Instagram Post Item
// $props contains the item's own properties

$output = '';
if (!empty($props['caption'])) {
    $output .= 'Caption: ' . $props['caption'] . "\n";
}
if (!empty($props['media_url'])) {
    $output .= 'Media: ' . $props['media_url'] . "\n";
}
if (!empty($props['permalink'])) {
    $output .= 'Link: ' . $props['permalink'] . "\n";
}

echo nl2br(htmlspecialchars(trim($output ?: 'Instagram Post Item')));
?>
