<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Models_Sitemaps {

	/**
	 * Get the sitemap index
	 *
	 * @return string
	 */
	public function getSitemapIndex() {
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		$sitemaps = ESQ_Classes_Helpers_Tools::getOption( 'esq_sitemap' );
		if ( ! empty( $sitemaps ) ) {
			foreach ( $sitemaps as $type => $data ) {
				if ( isset( $data[1] ) && $data[1] ) {
					$pages = $this->getTotalPages( str_replace( 'sitemap-', '', $type ) );

					$xml .= '<sitemap>' . "\n";
					$xml .= '<loc>' . esc_url( home_url( '/' ) . $data[0] ) . '</loc>' . "\n";
					$xml .= '<lastmod>' . gmdate( 'Y-m-d\TH:i:sP', current_time( 'timestamp', true ) ) . '</lastmod>' . "\n";
					$xml .= '</sitemap>' . "\n";

					//Add the paged sitemaps (sitemap-products.xml?page=2)
					for ( $page = 2; $page <= $pages; $page ++ ) {
						$xml .= '<sitemap>' . "\n";
						$xml .= '<loc>' . esc_url( home_url( '/' ) . $data[0] . '?page=' . $page ) . '</loc>' . "\n";
						$xml .= '<lastmod>' . gmdate( 'Y-m-d\TH:i:sP', current_time( 'timestamp', true ) ) . '</lastmod>' . "\n";
						$xml .= '</sitemap>' . "\n";
					}
				}
			}
		}

		$xml .= '</sitemapindex>' . "\n";

		return apply_filters( 'esq_sitemap_index', $xml );
	}

	/**
	 * Get the content of an individual sitemap
	 *
	 * @param  string $type posts|pages|products|categories|tags
	 * @param  int    $page The sitemap page number (0 based)
	 *
	 * @return string
	 */
	public function getSitemap( $type, $page = 0 ) {
		//Normalize the type (sitemap-products -> products)
		$type = str_replace( 'sitemap-', '', $type );

		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		switch ( $type ) {
			case 'posts':
				$xml .= $this->getPostUrls( 'post', $page );
				break;
			case 'pages':
				$xml .= $this->getPostUrls( 'page', $page );
				break;
			case 'products':
				$xml .= $this->getPostUrls( 'product', $page );
				break;
			case 'categories':
				$xml .= $this->getTermUrls( 'category' );
				break;
			case 'tags':
				$xml .= $this->getTermUrls( 'post_tag' );
				break;
		}

		$xml .= '</urlset>' . "\n";

		return apply_filters( 'esq_sitemap_' . $type, $xml );
	}

	/**
	 * Get the number of pages for a sitemap type
	 *
	 * @param  string $type posts|pages|products|categories|tags
	 *
	 * @return int
	 */
	public function getTotalPages( $type ) {
		$per_page = (int) ESQ_Classes_Helpers_Tools::getOption( 'esq_sitemap_perpage' );
		if ( $per_page < 1 ) {
			$per_page = 500;
		}

		$total = 0;
		switch ( $type ) {
			case 'posts':
				$count = wp_count_posts( 'post' );
				$total = ( isset( $count->publish ) ? (int) $count->publish : 0 );
				break;
			case 'pages':
				$count = wp_count_posts( 'page' );
				$total = ( isset( $count->publish ) ? (int) $count->publish : 0 );
				break;
			case 'products':
				$count = wp_count_posts( 'product' );
				$total = ( isset( $count->publish ) ? (int) $count->publish : 0 );
				break;
			case 'categories':
				$total = (int) wp_count_terms( 'category' );
				break;
			case 'tags':
				$total = (int) wp_count_terms( 'post_tag' );
				break;
		}

		return ( $total > 0 ? (int) ceil( $total / $per_page ) : 0 );
	}

	/**
	 * Get the URLs for a post type
	 *
	 * @param  string $post_type
	 * @param  int    $page The sitemap page number (0 based)
	 *
	 * @return string
	 */
	private function getPostUrls( $post_type, $page = 0 ) {
		$xml = '';

		$exclude_noindex = ESQ_Classes_Helpers_Tools::getOption( 'esq_sitemap_exclude_noindex' );
		$per_page        = (int) ESQ_Classes_Helpers_Tools::getOption( 'esq_sitemap_perpage' );
		$frequency       = ESQ_Classes_Helpers_Tools::getOption( 'esq_sitemap_frequency' );

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => ( $per_page > 0 ? $per_page : 500 ),
			'paged'          => ( $page > 0 ? $page : 1 ),
			'orderby'        => 'modified',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		);

		$posts = get_posts( $args );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {

				//Exclude the noindex posts
				if ( $exclude_noindex && ESQ_Classes_ObjController::getClass( 'ESQ_Models_Post' )->getPostMeta( $post->ID, 'noindex' ) ) {
					continue;
				}

				$xml .= '<url>' . "\n";
				$xml .= '<loc>' . esc_url( get_permalink( $post ) ) . '</loc>' . "\n";
				$xml .= '<lastmod>' . gmdate( 'Y-m-d\TH:i:sP', strtotime( $post->post_modified_gmt ) ) . '</lastmod>' . "\n";
				$xml .= '<changefreq>' . esc_attr( $frequency ) . '</changefreq>' . "\n";
				$xml .= '<priority>' . ( $post_type == 'post' ? '0.8' : '0.6' ) . '</priority>' . "\n";
				$xml .= '</url>' . "\n";
			}
		}

		return $xml;
	}

	/**
	 * Get the URLs for a taxonomy
	 *
	 * @param  string $taxonomy
	 *
	 * @return string
	 */
	private function getTermUrls( $taxonomy ) {
		$xml = '';

		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		) );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$xml .= '<url>' . "\n";
				$xml .= '<loc>' . esc_url( get_term_link( $term ) ) . '</loc>' . "\n";
				$xml .= '<lastmod>' . gmdate( 'Y-m-d\TH:i:sP', current_time( 'timestamp', true ) ) . '</lastmod>' . "\n";
				$xml .= '<changefreq>monthly</changefreq>' . "\n";
				$xml .= '<priority>0.4</priority>' . "\n";
				$xml .= '</url>' . "\n";
			}
		}

		return $xml;
	}

}