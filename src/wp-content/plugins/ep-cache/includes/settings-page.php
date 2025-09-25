
<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">

	<h1><?php _e( 'Settings', 'ep-cache' ); ?></h1>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">

		<?php settings_fields( 'ep-cache' ); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Cache Path', 'ep-cache' ); ?></th>
				<td>
					<input type="text" class="regular-text code" name="ep_cache_path" placeholder="/etc/nginx/cache/" value="<?php echo esc_attr( get_option( 'ep_cache_path' ) ); ?>" />
					<p class="description"><?php _e( 'The absolute path to the location of the cache zone, specified in the Nginx <code>fastcgi_cache_path</code> or <code>proxy_cache_path</code> directive.', 'ep-cache' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Auto-purge Cache', 'ep-cache' ); ?></th>
				<td>
					<label for="ep_cache_auto">
						<input name="ep_cache_auto" type="checkbox" id="ep_cache_auto" value="1" <?php checked( get_option( 'ep_cache_auto' ), '1' ); ?> />
						<?php _e( 'Automatically flush the cache when content changes.', 'ep-cache' ); ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Single Purge Post Types', 'ep-cache' ); ?></th>
				<td>
					<input type="text" class="regular-text code" name="ep_cache_purge_singular" placeholder="Recommended: page" value="<?php echo esc_attr( get_option( 'ep_cache_purge_singular' ) ); ?>" />
					<p class="description"><?php _e( 'Comma-separated list of post types that purge only its URL.', 'ep-cache' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Purge Everything Post Types', 'ep-cache' ); ?></th>
				<td>
					<input type="text" class="regular-text code" name="ep_cache_purge_all" placeholder="Recommended: post,product" value="<?php echo esc_attr( get_option( 'ep_cache_purge_all' ) ); ?>" />
					<p class="description"><?php _e( 'Comma-separated list of post types that purge all cache. Theme changes or menu updates will always clear everything.', 'ep-cache' ); ?></p>
				</td>
			</tr>
		</table>

		<?php echo get_submit_button( null, 'primary large' ); ?>
		
	</form>

	<hr>

	<form action="<?= $this->admin_page ?>" method="post">

		<?php wp_nonce_field('ep_purge', '_purge_nonce') ?>
		
		<h2 id="cache-tools">Purge Tools</h2>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Purge Single URL', 'ep-cache' ); ?></th>
				<td>
					<input type="url" class="regular-text code" name="ep_cache_purge_url" placeholder="" value="" />
				</td>
			</tr>
		</table>

		<?php if ( !is_wp_error( $this->is_valid_path() ) ) : ?>
			<p class="submit">
				<?php echo get_submit_button( 'Purge URL', 'primary large', null, false ); ?> or <a href="<?php echo wp_nonce_url( admin_url( add_query_arg( 'action', 'purge-all', $this->admin_page ) ), 'purge-all' ); ?>" class="button button-large button-secondary"><?php _e( 'Purge Everything', 'ep-cache' ); ?></a>
			</p>
		<?php endif; ?>
	</form>

</div>
