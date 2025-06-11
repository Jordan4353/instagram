<?php
// Main template for the Instagram Feed element
// $props contains the element's settings
// $children contains the Instagram post items

// Ensure JLoader is available
if (!class_exists('JLoader')) {
    if (defined('JPATH_LIBRARIES') && file_exists(JPATH_LIBRARIES . '/cms/loader.php')) {
        require_once JPATH_LIBRARIES . '/cms/loader.php';
    } elseif (defined('JPATH_PLATFORM') && file_exists(JPATH_PLATFORM . '/includes/loader.php')) {
        // Joomla 3.x platform
        require_once JPATH_PLATFORM . '/includes/loader.php';
    } else {
        // Fallback or error if JLoader cannot be found
        // This might indicate an environment where Joomla's core isn't fully loaded
    }
}

if (class_exists('JLoader')) {
    JLoader::import('joomla.application.component.helper');
}


// Example of accessing settings:
// $layout_type = $props['layout_type'] ?? 'grid';
// $columns = $props['columns'] ?? '3';

// Placeholder for API call logic (will be in element.php)
// For now, we assume $children are populated if the API call was successful.

if (empty($children) && !empty($props['instagram_access_token'])) {
    echo "<p>Instagram feed data is being fetched or no posts found. Please configure the access token and source settings. If already configured, check your API permissions and token validity.</p>";
} elseif (empty($props['instagram_access_token'])) {
    echo "<p>Please enter your Instagram Access Token in the element's Content settings.</p>";
}

if (!empty($children)) {
    $grid_attrs = [
        'class' => [
            'uk-grid',
            'uk-child-width-1-' . ($props['columns'] ?? '3') . '@m', // Example responsive columns
            $props['columns_gap'] ? 'uk-grid-' . $props['columns_gap'] : ''
        ],
        'uk-grid' => true, // UIkit grid attribute
    ];

    if (($props['layout_type'] ?? 'grid') === 'masonry') {
        $grid_attrs['uk-grid'] = 'masonry: true';
    }

    // Start el-element
    $el = $this->el('div', [
        'class' => [
            'el-element',
            // Add any other base classes for the element itself
        ],
        // Add other attributes for the main wrapper if needed
    ]);

    echo $el($props); // Render opening tag for el-element

    // TODO: Add Carousel specific wrapper if layout_type is carousel
    // For example:
    // if (($props['layout_type'] ?? 'grid') === 'carousel') {
    //     echo '<div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slider="' . ($props['carousel_autoplay'] ? 'autoplay: true; autoplay-interval: ' . ($props['carousel_interval'] ?? 3000) . ';' : '') . '">';
    //     echo '<ul class="uk-slider-items uk-child-width-1-1 uk-child-width-1-' . ($props['columns'] ?? '3') . '@m">'; // Adjust child-width for carousel
    // } else {
    //     echo '<div' . $this->attrs($grid_attrs) . '>';
    // }


    echo '<div' . $this->attrs($grid_attrs) . '>'; // Non-carousel grid for now

    foreach ($children as $child) {
        // For carousel, each item would be an <li>
        // if (($props['layout_type'] ?? 'grid') === 'carousel') {
        //     echo '<li>';
        // } else {
        //     echo '<div>'; // For grid/masonry
        // }

        echo '<div>'; // For grid/masonry wrapper for each item
        echo $builder->render($child, ['element' => $props]); // Pass parent props to child
        echo '</div>';

        // if (($props['layout_type'] ?? 'grid') === 'carousel') {
        //     echo '</li>';
        // }
    }

    echo '</div>'; // Close grid_attrs div

    // TODO: Add Carousel navigation if layout_type is carousel
    // if (($props['layout_type'] ?? 'grid') === 'carousel' && ($props['carousel_show_navigation'] ?? true)) {
    //     echo '<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>';
    //     echo '<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>';
    // }
    // if (($props['layout_type'] ?? 'grid') === 'carousel') {
    //     echo '</ul>'; // Close slider-items
    //     echo '</div>'; // Close uk-slider div
    // }

    echo $el->end(); // Render closing tag for el-element

} else if (empty($props['instagram_access_token'])) {
    // Handled above, but kept for clarity
} else {
    // If $children is empty but token was provided - means no posts or API error
    // This message is also shown above, consider consolidating.
    // echo "<p>No posts found for the configured source, or there was an issue fetching data.</p>";
}
?>
