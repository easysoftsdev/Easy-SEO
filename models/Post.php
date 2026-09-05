<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Models_Post {

	/**
	 * The prefix for the post metas used by the plugin
	 *
	 * @var string
	 */
	public static $meta_key = '_esq_';

	/**
	 * Get the current post from the query
	 *
	 * @return WP_Post|false|null
	 */
	public function getCurrentPost() {
		global $post;

		if ( isset( $post ) && $post instanceof WP_Post ) {
			return $post;
		}

		if ( is_singular() ) {
			return get_queried_object();
		}

		return false;
	}

	/**
	 * Get the post meta values
	 *
	 * @param  null  $post_id
	 * @param  null  $meta_key
	 *
	 * @return mixed
	 */
	public function getPostMeta( $post_id = null, $meta_key = null ) {
		if ( ! isset( $post_id ) ) {
			if ( $post = $this->getCurrentPost() ) {
				$post_id = $post->ID;
			}
		}

		if ( ! isset( $post_id ) || (int) $post_id == 0 ) {
			return false;
		}

		if ( isset( $meta_key ) ) {
			return get_post_meta( $post_id, self::$meta_key . $meta_key, true );
		}

		$metas = array();
		$all   = get_post_meta( $post_id );
		if ( ! empty( $all ) ) {
			foreach ( $all as $key => $values ) {
				if ( strpos( $key, self::$meta_key ) === 0 ) {
					$metas[ str_replace( self::$meta_key, '', $key ) ] = $values[0];
				}
			}
		}

		return $metas;
	}

	/**
	 * Check if the post type has the SEO options enabled
	 *
	 * @param  string $post_type
	 *
	 * @return bool
	 */
	public function isSnippetEnable( $post_type ) {
		if ( $post_type instanceof WP_Post ) {
			$post_type = $post_type->post_type;
		}

		if ( $post_type == '' ) {
			return false;
		}

		$types = get_post_types( array( 'public' => true ) );
		if ( ! empty( $types ) ) {
			return in_array( $post_type, (array) $types );
		}

		return true;
	}

	/**
	 * Get the canonical URL for a post
	 *
	 * @param  null  $post_id
	 *
	 * @return string
	 */
	public function getCanonical( $post_id = null ) {
		if ( $canonical = $this->getPostMeta( $post_id, 'canonical' ) ) {
			return esc_url( $canonical );
		}

		return get_permalink( $post_id );
	}

}