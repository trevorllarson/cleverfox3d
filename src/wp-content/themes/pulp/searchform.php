<?php

$unique_id = wp_unique_id('search-form-');
?>
<form role="search" aria-label="site search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="<?php echo esc_attr($unique_id); ?>">Search</label>
    <input type="search" id="<?php echo esc_attr($unique_id); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" />
    <button type="submit" class="btn">Search</button>
</form>
