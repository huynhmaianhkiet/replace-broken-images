<?php
	/*
	  Plugin Name: Replace Broken Images
	  Version: 1.02
	  Description: Alternate image with a default image if source image is not found on posts and pages.
	  Author: Hura Apps
	  Author URI: http://www.huraapps.com
	 */
	function HMAK_FixImageBrokenLink_settings_page()
	{	
		if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
			check_admin_referer( 'default_image_for_bronken_link_nonce');
			update_option( 'default_image_for_bronken_link', absint( $_POST['image_attachment_id'] ) );
		endif;
		wp_enqueue_media();
		?>
			<style>
				h3.hndle2{
					cursor: pointer;
					border-bottom: 1px solid #eeeeee;
				}			
			</style>
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar">
					<div id="side-sortables" class="meta-box-sortabless ui-sortable">
						<div class="postbox ">
							<h3 class="hndle2"><span>About This Plugin</span></h3>
							<div class="inside">
								<p>This plugin will help to replace broken images in post by a default image.</p>
								<p>Version: 1.0 - 11/03/2017</p>
								<p>Version: 1.01 - 11/03/2019</p>
								<p>Version: 1.02 - 21/09/2019</p>
							</div>
						</div>
						<div class="postbox">
								<h3 class="hndle2"><span>About Us</span></h3>
								<div class="inside">
									<p></p>
									<p>Hura Apps is a Vietnam-based Web & Mobile App development team. You can contact us via:</p>
									<ul>
										<li>Email: <a href="mailto:info@huraapps.com">Info@HuraApps.Com</a></li>
										<li>Facebook: <a href="//www.facebook.com/huraapps" target="_blank">HuraApps</a></li>
										<li>Website: <a href="//www.huraapps.com" target="_blank">wWw.HuraApps.Com</a></li>
									</ul>
									<p></p>
									<form id="paypal-donation" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
									<input type="hidden" name="cmd" value="_s-xclick">
									<input type="hidden" name="hosted_button_id" value="63V868PTNSW52">
									<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
									<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
								</form>	
								</div>
						</div>
					</div>
				</div>
				<div class="has-sidebar sm-padded">
					<div id="post-body-content" class="has-sidebar-content">
						<div class="meta-box-sortabless">
							<div class="postbox">
								<h3 class="hndle2">Settings</h3>
								<div class="inside">
									<?php
										if(current_user_can('administrator')){
									?>
									<p>Please select alternate image</p>
									<form method='post'>
										 <?php wp_nonce_field('default_image_for_bronken_link_nonce') ?>
										<div class='image-preview-wrapper'>
											<img id='image-preview' src='<?php if(!get_option('default_image_for_bronken_link')){ echo plugins_url( 'images/default.jpg', __FILE__ ); }else{ echo wp_get_attachment_url( get_option( 'default_image_for_bronken_link' ) ); } ?>' height='100'>
										</div>
										<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
										<input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo get_option( 'default_image_for_bronken_link' ); ?>'>
										<input type="submit" name="submit_image_selector" value="Save" class="button-primary">
									</form>
									<?php
										}else{
											echo "<p style='text-align:center;'>You don't have permission to access</p>";
										}
									?>
								</div>
							</div>
							
							<div class="postbox">
								<div class="inside">
									<p style="text-align:center;">Copyright &copy; <?php echo date("Y"); ?> by <a href="//www.huraapps.com" target="_blank">wWw.HuraApps.Com</a>. All rights reserved.<br>Developed and Designed by <a href="//me.anhkiet.info">Huynh Mai Anh Kiet</a>.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	
	function add_FixImageBrokenLink_menu_item()
	{
		add_menu_page("Replace Broken Images Panel", "Replace Broken Images", "manage_options", "hmak-replace-broken-images-panel", "HMAK_FixImageBrokenLink_settings_page", null, 99);
	}
	add_action("admin_menu", "add_FixImageBrokenLink_menu_item");
	if(!empty($_GET['page']) && $_GET['page']=='hmak-replace-broken-images-panel'){
		add_action( 'admin_footer', 'hmak_media_selector_print_scripts' );
	}
	function hmak_media_selector_print_scripts() {
		$hmak_saved_attachment_post_id = get_option( 'default_image_for_bronken_link', 0 );
		?><script type='text/javascript'>
			jQuery( document ).ready( function( $ ) {
				var file_frame;
				var wp_media_post_id = wp.media.model.settings.post.id;
				var set_to_post_id = <?php echo $hmak_saved_attachment_post_id; ?>;
				jQuery('#upload_image_button').on('click', function( event ){
					event.preventDefault();
					if ( file_frame ) {
						file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
						file_frame.open();
						return;
					} else {
						wp.media.model.settings.post.id = set_to_post_id;
					}
					file_frame = wp.media.frames.file_frame = wp.media({
						title: 'Select a image to upload',
						button: {
							text: 'Use this image',
						},
						multiple: false
					});
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();
						$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
						$( '#image_attachment_id' ).val( attachment.id );
						wp.media.model.settings.post.id = wp_media_post_id;
					});
					file_frame.open();
				});
				jQuery( 'a.add_media' ).on( 'click', function() {
					wp.media.model.settings.post.id = wp_media_post_id;
				});
			});
		</script><?php
	}
	function HMAK_FixImageBrokenLink_main($content) {
		if(!get_option('default_image_for_bronken_link')){
			$code = "<img onerror=\"this.src='".plugins_url( 'images/default.jpg', __FILE__ )."'\"";
		}else{
			$code = "<img onerror=\"this.src='".wp_get_attachment_url( get_option( 'default_image_for_bronken_link' ) )."'\"";
		}
		$content = str_ireplace("<img", $code, $content);
		return $content;
	}	
	add_filter("the_content", 'HMAK_FixImageBrokenLink_main', 10);
?>