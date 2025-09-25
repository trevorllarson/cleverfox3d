<?php
if (wp_get_environment_type() !== 'production') {
    return;
}
if (!defined('GA_ID')) {
    return;
}
if (GA_ID === '') {
    return;
}
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_html(GA_ID) ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', '<?php echo esc_html(GA_ID) ?>');
</script>
