<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 17/10/17
 * Time: 10:56 PM
 */

class WPML_LS_Display_As_Translated_Link {

	/** @var SitePress $sitepress */
	private $sitepress;
	/** @var IWPML_URL_Converter_Strategy $url_converter */
	private $url_converter;
	/** @var WP_Query $wp_query */
	private $wp_query;
	/** @var string $default_language */
	private $default_language;

	public function __construct( SitePress $sitepress, IWPML_URL_Converter_Strategy $url_converter, WP_Query $wp_query ) {
		$this->sitepress        = $sitepress;
		$this->url_converter    = $url_converter;
		$this->wp_query         = $wp_query;
		$this->default_language = $sitepress->get_default_language();
	}

	public function get_url( $translations, $lang ) {
		$queried_object = $this->wp_query->get_queried_object();
		if ( $queried_object instanceof WP_Post ) {
			return $this->get_post_url( $translations, $lang, $queried_object );
		} else if ( $queried_object instanceof WP_Term ) {
			return $this->get_term_url( $translations, $lang, $queried_object );
		} else {
			return null;
		}
	}

	private function get_post_url( $translations, $lang, $queried_object ) {
		$url = null;

		if ( $this->sitepress->is_display_as_translated_post_type( $queried_object->post_type ) &&
		     isset( $translations[ $this->default_language ] ) ) {

			$this->sitepress->switch_lang( $this->default_language );
			$url = get_permalink( $translations[ $this->default_language ]->element_id );
			$this->sitepress->switch_lang();
			$url = $this->url_converter->convert_url_string( $url, $lang );
		}

		return $url;
	}

	private function get_term_url( $translations, $lang, $queried_object ) {
		$url = null;

		if ( $this->sitepress->is_display_as_translated_taxonomy( $queried_object->taxonomy ) &&
		     isset( $translations[ $this->default_language ] ) ) {

			$url = get_term_link( (int) $translations[ $this->default_language ]->element_id );
			$url = $this->url_converter->convert_url_string( $url, $lang );
		}

		return $url;
	}


}