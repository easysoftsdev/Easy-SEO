<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Models_SeoSettings {

	/**
	 * Get the settings tabs
	 *
	 * @return array
	 */
	public function getTabs() {
		return ESQ_Classes_ObjController::getClass( 'ESQ_Models_Menu' )->getTabs( 'esq_seosettings' );
	}

	/**
	 * Validate and normalize the settings submitted from the form
	 *
	 * @param  array $params
	 *
	 * @return array
	 */
	public function sanitizeSettings( $params ) {
		$options = ESQ_Classes_Helpers_Tools::getOptions();

		if ( ! is_array( $params ) ) {
			return $options;
		}

		//General
		if ( isset( $params['esq_title_separator'] ) ) {
			$options['esq_title_separator'] = sanitize_key( $params['esq_title_separator'] );
		}
		if ( isset( $params['esq_home_title'] ) ) {
			$options['esq_home_title'] = sanitize_text_field( $params['esq_home_title'] );
		}
		if ( isset( $params['esq_home_description'] ) ) {
			$options['esq_home_description'] = sanitize_textarea_field( $params['esq_home_description'] );
		}
		if ( isset( $params['esq_google_wt'] ) ) {
			$options['esq_google_wt'] = sanitize_text_field( $params['esq_google_wt'] );
		}
		if ( isset( $params['esq_bing_wt'] ) ) {
			$options['esq_bing_wt'] = sanitize_text_field( $params['esq_bing_wt'] );
		}

		//Sitemap
		if ( isset( $params['esq_auto_sitemap'] ) ) {
			$options['esq_auto_sitemap'] = 1;
		} else {
			$options['esq_auto_sitemap'] = 0;
		}
		if ( isset( $params['esq_sitemap_ping'] ) ) {
			$options['esq_sitemap_ping'] = 1;
		} else {
			$options['esq_sitemap_ping'] = 0;
		}
		if ( isset( $params['esq_sitemap_exclude_noindex'] ) ) {
			$options['esq_sitemap_exclude_noindex'] = 1;
		} else {
			$options['esq_sitemap_exclude_noindex'] = 0;
		}
		if ( isset( $params['esq_sitemap_frequency'] ) ) {
			$options['esq_sitemap_frequency'] = sanitize_key( $params['esq_sitemap_frequency'] );
		}
		if ( isset( $params['esq_sitemap_perpage'] ) && (int) $params['esq_sitemap_perpage'] > 0 ) {
			$options['esq_sitemap_perpage'] = (int) $params['esq_sitemap_perpage'];
		}
		if ( isset( $params['esq_sitemap'] ) && is_array( $params['esq_sitemap'] ) ) {
			foreach ( $options['esq_sitemap'] as $type => $data ) {
				$options['esq_sitemap'][ $type ][1] = ( isset( $params['esq_sitemap'][ $type ] ) ? 1 : 0 );
			}
		}

		//Social
		if ( isset( $params['esq_og_locale'] ) ) {
			$options['esq_og_locale'] = sanitize_text_field( $params['esq_og_locale'] );
		}
		if ( isset( $params['esq_og_image'] ) ) {
			$options['esq_og_image'] = esc_url_raw( $params['esq_og_image'] );
		}
		if ( isset( $params['esq_tc_image'] ) ) {
			$options['esq_tc_image'] = esc_url_raw( $params['esq_tc_image'] );
		}
		if ( isset( $params['esq_socials'] ) && is_array( $params['esq_socials'] ) ) {
			foreach ( $options['esq_socials'] as $key => $value ) {
				if ( isset( $params['esq_socials'][ $key ] ) ) {
					$options['esq_socials'][ $key ] = ( $key == 'twitter_card_type' ) ? sanitize_key( $params['esq_socials'][ $key ] ) : sanitize_text_field( $params['esq_socials'][ $key ] );
				}
			}
		}

		return apply_filters( 'esq_sanitize_settings', $options, $params );
	}

}