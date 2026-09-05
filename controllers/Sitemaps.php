<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Controllers_Sitemaps extends ESQ_Classes_FrontController {

	/** @var ESQ_Models_Sitemaps */
	public $model;

	/**
	 * List of the sitemap files
	 *
	 * @var array
	 */
	private $sitemaps = array();

	public function __construct() {
		parent::__construct();

		$this->sitemaps = ESQ_Classes_Helpers_Tools::getOption( 'esq_sitemap' );
	}

	/**
	 * Hook the template_redirect and serve the sitemap files
	 * Called from HookController on template_redirect action
	 */
	public function hookPreload() {

		if ( is_admin() || is_network_admin() ) {
			return;
		}

		$basename = basename( (string) parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );

		if ( $basename == 'sitemap.xml' || $basename == 'sitemap_index.xml' ) {
			$this->showSitemapIndex();

			return;
		}

		if ( $type = $this->getSitemapType( $basename ) ) {
			$this->showSitemap( $type );

			return;
		}
	}

	/**
	 * Get the sitemap type based on the requested file name
	 *
	 * @param  string $basename
	 *
	 * @return string|false
	 */
	private function getSitemapType( $basename ) {
		if ( ! empty( $this->sitemaps ) ) {
			foreach ( $this->sitemaps as $type => $data ) {
				if ( isset( $data[0] ) && $data[0] == $basename ) {
					return $type;
				}
			}
		}

		return false;
	}

	/**
	 * Show the sitemap index
	 *
	 * @return void
	 */
	private function showSitemapIndex() {
		ESQ_Classes_Helpers_Tools::setHeader( 'xml' );
		echo $this->model->getSitemapIndex();
		exit();
	}

	/**
	 * Show an individual sitemap
	 *
	 * @param  string $type
	 *
	 * @return void
	 */
	private function showSitemap( $type ) {
		$page = (int) ESQ_Classes_Helpers_Tools::getValue( 'page', 0 );
		if ( $page < 1 ) {
			$page = 0;
		}

		ESQ_Classes_Helpers_Tools::setHeader( 'xml' );
		echo $this->model->getSitemap( $type, $page );
		exit();
	}

	/**
	 * Refresh the sitemap (clear cache) and ping the search engines
	 * Called from the save_post hook
	 *
	 * @param  string $new_status
	 * @param  string $old_status
	 * @param  object $post
	 *
	 * @return void
	 */
	public function refreshSitemap( $new_status, $old_status, $post ) {
		if ( ! isset( $post->post_type ) || $post->post_type == 'revision' ) {
			return;
		}

		//Ping the search engines when the post is published
		if ( $new_status == 'publish' && $old_status <> 'publish' ) {
			if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_sitemap_ping' ) ) {
				$this->pingSitemap();
			}
		}
	}

	/**
	 * Ping Google and Bing with the new sitemap
	 *
	 * @return void
	 */
	public function pingSitemap() {
		$sitemap_url = home_url( '/sitemap.xml' );

		$urls = array(
			'https://www.google.com/ping?sitemap=' . rawurlencode( $sitemap_url ),
			'https://www.bing.com/ping?sitemap=' . rawurlencode( $sitemap_url ),
		);

		foreach ( $urls as $url ) {
			wp_remote_get( $url, array(
				'timeout'    => 5,
				'blocking'   => false,
				'user-agent' => 'Easy-SEO/1.0',
			) );
		}
	}

}