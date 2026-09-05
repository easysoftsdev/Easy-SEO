<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Controllers_Frontend extends ESQ_Classes_FrontController {

	/** @var ESQ_Models_Frontend */
	public $model;

	public function __construct() {

		//check again not to load in admin
		if ( is_admin() || is_network_admin() || ESQ_Classes_Helpers_Tools::isAjax() ) {
			return;
		}

		//load the hooks
		parent::__construct();

		/* Check if the sitemap is on and Load the Sitemap */
		if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_auto_sitemap' ) ) {
			add_filter( 'wp_sitemaps_enabled', '__return_false' );
			ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_Sitemaps' );
		}
	}

	/**
	 * Set the current post for the SEO Model
	 * Called from HookController on template_redirect action
	 */
	public function hookPreload() {
		$this->model->setPost();
	}

	/**
	 * Called on init action
	 */
	public function hookInit() {
		//Replace the document title
		if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_auto_title' ) ) {
			add_filter( 'pre_get_document_title', array( $this, 'getTitle' ), PHP_INT_MAX );
			add_filter( 'document_title_parts', array( $this, 'getTitleParts' ), PHP_INT_MAX );
		}
	}

	/**
	 * Hook the wp_head action and output the SEO metas
	 * Called from HookController on wp_head action
	 */
	public function hookFronthead() {
		//Disable the other SEO plugins? (optional, not forced)

		if ( $description = $this->model->getDescription() ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		}

		if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_auto_noindex' ) ) {
			echo '<meta name="robots" content="' . esc_attr( $this->model->getRobots() ) . '" />' . "\n";
		}

		if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_auto_canonical' ) ) {
			echo '<link rel="canonical" href="' . esc_url( $this->model->getCanonical() ) . '" />' . "\n";
		}

		//Webmaster verification
		if ( $google = ESQ_Classes_Helpers_Tools::getOption( 'esq_google_wt' ) ) {
			echo '<meta name="google-site-verification" content="' . esc_attr( $google ) . '" />' . "\n";
		}
		if ( $bing = ESQ_Classes_Helpers_Tools::getOption( 'esq_bing_wt' ) ) {
			echo '<meta name="msvalidate.01" content="' . esc_attr( $bing ) . '" />' . "\n";
		}

		//Open Graph
		if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_auto_facebook' ) ) {
			foreach ( $this->model->getOpenGraph() as $property => $content ) {
				echo '<meta property="' . esc_attr( $property ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
			}
		}

		//Twitter Card
		if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_auto_twitter' ) ) {
			foreach ( $this->model->getTwitterCard() as $property => $content ) {
				echo '<meta name="' . esc_attr( $property ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
			}
		}

		//JSON-LD
		if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_auto_jsonld' ) ) {
			foreach ( $this->model->getJsonLd() as $schema ) {
				echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
			}
		}
	}

	/**
	 * Get the document title from the SEO Model
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->model->getTitle();
	}

	/**
	 * Override the title part from document_title_parts
	 *
	 * @param  array $parts
	 *
	 * @return array
	 */
	public function getTitleParts( $parts ) {
		$parts['title'] = $this->model->getTitle();

		return $parts;
	}

}