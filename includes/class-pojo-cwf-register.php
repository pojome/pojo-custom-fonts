<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_CWF_Register {

	public function print_css_fonts() {
		$fonts = Pojo_CWF_Main::instance()->db->get_fonts();
		
		foreach ( $fonts as $font ) {
			
		}
	}

	public function __construct() {
		if ( ! Pojo_CWF_Main::instance()->db->has_fonts() )
			return;
		
		add_action( 'wp_head', array( &$this, 'print_css_fonts' ), 1 );
	}

}