<?php
// Set up the label
if(isset($button['link'], $button['link']['title'])) {
    $label = esc_html($button['link']['title']);
} else {
    $label = '';
}

// Set up the button classes
$buttonClass = 'btn-primary ';

if(isset($button['button_style'])) {
    $buttonClass .= 'btn-' . esc_attr($button['button_style']) . ' ';
}

$buttonClass .= ' ' . esc_attr($extraClass) . ' ';

// Set up the target attribute
$target = (isset($button['link']['target'])) ? esc_attr($button['link']['target']) : '';
$url = isset($button['link']['url']) ? esc_url($button['link']['url']) : '#';

// Special button types with SVGs (commented out for now)
// Uncomment and modify as needed
/*
if(isset($button['button_style']) && $button['button_style'] === 'download') {
    ?>
    <a href="<?= $url; ?>" class="btn <?= $buttonClass; ?>" target="<?= $target; ?>" data-content="<?= $label; ?>">
        <svg width="11" height="15" viewBox="0 0 11 15" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.70226 4.6897L6.22612 8.16584V0H4.60857V8.16584L1.13243 4.6897L0 5.82138L5.41738 11.2388L10.8348 5.82138L9.70226 4.6897Z" fill="#0098FE"/>
            <path d="M0.969727 12.9366H9.86324V14.5534H0.969727V12.9366Z" fill="#0098FE"/>
        </svg>
        <?= $label; ?>
    </a>
    <?php
    return;
}

if(isset($button['button_style']) && $button['button_style'] === 'arrow') {
    ?>
    <a href="<?= $url; ?>" class="btn <?= $buttonClass; ?>" target="<?= $target; ?>" data-content="<?= $label; ?>">
        <?= $label; ?>
        <svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M-3.52453e-07 0.936807L3.89257 4.51093L0.0245715 8.10665L1.04099 9L6 4.53249L1.06556 -4.65771e-08L-3.52453e-07 0.936807Z" fill="#05C356"/>
        </svg>
    </a>
    <?php
    return;
}
*/
?>

<a
        href="<?= esc_url($url); ?>"
        class="btn <?= esc_attr($buttonClass); ?>"
    <?= !empty($target) ? 'target="' . esc_attr($target) . '" rel="noopener noreferrer"' : ''; ?>
        data-content="<?= esc_attr($label); ?>">
    <?= esc_html($label); ?>
</a>
