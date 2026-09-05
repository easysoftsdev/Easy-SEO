<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Controllers_PostsList extends ESQ_Classes_FrontController {

	/** @var ESQ_Models_PostsList */
	public $model;

	/**
	 * Hook the Posts List columns
	 */
	public function hookInit() {

		//Add the columns to the post type lists
		$types = get_post_types( array( 'public' => true ) );
		if ( ! empty( $types ) ) {
			foreach ( $types as $type ) {
				if ( ESQ_Classes_ObjController::getClass( 'ESQ_Models_Post' )->isSnippetEnable( $type ) ) {
					add_filter( 'manage_' . $type . '_posts_columns', array( $this, 'addColumns' ) );
					add_action( 'manage_' . $type . '_posts_custom_column', array( $this, 'hookColumnContent' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Add the SEO columns in the posts list
	 *
	 * @param  array $columns
	 *
	 * @return array
	 */
	public function addColumns( $columns ) {
		$columns = array_merge( $columns, $this->model->getColumns() );

		return $columns;
	}

	/**
	 * Output the column content
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 *
	 * @return void
	 */
	public function hookColumnContent( $column, $post_id ) {
		$this->model->getColumnContent( $column, $post_id );
	}

}