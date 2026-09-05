<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Models_Frontend {

	/** @var WP_Post|WP_Term|false */
	public $post;

	/** @var string */
	public $context = '';

	/**
	 * Set the current post from the query
	 *
	 * @return void
	 */
	public function setPost() {
		global $post;

		if ( is_home() || is_front_page() ) {
			$this->context = 'home';
		} elseif ( is_singular() && ( $queried = get_queried_object() ) && $queried instanceof WP_Post ) {
			$this->post    = $queried;
			$this->context = 'post';
		} elseif ( is_category() || is_tag() || is_tax() || ( function_exists( 'is_product_cat' ) && is_product_cat() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() ) ) {
			$this->post    = get_queried_object();
			$this->context = 'term';
		} elseif ( is_author() ) {
			$this->post    = get_queried_object();
			$this->context = 'author';
		} elseif ( is_search() ) {
			$this->context = 'search';
		} elseif ( ( function_exists( 'is_shop' ) && is_shop() ) || is_post_type_archive() || is_archive() ) {
			$this->context = 'archive';
		} elseif ( is_404() ) {
			$this->context = '404';
		} elseif ( isset( $post ) && $post instanceof WP_Post ) {
			$this->post    = $post;
			$this->context = 'post';
		}
	}

	/**
	 * Replace the patterns in a string
	 *
	 * @param  string $string
	 *
	 * @return string
	 */
	public function replacePatterns( $string ) {
		$sep_key  = ESQ_Classes_Helpers_Tools::getOption( 'esq_title_separator' );
		$all_sep  = json_decode( ESQ_ALL_SEP, true );
		$sep      = ( isset( $all_sep[ $sep_key ] ) ? $all_sep[ $sep_key ] : '|' );

		$replace = array(
			'{{sep}}'         => $sep,
			'{{sitename}}'    => get_bloginfo( 'name' ),
			'{{sitedesc}}'    => get_bloginfo( 'description' ),
			'{{page}}'        => '',
			'{{date}}'        => ( $this->post && isset( $this->post->post_date ) ? get_the_date( '', $this->post ) : '' ),
			'{{modified}}'    => ( $this->post && isset( $this->post->post_modified ) ? get_the_modified_date( '', $this->post ) : '' ),
			'{{name}}'        => ( $this->post && isset( $this->post->post_author ) ? get_the_author_meta( 'display_name', $this->post->post_author ) : '' ),
			'{{currentdate}}' => current_time( get_option( 'date_format' ) ),
			'{{currentyear}}' => current_time( 'Y' ),
			'{{category}}'    => ( $this->post ? $this->getPostCategories( $this->post ) : '' ),
			'{{tag}}'         => ( $this->post ? $this->getPostTags( $this->post ) : '' ),
			'{{keyword}}'     => ( $this->post ? $this->getPostMeta( $this->post->ID, 'keyword' ) : '' ),
		);

		return str_replace( array_keys( $replace ), array_values( $replace ), $string );
	}

	/**
	 * Get the post categories as a comma separated list
	 *
	 * @param  WP_Post $post
	 *
	 * @return string
	 */
	public function getPostCategories( $post ) {
		$cats = wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) );

		return implode( ', ', $cats );
	}

	/**
	 * Get the post tags as a comma separated list
	 *
	 * @param  WP_Post $post
	 *
	 * @return string
	 */
	public function getPostTags( $post ) {
		$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) );

		return implode( ', ', $tags );
	}

	/**
	 * Get the post meta value
	 *
	 * @param  int    $post_id
	 * @param  string $key
	 *
	 * @return mixed
	 */
	public function getPostMeta( $post_id, $key ) {
		return ESQ_Classes_ObjController::getClass( 'ESQ_Models_Post' )->getPostMeta( $post_id, $key );
	}

	/**
	 * Get the meta title for the current context
	 *
	 * @return string
	 */
	public function getTitle() {
		$title = '';

		switch ( $this->context ) {
			case 'home':
				$title = ESQ_Classes_Helpers_Tools::getOption( 'esq_home_title' );
				break;

			case 'post':
			case 'term':
			case 'author':
				if ( $this->post && ( $meta = $this->getPostMeta( $this->post->ID, 'title' ) ) ) {
					$title = $meta;
				} else {
					$title = '{{title}} {{sep}} {{sitename}}';
				}
				break;

			case 'search':
				$title = sprintf( esc_html__( "Search results for: %s", 'easy-seo' ), get_search_query() ) . ' {{sep}} {{sitename}}';
				break;

			case 'archive':
				$title = wp_get_document_title();
				break;

			case '404':
				$title = esc_html__( "Page not found", 'easy-seo' ) . ' {{sep}} {{sitename}}';
				break;
		}

		//Replace the {{title}} pattern with the real title
		if ( strpos( $title, '{{title}}' ) !== false ) {
			$post_title = ( $this->post && isset( $this->post->post_title ) ) ? $this->post->post_title : '';
			if ( $this->context == 'term' && $this->post instanceof WP_Term ) {
				$post_title = $this->post->name;
			}
			if ( $this->context == 'author' && $this->post instanceof WP_User ) {
				$post_title = $this->post->display_name;
			}
			$title = str_replace( '{{title}}', $post_title, $title );
		}

		//Replace the {{excerpt}} pattern
		if ( strpos( $title, '{{excerpt}}' ) !== false ) {
			$excerpt = ( $this->post && isset( $this->post->post_excerpt ) && $this->post->post_excerpt <> '' ) ? $this->post->post_excerpt : $this->getAutoExcerpt();
			$title   = str_replace( '{{excerpt}}', $excerpt, $title );
		}

		$title = $this->replacePatterns( $title );

		return ESQ_Classes_Helpers_Sanitize::sanitizeTitle( $title, ESQ_Classes_Helpers_Tools::getOption( 'esq_metas' )['title_maxlength'] );
	}

	/**
	 * Get an auto generated excerpt
	 *
	 * @param  int $length
	 *
	 * @return string
	 */
	public function getAutoExcerpt( $length = 155 ) {
		if ( ! $this->post ) {
			return '';
		}

		$content = get_post_field( 'post_content', $this->post->ID );
		$content = trim( wp_strip_all_tags( strip_shortcodes( $content ) ) );
		$content = preg_replace( '/\r\n|\r|\n|[\t ]+/', ' ', $content );

		if ( function_exists( 'mb_substr' ) ) {
			if ( mb_strlen( $content ) > $length ) {
				$content = mb_substr( $content, 0, $length - 3 ) . '...';
			}
		} elseif ( strlen( $content ) > $length ) {
			$content = substr( $content, 0, $length - 3 ) . '...';
		}

		return $content;
	}

	/**
	 * Get the meta description for the current context
	 *
	 * @return string
	 */
	public function getDescription() {
		$description = '';

		switch ( $this->context ) {
			case 'home':
				$description = ESQ_Classes_Helpers_Tools::getOption( 'esq_home_description' );
				break;

			case 'post':
			case 'term':
			case 'author':
				if ( $this->post && ( $meta = $this->getPostMeta( $this->post->ID, 'description' ) ) ) {
					$description = $meta;
				} else {
					$description = '{{excerpt}}';
				}
				break;

			case 'search':
				$description = sprintf( esc_html__( "These are the search results for %s on our website.", 'easy-seo' ), get_search_query() );
				break;

			case 'archive':
				$description = get_the_archive_description();
				break;

			case '404':
				$description = esc_html__( "This page could not be found on our website.", 'easy-seo' );
				break;
		}

		if ( strpos( $description, '{{excerpt}}' ) !== false ) {
			$excerpt = '';
			if ( $this->post && isset( $this->post->post_excerpt ) && $this->post->post_excerpt <> '' ) {
				$excerpt = $this->post->post_excerpt;
			} elseif ( $this->context == 'term' && $this->post instanceof WP_Term ) {
				$excerpt = $this->post->description;
			} elseif ( $this->context == 'author' && $this->post instanceof WP_User ) {
				$excerpt = $this->post->description;
			} else {
				$excerpt = $this->getAutoExcerpt();
			}
			$description = str_replace( '{{excerpt}}', $excerpt, $description );
		}

		$description = $this->replacePatterns( $description );

		return ESQ_Classes_Helpers_Sanitize::sanitizeDescription( $description, ESQ_Classes_Helpers_Tools::getOption( 'esq_metas' )['description_maxlength'] );
	}

	/**
	 * Get the canonical URL for the current context
	 *
	 * @return string
	 */
	public function getCanonical() {
		if ( $this->post && $this->context == 'post' ) {
			return ESQ_Classes_ObjController::getClass( 'ESQ_Models_Post' )->getCanonical( $this->post->ID );
		}

		if ( is_home() || is_front_page() ) {
			return home_url( '/' );
		}

		if ( $this->context == 'term' ) {
			return get_term_link( $this->post );
		}

		if ( $this->context == 'author' ) {
			return get_author_posts_url( $this->post->ID );
		}

		if ( $this->context == 'search' ) {
			return get_search_link();
		}

		return home_url( add_query_arg( array(), $GLOBALS['wp']->request ? $GLOBALS['wp']->request : '/' ) );
	}

	/**
	 * Get the robots meta for the current context
	 *
	 * @return string
	 */
	public function getRobots() {
		$robots = array();

		if ( $this->context == '404' || $this->context == 'search' ) {
			$robots[] = 'noindex';
			$robots[] = 'nofollow';
		}

		if ( $this->post && ( $noindex = $this->getPostMeta( $this->post->ID, 'noindex' ) ) ) {
			$robots[] = 'noindex';
		}
		if ( $this->post && ( $nofollow = $this->getPostMeta( $this->post->ID, 'nofollow' ) ) ) {
			$robots[] = 'nofollow';
		}

		if ( empty( $robots ) ) {
			$robots[] = 'index';
			$robots[] = 'follow';
		}

		return implode( ', ', array_unique( $robots ) );
	}

	/**
	 * Get the Open Graph metas
	 *
	 * @return array
	 */
	public function getOpenGraph() {
		$og = array(
			'og:locale'    => ESQ_Classes_Helpers_Tools::getOption( 'esq_og_locale' ),
			'og:type'      => ( $this->context == 'post' ? 'article' : 'website' ),
			'og:title'     => $this->getTitle(),
			'og:description' => $this->getDescription(),
			'og:url'       => $this->getCanonical(),
			'og:site_name' => get_bloginfo( 'name' ),
		);

		if ( $image = $this->getSocialImage() ) {
			$og['og:image'] = $image;
		}

		return apply_filters( 'esq_opengraph', $og );
	}

	/**
	 * Get the Twitter Card metas
	 *
	 * @return array
	 */
	public function getTwitterCard() {
		$socials = ESQ_Classes_Helpers_Tools::getOption( 'esq_socials' );
		$tc      = array(
			'twitter:card'   => ( isset( $socials['twitter_card_type'] ) ? $socials['twitter_card_type'] : 'summary_large_image' ),
			'twitter:title'  => $this->getTitle(),
			'twitter:description' => $this->getDescription(),
		);

		if ( isset( $socials['twitter_site'] ) && $socials['twitter_site'] <> '' ) {
			$tc['twitter:site'] = $socials['twitter_site'];
		}

		if ( $image = $this->getSocialImage() ) {
			$tc['twitter:image'] = $image;
		}

		return apply_filters( 'esq_twittercard', $tc );
	}

	/**
	 * Get the social image (featured image, post meta or global option)
	 *
	 * @return string
	 */
	public function getSocialImage() {
		$image = '';

		if ( $this->post && $this->context == 'post' ) {
			if ( $meta_image = $this->getPostMeta( $this->post->ID, 'og_image' ) ) {
				$image = $meta_image;
			} elseif ( has_post_thumbnail( $this->post->ID ) ) {
				$image = get_the_post_thumbnail_url( $this->post->ID, 'full' );
			}
		}

		if ( $image == '' ) {
			$image = ESQ_Classes_Helpers_Tools::getOption( 'esq_og_image' );
		}

		return apply_filters( 'esq_social_image', esc_url( $image ) );
	}

	/**
	 * Get the JSON-LD schemas
	 *
	 * @return array
	 */
	public function getJsonLd() {
		$jsonld = array();

		if ( $this->context == 'home' ) {
			$jsonld[] = array(
				'@context'  => 'https://schema.org',
				'@type'     => 'WebSite',
				'name'      => get_bloginfo( 'name' ),
				'url'       => home_url( '/' ),
			);
		} elseif ( $this->context == 'post' && $this->post ) {
			$jsonld[] = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'Article',
				'headline'        => $this->post->post_title,
				'datePublished'   => get_the_date( 'Y-m-d\TH:i:s', $this->post ),
				'dateModified'    => get_the_modified_date( 'Y-m-d\TH:i:s', $this->post ),
				'author'          => array(
					'@type' => 'Person',
					'name'  => get_the_author_meta( 'display_name', $this->post->post_author ),
				),
				'mainEntityOfPage' => get_permalink( $this->post->ID ),
				'publisher'       => array(
					'@type' => 'Organization',
					'name'  => get_bloginfo( 'name' ),
				),
			);
		}

		return apply_filters( 'esq_jsonld', $jsonld );
	}

	/**
	 * Get the current URL of the request
	 *
	 * @return string
	 */
	public function getCurrentUrl() {
		return home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
	}

}