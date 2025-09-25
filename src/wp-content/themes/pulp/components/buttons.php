<?php
// Set up wrapper class
$wrapperExtraClass = '';
if($animate) $wrapperExtraClass = 'animate animate-1s';

// Add special class if only one button
if(count($buttons) === 1) {
    $wrapper .= ' has-one-button';
}

// Start wrapper if needed
if(!empty($wrapper)):
    ?>
    <div class="<?= esc_attr($wrapper); ?> <?= esc_attr($wrapperExtraClass); ?>" data-animation="scaleIn" data-animation-delay="<?= esc_attr($delayTime); ?>s">
<?php endif; ?>

<?php
// Render each button
foreach($buttons as $button) {
    // Since renderButton now uses an include and doesn't return a string,
    // we need to call it differently
    $this->renderButton($button);
}
?>

<?php
// End wrapper if needed
if(!empty($wrapper)):
    ?>
    </div>
<?php endif; ?>