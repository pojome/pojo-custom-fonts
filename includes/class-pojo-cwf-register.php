<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_CWF_Register {

	public function print_css_fonts() {
		$fonts = Pojo_CWF_Main::instance()->db->get_fonts();
		
		?><style><?php
		foreach ( $fonts as $font ) : ?>
		@font-face {
			font-family: '<?php echo esc_attr( $font->name ); ?>';
			src: url('<?php echo esc_attr( $font->links['font_eot'] ); ?>');
			src: url('<?php echo esc_attr( $font->links['font_eot'] ); ?>?#iefix') format('embedded-opentype'),
			url('<?php echo esc_attr( $font->links['font_woff'] ); ?>') format('woff'),
			url('<?php echo esc_attr( $font->links['font_ttf'] ); ?>') format('truetype'),
			url('<?php echo esc_attr( $font->links['font_svg'] ); ?>#<?php echo $font->slug; ?>') format('svg');
			font-style: normal;
			font-weight: normal;
		}
	<?php endforeach;
		?></style><?php
	}

	public function add_fonts_to_pojo_customizer( $pojo_fonts ) {
		foreach ( Pojo_CWF_Main::instance()->db->get_fonts() as $font ) {
			$pojo_fonts[ $font->name ] = 'local';
		}
		return $pojo_fonts;
	}

	public function __construct() {
		if ( ! Pojo_CWF_Main::instance()->db->has_fonts() )
			return;
		
		add_action( 'wp_head', array( &$this, 'print_css_fonts' ), 1 );
		add_filter( 'pojo_register_font_faces', array( &$this, 'add_fonts_to_pojo_customizer' ) );
	}

}