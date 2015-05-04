<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_CWF_Register {

	public function print_css_fonts() {
		$fonts = Pojo_CWF_Main::instance()->db->get_fonts();

		?><style><?php
		foreach ( $fonts as $font ) :
		$svg_syntax = '';
		if ( ! empty( $font->links['font_svg'] ) )
			$svg_syntax = ", url('" . esc_attr( $font->links['font_svg'] ) . "#{$font->slug}') format('svg')";
		?>
	@font-face {
		font-family: '<?php echo esc_attr( $font->name ); ?>';
		src: url('<?php echo esc_attr( $font->links['font_eot'] ); ?>');
		src: url('<?php echo esc_attr( $font->links['font_eot'] ); ?>?#iefix') format('embedded-opentype'),
		url('<?php echo esc_attr( $font->links['font_woff'] ); ?>') format('woff'),
		url('<?php echo esc_attr( $font->links['font_ttf'] ); ?>') format('truetype')<?php echo $svg_syntax; ?>;
		font-style: normal;
		font-weight: normal;
	}
	<?php endforeach;
		?></style><?php
	}

	public function add_fonts_to_pojo_customizer( $pojo_fonts ) {
		$new_fonts = array();
		foreach ( Pojo_CWF_Main::instance()->db->get_fonts() as $font ) {
			$new_fonts[ $font->name ] = 'local';
		}
		
		if ( ! empty( $new_fonts ) ) {
			$pojo_fonts = array_merge( $new_fonts, $pojo_fonts );
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