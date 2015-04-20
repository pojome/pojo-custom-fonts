<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_CWF_Admin_UI {

	protected $_capability = 'edit_theme_options';
	protected $_page_id = 'pojo-cwf';

	public function get_setting_page_link( $message_id = '' ) {
		$link_args = array(
			'page' => $this->_page_id,
		);

		if ( ! empty( $message_id ) )
			$link_args['message'] = $message_id;

		return add_query_arg( $link_args, admin_url( 'admin.php' ) );
	}

	public function get_remove_font_link( $font_id ) {
		return add_query_arg(
			array(
				'action' => 'pcwf_remove_font',
				'font_id' => $font_id,
				'_nonce' => wp_create_nonce( 'pcwf-remove-font-' . $font_id ),
			),
			admin_url( 'admin-ajax.php' )
		);
	}

	public function ajax_pcwf_remove_font() {
		if ( ! isset( $_GET['font_id'] ) || ! check_ajax_referer( 'pcwf-remove-font-' . $_GET['font_id'], '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-cwf' ) );
		}

		Pojo_CWF_Main::instance()->db->remove_font( $_GET['font_id'] );

		wp_redirect( $this->get_setting_page_link() );
		die();
	}

	public function manager_actions() {
		if ( empty( $_POST['pcwf_action'] ) )
			return;

		switch ( $_POST['pcwf_action'] ) {
			case 'add_font' :
				if ( ! check_ajax_referer( 'pcwf-add-font', '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-cwf' ) );
				}

				$return = Pojo_CWF_Main::instance()->db->update_font(
					array(
						'name' => $_POST['name'],
						'font_eot' => $_POST['font_eot'],
						'font_woff' => $_POST['font_woff'],
						'font_ttf' => $_POST['font_ttf'],
						'font_svg' => $_POST['font_svg'],
					)
				);

				if ( is_wp_error( $return ) ) {
					wp_die( $return->get_error_message() );
				}

				wp_redirect( $this->get_setting_page_link() );
				die;

			case 'update_font' :
				if ( ! isset( $_POST['font_id'] ) || ! check_ajax_referer( 'pcwf-update-font-' . $_POST['font_id'], '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo-cwf' ) );
				}

				$return = Pojo_CWF_Main::instance()->db->update_font(
					array(
						'name' => $_POST['name'],
						'font_eot' => $_POST['font_eot'],
						'font_woff' => $_POST['font_woff'],
						'font_ttf' => $_POST['font_ttf'],
						'font_svg' => $_POST['font_svg'],
					),
					$_POST['font_id']
				);

				if ( is_wp_error( $return ) ) {
					wp_die( $return->get_error_message() );
				}

				wp_redirect( $this->get_setting_page_link() );
				die;
		}
	}

	public function register_menu() {
		add_submenu_page(
			'pojo-home',
			__( 'Custom Web Fonts', 'pojo-cwf' ),
			__( 'Custom Web Fonts', 'pojo-cwf' ),
			$this->_capability,
			'pojo-cwf',
			array( &$this, 'display_page' )
		);
	}

	public function display_page() {
		$fonts = Pojo_CWF_Main::instance()->db->get_fonts();
		?>
		<div class="wrap">

			<div id="icon-themes" class="icon32"></div>
			<h2><?php _e( 'Widgets Area', 'pojo-cwf' ); ?></h2>

			<?php // Add Font ?>
			<div>
				<form action="" method="post">
					<input type="hidden" name="pcwf_action" value="add_font" />
					<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'pcwf-add-font' ) ?>" />

					<div>
						<label>
							<?php _e( 'Name', 'pojo-cwf' ); ?>:
							<input type="text" name="name" />
						</label>
					</div>

					<?php $this->_print_image_field( 'font_eot', __( 'Font .eot', '' ) ); ?>
					<?php $this->_print_image_field( 'font_woff', __( 'Font .woff', 'pojo-cwf' ) ); ?>
					<?php $this->_print_image_field( 'font_ttf', __( 'Font .ttf', 'pojo-cwf' ) ); ?>
					<?php $this->_print_image_field( 'font_svg', __( 'Font .svg', 'pojo-cwf' ) ); ?>
					
					<div>
						<p><button type="submit" class="button"><?php _e( 'Create', 'pojo-cwf' ); ?></button></p>
					</div>
				</form>
			</div>

			<?php // All Fonts ?>
			<div>
				<?php if ( ! empty( $fonts ) ) : ?>
					<?php foreach ( $fonts as $font_id => $font_data ) : ?>
						<form action="" method="post">
							<input type="hidden" name="pcwf_action" value="update_font" />
							<input type="hidden" name="font_id" value="<?php echo esc_attr( $font_id ); ?>" />
							<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'pcwf-update-font-' . $font_id ) ?>" />

							<h3><?php echo $font_data['name']; ?></h3>

							<div>
								<a href="<?php echo $this->get_remove_font_link( $font_id ); ?>"><?php _e( 'Remove', 'pojo-cwf' ); ?></a>
							</div>

							<div>
								<label>
									<?php _e( 'Name', 'pojo-cwf' ); ?>:
									<input type="text" name="name" value="<?php echo esc_attr( $font_data['name'] ); ?>" />
								</label>
							</div>

							<?php $this->_print_image_field( 'font_eot', __( 'Font .eot', '' ), $font_data['font_eot'] ); ?>
							<?php $this->_print_image_field( 'font_woff', __( 'Font .woff', '' ), $font_data['font_woff'] ); ?>
							<?php $this->_print_image_field( 'font_ttf', __( 'Font .ttf', '' ), $font_data['font_ttf'] ); ?>
							<?php $this->_print_image_field( 'font_svg', __( 'Font .svg', '' ), $font_data['font_svg'] ); ?>

							<div>
								<label>
									<?php _e( 'Font .eot', 'pojo-cwf' ); ?>:
									<input type="text" name="font_eot" value="<?php echo esc_attr( $font_data['font_eot'] ); ?>" />
								</label>
							</div>

							<div>
								<label>
									<?php _e( 'Font .woff', 'pojo-cwf' ); ?>:
									<input type="text" name="font_woff" value="<?php echo esc_attr( $font_data['font_woff'] ); ?>" />
								</label>
							</div>

							<div>
								<label>
									<?php _e( 'Font .ttf', 'pojo-cwf' ); ?>:
									<input type="text" name="font_ttf" value="<?php echo esc_attr( $font_data['font_ttf'] ); ?>" />
								</label>
							</div>

							<div>
								<label>
									<?php _e( 'Font .svg', 'pojo-cwf' ); ?>:
									<input type="text" name="font_svg" value="<?php echo esc_attr( $font_data['font_svg'] ); ?>" />
								</label>
							</div>

							<div>
								<p><button type="submit" class="button"><?php _e( 'Update', 'pojo-cwf' ); ?></button></p>
							</div>
						</form>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php _e( 'No have any fonts.', 'pojo-cwf' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function admin_head() {
		?>
		<script>jQuery(document).ready(function($){
				var file_frame;
				window.formfield = '';

				$('body').on('click', '.pojo_cwf_upload_file_button', function(e) {

					e.preventDefault();

					var button = $(this);

					window.formfield = $(this).closest('.pojo_cwf_repeatable_upload_wrapper');

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						//file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.file_frame = wp.media( {
						frame: 'post',
						state: 'insert',
						title: button.data( 'uploader-title' ),
						button: {
							text: button.data( 'uploader-button-text' )
						},
						multiple: $( this ).data( 'multiple' ) == '0' ? false : true  // Set to true to allow multiple files to be selected
					} );

					file_frame.on( 'menu:render:default', function( view ) {
						// Store our views in an object.
						var views = {};

						// Unset default menu items
						view.unset( 'library-separator' );
						view.unset( 'gallery' );
						view.unset( 'featured-image' );
						view.unset( 'embed' );

						// Initialize the views in our view object.
						view.set( views );
					} );

					// When an image is selected, run a callback.
					file_frame.on( 'insert', function() {

						var selection = file_frame.state().get('selection');
						selection.each( function( attachment, index ) {
							attachment = attachment.toJSON();
							if ( 0 === index ) {
								// place first attachment in field
								window.formfield.find( '.pojo_cwf_repeatable_attachment_id_field' ).val( attachment.id );
								window.formfield.find( '.pojo_cwf_repeatable_upload_field' ).val( attachment.url );
								window.formfield.find( '.pojo_cwf_repeatable_name_field' ).val( attachment.title );
							} else {
								// Create a new row for all additional attachments
								var row = window.formfield,
									clone = EDD_Download_Configuration.clone_repeatable( row );

								clone.find( '.pojo_cwf_repeatable_attachment_id_field' ).val( attachment.id );
								clone.find( '.pojo_cwf_repeatable_upload_field' ).val( attachment.url );
								if ( attachment.title.length > 0 ) {
									clone.find( '.pojo_cwf_repeatable_name_field' ).val( attachment.title );
								} else {
									clone.find( '.pojo_cwf_repeatable_name_field' ).val( attachment.filename );
								}
								clone.insertAfter( row );
							}
						});
					});

					// Finally, open the modal
					file_frame.open();
				});
			} );</script>
		<?php
	}

	protected function _print_image_field( $id, $title, $value = '' ) {
		?>
		<div class="pojo_cwf_repeatable_upload_wrapper">
			<div class="pojo_cwf_repeatable_upload_field_container">
				<label>
					<?php echo $title; ?>
					<input type="text" class="pojo_cwf_repeatable_upload_field" name="<?php echo esc_attr( $id ); ?>" placeholder="<?php _e( 'Upload or enter the file URL', '' ); ?>" value="<?php echo esc_attr( $value ); ?>" />
				</label>
	
				<span class="pojo_cwf_upload_file">
					<a href="#" data-uploader-title="<?php _e( 'Insert Font', 'pojo_cwf' ); ?>" data-uploader-button-text="<?php _e( 'Insert', 'pojo_cwf' ); ?>" class="pojo_cwf_upload_file_button" onclick="return false;"><?php _e( 'Upload a Font', 'pojo_cwf' ); ?></a>
				</span>
			</div>
		</div>
		<?php
	}

	public function __construct() {
		$this->manager_actions();

		add_action( 'admin_menu', array( &$this, 'register_menu' ), 200 );
		add_action( 'admin_head', array( &$this, 'admin_head' ) );

		add_action( 'wp_ajax_pcwf_remove_font', array( &$this, 'ajax_pcwf_remove_font' ) );
	}
	
}