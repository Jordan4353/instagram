<?php
// Content template for the Instagram Feed element (for search indexing, etc.)
// $props contains the element's settings
// $children contains the Instagram post items (already processed by element.php)

$content = '';
if (!empty($children)) {
    foreach ($children as $child) {
        if (!empty($child->props['caption'])) {
            $content .= $child->props['caption'] . "\n\n"; // Add caption
        }
        if (!empty($child->props['media_url'])) {
            $content .= 'Image/Video: ' . $child->props['media_url'] . "\n";
        }
    }
} else {
    $content = 'Instagram Feed Element - No posts to display or not yet configured.';
}

// Output content in a simple format
echo nl2br(htmlspecialchars($content));

?>
