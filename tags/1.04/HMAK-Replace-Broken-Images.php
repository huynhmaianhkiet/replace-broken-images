<?php
/**
 * Plugin Name: Replace Broken Images
 * Version: 1.04
 * Description: Alternate image with a default image if source image is not found on posts and pages.
 * Author: Hura Apps
 * Author URI: https://www.huraapps.com
 * Text Domain: replace-broken-images
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HMAK_RBI_OPTION_KEY', 'default_image_for_bronken_link' );
define( 'HMAK_RBI_NONCE',      'default_image_for_bronken_link_nonce' );

/**
 * Returns the URL of the fallback image.
 * Uses the saved attachment if set, otherwise falls back to the bundled default.
 */
function hmak_rbi_get_fallback_url() {
	$attachment_id = (int) get_option( HMAK_RBI_OPTION_KEY, 0 );
	if ( $attachment_id ) {
		$url = wp_get_attachment_url( $attachment_id );
		if ( $url ) {
			return $url;
		}
	}
	return plugins_url( 'images/default.jpg', __FILE__ );
}

// ─── Admin menu ──────────────────────────────────────────────────────────────

function hmak_rbi_add_menu_item() {
	add_menu_page(
		esc_html__( 'Replace Broken Images Panel', 'replace-broken-images' ),
		esc_html__( 'Replace Broken Images', 'replace-broken-images' ),
		'manage_options',
		'hmak-replace-broken-images-panel',
		'hmak_rbi_settings_page',
		null,
		99
	);
}
add_action( 'admin_menu', 'hmak_rbi_add_menu_item' );

// ─── Enqueue media selector script only on plugin page ───────────────────────

function hmak_rbi_maybe_enqueue_scripts( $hook ) {
	if ( 'toplevel_page_hmak-replace-broken-images-panel' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	add_action( 'admin_footer', 'hmak_rbi_media_selector_scripts' );
}
add_action( 'admin_enqueue_scripts', 'hmak_rbi_maybe_enqueue_scripts' );

// ─── Settings page ───────────────────────────────────────────────────────────

function hmak_rbi_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( "You don't have permission to access this page.", 'replace-broken-images' ) );
	}

	if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) {
		check_admin_referer( HMAK_RBI_NONCE );
		update_option( HMAK_RBI_OPTION_KEY, absint( $_POST['image_attachment_id'] ) );
	}

	$preview_url   = esc_url( hmak_rbi_get_fallback_url() );
	$attachment_id = absint( get_option( HMAK_RBI_OPTION_KEY, 0 ) );
	?>
	<style>
		h3.hndle2 {
			cursor: pointer;
			border-bottom: 1px solid #eeeeee;
		}
	</style>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortabless ui-sortable">
				<div class="postbox">
					<h3 class="hndle2"><span><?php esc_html_e( 'About This Plugin', 'replace-broken-images' ); ?></span></h3>
					<div class="inside">
						<p><?php esc_html_e( 'This plugin replaces broken images in posts and pages with a default image.', 'replace-broken-images' ); ?></p>
					</div>
				</div>
				<div class="postbox">
					<h3 class="hndle2"><span><?php esc_html_e( 'About Us', 'replace-broken-images' ); ?></span></h3>
					<div class="inside">
						<p><?php esc_html_e( 'Hura Apps is a web development team based in Vietnam. You can contact us at:', 'replace-broken-images' ); ?></p>
						<ul>
							<li><?php esc_html_e( 'Email:', 'replace-broken-images' ); ?> <a href="mailto:support@huraapps.com">support@huraapps.com</a></li>
							<li><?php esc_html_e( 'Website:', 'replace-broken-images' ); ?> <a href="//www.huraapps.com" target="_blank" rel="noopener noreferrer">www.huraapps.com</a></li>
							<li><?php esc_html_e( 'LinkedIn:', 'replace-broken-images' ); ?> <a href="//www.linkedin.com/company/huraapps" target="_blank" rel="noopener noreferrer">huraapps</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="has-sidebar sm-padded">
			<div id="post-body-content" class="has-sidebar-content">
				<div class="meta-box-sortabless">
					<div class="postbox">
						<h3 class="hndle2"><?php esc_html_e( 'Settings', 'replace-broken-images' ); ?></h3>
						<div class="inside">
							<p><?php esc_html_e( 'Please select alternate image', 'replace-broken-images' ); ?></p>
							<form method="post">
								<?php wp_nonce_field( HMAK_RBI_NONCE ); ?>
								<div class="image-preview-wrapper">
									<img id="image-preview" src="<?php echo $preview_url; ?>" height="100">
								</div>
								<input id="upload_image_button" type="button" class="button" value="<?php esc_attr_e( 'Upload image', 'replace-broken-images' ); ?>" />
								<input type="hidden" name="image_attachment_id" id="image_attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>">
								<input type="submit" name="submit_image_selector" value="<?php esc_attr_e( 'Save', 'replace-broken-images' ); ?>" class="button-primary">
							</form>
						</div>
					</div>
					<div class="postbox">
						<div class="inside">
							<p style="text-align:center;">
								<?php
								printf(
									wp_kses(
										/* translators: 1: year, 2: website URL, 3: author URL */
										__( 'Copyright &copy; %1$s by <a href="%2$s" target="_blank" rel="noopener noreferrer">Hura Apps</a>. All rights reserved.<br>Developed and Designed by <a href="%3$s" target="_blank" rel="noopener noreferrer">Kiet Huynh</a>.', 'replace-broken-images' ),
										array( 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ), 'br' => array() )
									),
									esc_html( date( 'Y' ) ),
									'//www.huraapps.com',
									'//anhkiet.biz'
								);
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// ─── Media selector JS ───────────────────────────────────────────────────────

function hmak_rbi_media_selector_scripts() {
	$attachment_id = absint( get_option( HMAK_RBI_OPTION_KEY, 0 ) );
	?>
	<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id;
			var set_to_post_id   = <?php echo $attachment_id; ?>;

			$( '#upload_image_button' ).on( 'click', function( event ) {
				event.preventDefault();

				if ( file_frame ) {
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					file_frame.open();
					return;
				}

				wp.media.model.settings.post.id = set_to_post_id;

				file_frame = wp.media.frames.file_frame = wp.media( {
					title:    'Select an image to upload',
					button:   { text: 'Use this image' },
					multiple: false
				} );

				file_frame.on( 'select', function() {
					var attachment = file_frame.state().get( 'selection' ).first().toJSON();
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#image_attachment_id' ).val( attachment.id );
					wp.media.model.settings.post.id = wp_media_post_id;
				} );

				file_frame.open();
			} );

			$( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			} );
		} );
	</script>
	<?php
}

// ─── Front-end: inject onerror handler on post/page images ───────────────────

function hmak_rbi_inject_onerror( $content ) {
	if ( ! is_singular() ) {
		return $content;
	}
	$fallback_url = esc_js( hmak_rbi_get_fallback_url() );
	$replacement  = '<img onerror="this.onerror=null;this.src=\'' . $fallback_url . '\'"';
	return str_ireplace( '<img', $replacement, $content );
}
add_filter( 'the_content', 'hmak_rbi_inject_onerror', 10 );
