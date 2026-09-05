<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Controllers_Post extends ESQ_Classes_FrontController {

	/** @var ESQ_Models_Post */
	public $model;

	/**
	 * Called from the metabox
	 */
	public function init() {
		$this->show_view( 'Post' );
	}

	/**
	 * Hook the post save action
	 */
	public function hookPost() {

		//Hook and save the SEO Metas for the post
		add_action( 'save_post', array( $this, 'hookSavePost' ), 10, 2 );

		//Refresh the sitemap when a post is saved
		if ( ESQ_Classes_Helpers_Tools::getOption( 'esq_auto_sitemap' ) ) {
			add_action( 'transition_post_status', array(
				ESQ_Classes_ObjController::getClass( 'ESQ_Controllers_Sitemaps' ),
				'refreshSitemap'
			), PHP_INT_MAX, 3 );
		}
	}

	/**
	 * Save the SEO metas from the metabox
	 *
	 * @param  int    $post_id
	 * @param  object $post
	 *
	 * @return void
	 */
	public function hookSavePost( $post_id, $post ) {

		//Ignore the autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		//Ignore the revisions
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		//Check the nonce
		if ( ! isset( $_POST['esq_nonce'] ) || ! wp_verify_nonce( $_POST['esq_nonce'], 'esq_save_post' ) ) {
			return;
		}

		//Check the user capabilities
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['esq'] ) || ! is_array( $_POST['esq'] ) ) {
			return;
		}

		$fields = array(
			'title'       => 'sanitize_text_field',
			'description' => 'sanitize_textarea_field',
			'keyword'     => 'sanitize_text_field',
			'canonical'   => 'esc_url_raw',
			'og_title'    => 'sanitize_text_field',
			'og_description' => 'sanitize_textarea_field',
			'og_image'    => 'esc_url_raw',
		);

		$metas = array_map( 'wp_unslash', $_POST['esq'] );

		foreach ( $fields as $key => $sanitizer ) {
			$value = isset( $metas[ $key ] ) ? $metas[ $key ] : '';
			if ( function_exists( $sanitizer ) ) {
				$value = call_user_func( $sanitizer, $value );
			}
			update_post_meta( $post_id, ESQ_Models_Post::$meta_key . $key, $value );
		}

		//Save the robots settings
		update_post_meta( $post_id, ESQ_Models_Post::$meta_key . 'noindex', ( isset( $metas['noindex'] ) ? 1 : 0 ) );
		update_post_meta( $post_id, ESQ_Models_Post::$meta_key . 'nofollow', ( isset( $metas['nofollow'] ) ? 1 : 0 ) );

	}

}