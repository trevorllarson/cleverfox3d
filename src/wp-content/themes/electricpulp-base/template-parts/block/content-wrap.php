<?php
$className = electricpulp_get_block_class_attr($block);
?>
<section class="<?php echo $className ?> <?php echo (!strstr($className, 'pt-')) ? 'pt-sm pb-sm' : '' ?>">
    <div class="row justify-content-center">
        <div class="col-lg-6 cms-styles">
            <InnerBlocks />
        </div>
    </div>
</section>