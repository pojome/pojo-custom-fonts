<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_CWF_Admin_UI {

	protected $_menu_parent = 'pojo-home';
	protected $_capability = 'edit_theme_options';

	public function register_menu() {
		add_submenu_page(
			$this->_menu_parent,
			__( 'Custom Fonts', 'pojo-cwf' ),
			__( 'Custom Fonts', 'pojo-cwf' ),
			$this->_capability,
			'edit-tags.php?taxonomy=pojo_custom_fonts'
		);
	}

	public function menu_highlight() {
		global $parent_file, $submenu_file;

		if ( 'edit-tags.php?taxonomy=pojo_custom_fonts' === $submenu_file ) {
			$parent_file = $this->_menu_parent;
		}

		if ( 'edit-pojo_custom_fonts' !== get_current_screen()->id )
			return;

		?><style>#addtag div.form-field.term-slug-wrap, #edittag tr.form-field.term-slug-wrap { display: none; }
			#addtag div.form-field.term-description-wrap, #edittag tr.form-field.term-description-wrap { display: none; }</style><script>jQuery( document ).ready( function( $ ) {
				var $wrapper = $( '#addtag, #edittag' );
				$wrapper.find( 'tr.form-field.term-name-wrap p, div.form-field.term-name-wrap > p' ).text( '<?php _e( 'The name of the font as it appears in the attached CSS file.', 'pojo-cwf' ); ?>' );
			} );</script><?php
	}

	public function manage_columns( $columns ) {
		$old_columns = $columns;
		$columns = array(
			'cb' => $old_columns['cb'],
			'name' => $old_columns['name'],
			'ID' => __( 'ID', 'pojo-cwf' ),
		);

		return $columns;
	}

	public function sortable_columns( $sortable_columns ) {
		$sortable_columns['ID'] = 'ID';
		return $sortable_columns;
	}

	public function manage_custom_columns( $value, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'ID' :
				$value = '#' . $term_id;
				break;
		}

		return $value;
	}

	public function save_metadata( $term_id ) {
		if ( isset( $_POST['pojo_custom_fonts'] ) ) {
			Pojo_CWF_Main::instance()->db->update_font_links( $_POST['pojo_custom_fonts'], $term_id );
		}
	}
	
	public function extra_new_metadata() {
		$this->_print_image_new_field( 'font_woff', __( 'Font .woff', 'pojo-cwf' ), __( 'Upload the font\'s woff file', 'pojo-cwf' ) );
		$this->_print_image_new_field( 'font_ttf', __( 'Font .ttf', 'pojo-cwf' ), __( 'Upload the font\'s ttf file', 'pojo-cwf' ) );
		$this->_print_image_new_field( 'font_eot', __( 'Font .eot', 'pojo-cwf' ), __( 'Upload the font\'s eot file', 'pojo-cwf' ) );
		$this->_print_image_new_field( 'font_svg', __( 'Font .svg', 'pojo-cwf' ), __( 'Upload the font\'s svg file', 'pojo-cwf' ) );
	}

	public function extra_edit_metadata( $term ) {
		$data = Pojo_CWF_Main::instance()->db->get_font_links( $term->term_id );

		$this->_print_image_edit_field( 'font_woff', __( 'Font .woff', 'pojo-cwf' ), __( 'Upload the font\'s woff file', 'pojo-cwf' ), $data['font_woff'] );
		$this->_print_image_edit_field( 'font_ttf', __( 'Font .ttf', 'pojo-cwf' ), __( 'Upload the font\'s ttf file', 'pojo-cwf' ), $data['font_ttf'] );
		$this->_print_image_edit_field( 'font_eot', __( 'Font .eot', 'pojo-cwf' ), __( 'Upload the font\'s eot file', 'pojo-cwf' ), $data['font_eot'] );
		$this->_print_image_edit_field( 'font_svg', __( 'Font .svg', 'pojo-cwf' ), __( 'Upload the font\'s svg file', 'pojo-cwf' ), $data['font_svg'] );
	}

	protected function _print_image_new_field( $id, $title, $description, $value = '' ) {
		?>
		<div class="form-field term-<?php echo esc_attr( $id ); ?>-wrap pojo-setting-upload-file-wrap<?php if ( 'font_svg' !== $id ) echo ' form-required'; ?>">
			<label class="pojo-file-upload-label"><?php echo $title; ?></label>
			<input type="text" class="pojo-input-file-upload" name="pojo_custom_fonts[<?php echo esc_attr( $id ); ?>]" placeholder="<?php _e( 'Upload or enter the file URL', 'pojo-cwf' ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php if ( 'font_svg' !== $id ) echo ' required'; ?> />

			<span class="pojo-span-file-upload">
					<a href="javascript:void(0);" data-uploader-title="<?php _e( 'Insert Font', 'pojo-cwf' ); ?>" data-uploader-button-text="<?php _e( 'Insert', 'pojo-cwf' ); ?>" class="pojo-button-file-upload button"><?php _e( 'Upload', 'pojo-cwf' ); ?></a>
				</span>
			<p class="pojo-file-upload-description"><?php echo $description; ?></p>
		</div>
		<?php
	}

	protected function _print_image_edit_field( $id, $title, $description, $value = '' ) {
		?>
		<tr class="form-field term-<?php echo esc_attr( $id ); ?>-wrap pojo-setting-upload-file-wrap<?php if ( 'font_svg' !== $id ) echo ' form-required'; ?>">
			<th scope="row">
				<label for="metadata-<?php echo esc_attr( $id ); ?>">
					<?php echo $title; ?>
				</label>
			</th>
			<td>
				<input id="metadata-<?php echo esc_attr( $id ); ?>" type="text" class="pojo-input-file-upload" name="pojo_custom_fonts[<?php echo esc_attr( $id ); ?>]" placeholder="<?php _e( 'Upload or enter the file URL', 'pojo-cwf' ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php if ( 'font_svg' !== $id ) echo ' required'; ?> />
				<span class="pojo-span-file-upload">
					<a href="javascript:void(0);" data-uploader-title="<?php _e( 'Insert Font', 'pojo-cwf' ); ?>" data-uploader-button-text="<?php _e( 'Insert', 'pojo-cwf' ); ?>" class="pojo-button-file-upload button"><?php _e( 'Upload', 'pojo-cwf' ); ?></a>
				</span>
				<p><?php echo $description; ?></p>
			</td>
		</tr>
		<?php
	}

	public function add_fonts_to_allowed_mimes( $t, $user ) {
		if ( current_user_can( $this->_capability ) ) {
			$t['svg'] = 'image/svg+xml';
			$t['woff'] = 'application/octet-stream';
			$t['eot'] = 'application/vnd.ms-fontobject';
			$t['ttf'] = 'application/x-font-ttf';
		}
		return $t;
	}

	public function __construct() {
		add_action( 'admin_menu', array( &$this, 'register_menu' ), 100 );
		add_action( 'admin_head', array( &$this, 'menu_highlight' ) );

		add_filter( 'manage_edit-pojo_custom_fonts_columns', array( &$this, 'manage_columns' ) );
		add_filter( 'manage_pojo_custom_fonts_custom_column', array( &$this, 'manage_custom_columns' ), 10, 3 );
		add_filter( 'manage_edit-pojo_custom_fonts_sortable_columns', array( &$this, 'sortable_columns' ) );
		
		add_action( 'pojo_custom_fonts_add_form_fields', array( &$this, 'extra_new_metadata' ) );
		add_action( 'pojo_custom_fonts_edit_form_fields', array( &$this, 'extra_edit_metadata' ) );

		add_action( 'edited_pojo_custom_fonts', array( &$this, 'save_metadata' ) );
		add_action( 'create_pojo_custom_fonts', array( &$this, 'save_metadata' ) );
		
		add_filter( 'upload_mimes', array( &$this, 'add_fonts_to_allowed_mimes' ), 10, 2 );
		add_action( 'wp_ajax_pcwf_remove_font', array( &$this, 'ajax_pcwf_remove_font' ) );
	}
	
}
