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
			<h2><?php _e( 'Custom Web Fonts', 'pojo-cwf' ); ?></h2>

			<?php // Add Font ?>
			<div>
				<form action="" method="post">
					<input type="hidden" name="pcwf_action" value="add_font" />
					<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'pcwf-add-font' ) ?>" />
					
					<table class="form-table">
						<tbody>
						<tr>
							<th class="row"><?php _e( 'Name', 'pojo-cwf' ); ?>:</th>
							<td><input type="text" name="name" required /></td>
						</tr>
						
					<?php $this->_print_image_field( 'font_eot', __( 'Font .eot', 'pojo-cwf' ) ); ?>
					<?php $this->_print_image_field( 'font_woff', __( 'Font .woff', 'pojo-cwf' ) ); ?>
					<?php $this->_print_image_field( 'font_ttf', __( 'Font .ttf', 'pojo-cwf' ) ); ?>
					<?php $this->_print_image_field( 'font_svg', __( 'Font .svg', 'pojo-cwf' ) ); ?>
						</tbody>
					</table>
					
					<p class="submit">
						<button type="submit" class="button button-primary"><?php _e( 'Create', 'pojo-cwf' ); ?></button>
					</p>
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

							<table class="form-table">
								<tbody>
								<tr>
									<th class="row"><?php _e( 'Name', 'pojo-cwf' ); ?>:</th>
									<td><input type="text" name="name" value="<?php echo esc_attr( $font_data['name'] ); ?>" required /></td>
								</tr>
								<?php $this->_print_image_field( 'font_eot', __( 'Font .eot', 'pojo-cwf' ), $font_data['font_eot'] ); ?>
								<?php $this->_print_image_field( 'font_woff', __( 'Font .woff', 'pojo-cwf' ), $font_data['font_woff'] ); ?>
								<?php $this->_print_image_field( 'font_ttf', __( 'Font .ttf', 'pojo-cwf' ), $font_data['font_ttf'] ); ?>
								<?php $this->_print_image_field( 'font_svg', __( 'Font .svg', 'pojo-cwf' ), $font_data['font_svg'] ); ?>
								</tbody>
							</table>

							<p class="submit">
								<a href="<?php echo $this->get_remove_font_link( $font_id ); ?>"><?php _e( 'Remove', 'pojo-cwf' ); ?></a>
								<button type="submit" class="button button-primary"><?php _e( 'Create', 'pojo-cwf' ); ?></button>
							</p>
							
						</form>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php _e( 'No have any fonts.', 'pojo-cwf' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function _print_image_field( $id, $title, $value = '' ) {
		?>
		<tr class="pojo-setting-upload-file-wrap">
			<th class="row">
				<?php echo $title; ?>
			</th>
			<td>
				<input type="text" class="pojo-input-file-upload" name="<?php echo esc_attr( $id ); ?>" placeholder="<?php _e( 'Upload or enter the file URL', 'pojo-cwf' ); ?>" value="<?php echo esc_attr( $value ); ?>" required />
				<span class="pojo-span-file-upload">
				<a href="javascript:void(0);" data-uploader-title="<?php _e( 'Insert Font', 'pojo-cwf' ); ?>" data-uploader-button-text="<?php _e( 'Insert', 'pojo-cwf' ); ?>" class="pojo-button-file-upload"><?php _e( 'Upload a Font', 'pojo-cwf' ); ?></a>
			</span>
			</td>
		</tr>
		<?php
	}

	public function add_fonts_to_allowed_mimes( $t, $user ) {
		if ( current_user_can( $this->_capability ) ) {
			$t['svg'] = 'image/svg+xml';
			$t['woff'] = 'application/octet-stream';
			$t['eot'] = 'application/vnd.ms-fontobject';
			$t['ttf'] = 'font/ttf';
		}
		return $t;
	}

	public function __construct() {
		$this->manager_actions();

		add_action( 'admin_menu', array( &$this, 'register_menu' ), 200 );
		add_filter( 'upload_mimes', array( &$this, 'add_fonts_to_allowed_mimes' ), 10, 2 );
		add_action( 'wp_ajax_pcwf_remove_font', array( &$this, 'ajax_pcwf_remove_font' ) );
	}
	
}