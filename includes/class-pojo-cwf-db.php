<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_CWF_DB {
	
	protected $_fonts = null;

	protected function _default_args( $fonts ) {
		return wp_parse_args(
			$fonts,
			array(
				'font_woff' => '',
				'font_ttf' => '',
				'font_svg' => '',
				'font_eot' => '',
			)
		);
	}

	protected function _register_taxonomy() {
		// Taxonomy: pojo_custom_fonts.
		$labels = array(
			'name' => __( 'Custom Fonts', 'pojo-cwf' ),
			'singular_name' => __( 'Font', 'pojo-cwf' ),
			'menu_name' => _x( 'Custom Fonts', 'Admin menu name', 'pojo-cwf' ),
			'search_items' => __( 'Search Fonts', 'pojo-cwf' ),
			'all_items' => __( 'All Fonts', 'pojo-cwf' ),
			'parent_item' => __( 'Parent Font', 'pojo-cwf' ),
			'parent_item_colon' => __( 'Parent Font:', 'pojo-cwf' ),
			'edit_item' => __( 'Edit Font', 'pojo-cwf' ),
			'update_item' => __( 'Update Font', 'pojo-cwf' ),
			'add_new_item' => __( 'Add New Font', 'pojo-cwf' ),
			'new_item_name' => __( 'New Font Name', 'pojo-cwf' ),
		);
		
		$args = array(
			'hierarchical' => false,
			'labels' => $labels,
			'public' => false,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'capabilities' => array( 'edit_theme_options' ),
			'query_var' => false,
			'rewrite' => false,
		);
		
		register_taxonomy(
			'pojo_custom_fonts',
			apply_filters( 'pojo_taxonomy_objects_custom_fonts', array() ),
			apply_filters( 'pojo_taxonomy_args_custom_fonts', $args )
		);
	}

	public function get_fonts() {
		if ( is_null( $this->_fonts ) ) {
			$this->_fonts = array();
			
			$terms = get_terms(
				'pojo_custom_fonts',
				array(
					'hide_empty' => false,
				)
			);
			
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$this->_fonts[ $term->term_id ] = $term;
					$this->_fonts[ $term->term_id ]->links = $this->get_font_links( $term->term_id );
				}
			}
		}
		
		return $this->_fonts;
	}

	public function has_fonts() {
		$fonts = $this->get_fonts();
		return ! empty( $fonts );
	}

	public function get_font_links( $term_id ) {
		$links = get_option( "taxonomy_pojo_custom_fonts_{$term_id}", array() );
		return $this->_default_args( $links );
	}

	public function update_font_links( $posted, $term_id ) {
		$links = $this->get_font_links( $term_id );
		
		foreach ( array_keys( $links ) as $key ) {
			if ( isset( $posted[ $key ] ) )
				$links[ $key ] = $posted[ $key ];
			else
				$links[ $key ] = '';
		}
		update_option( "taxonomy_pojo_custom_fonts_{$term_id}", $links );
	}
	
	public function __construct() {
		$this->_register_taxonomy();
	}

}