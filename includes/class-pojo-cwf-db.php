<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_CWF_DB {

	const SETTING_KEY = 'pojo_custom_web_fonts';

	public function get_fonts() {
		return get_option( self::SETTING_KEY, array() );
	}

	public function has_fonts() {
		$fonts = $this->get_fonts();
		return ! empty( $fonts );
	}

	public function remove_font( $id ) {
		$fonts = $this->get_fonts();
		if ( isset( $fonts[ $id ] ) )
			unset( $fonts[ $id ] );

		update_option( self::SETTING_KEY, $fonts );
	}

	public function update_font( $args, $id = null ) {
		if ( is_null( $id ) )
			$id = uniqid();

		$args = array_map( 'trim', $args );

		$require_fields = array(
			'name',
			'font_eot',
			'font_woff',
			'font_ttf',
			'font_svg',
		);
		foreach ( $require_fields as $field ) {
			if ( empty( $args[ $field ] ) )
				return new WP_Error( 'field_empty', __( 'All fields should be filled in.', 'pojo-cwf' ) );
		}
		$fonts = $this->get_fonts();
		$fonts[ $id ] = $args;

		update_option( self::SETTING_KEY, $fonts );

		return $id;
	}

	public function __construct() {}

}