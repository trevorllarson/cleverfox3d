<?php

// Custom Page for Clearing Caches
add_action( 'admin_menu', 'cms_notes_admin' );

function cms_notes_admin() {
    add_menu_page( 'Helpers', 'Helpers', 'manage_options', 'cms-notes-admin-page', 'cms_notes_admin_content', 'dashicons-sos', 0  );
    add_menu_page( 'Block Examples', 'Helpers', 'manage_options', 'cms-notes-admin-page', 'cms_notes_admin_content', 'dashicons-sos', 0  );
}

function cms_notes_admin_content() {

    ?>
    <div class="wrap">

        <?php if(isset($responseMessage) and $responseMessage != ""): ?>

            <div class="notice <?= (isset($responseSuccess) and $responseSuccess) ? 'updated': 'error'; ?>">
                <p><?= $responseMessage; ?></p>
            </div>

        <?php endif; ?>

        <h2>Helpers and Notes on Using Your Custom WordPress Site</h2>

        <h3>Accessing the Request Info Popup Form</h3>
        <p>
            Wherever you would like to implement a link to the Request Info popup form, you can set the link of the button or inline content link to <span style="display: inline-block; background: white; border: 1px solid lightgrey;">#request-info</span>. We'll take care of the rest!
        </p>

        <!-- TODO: Add Snapshots of each block, maybe put on it's own page with links between these two. -->

    </div>
    <?php
}
