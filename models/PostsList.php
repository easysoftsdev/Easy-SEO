<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class ESQ_Models_PostsList {

	/**
	 * The list of columns
	 *
	 * @var array
	 */
	private $columns = array();

	/**
	 * Get the list of the columns added to the posts list
	 *
	 * @return array
	 */
	public function getColumns() {
		if ( empty( $this->columns ) ) {
			$this->columns = array(
				'esq_title'       => esc_html__( 'SEO Title', 'easy-seo' ),
				'esq_description' => esc_html__( 'SEO Description', 'easy-seo' ),
			);
		}

		return $this->columns;
	}

	/**
	 * Get the column content for a post
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 *
	 * @return void
	 */
	public function getColumnContent( $column, $post_id ) {
		$post_model = ESQ_Classes_ObjController::getClass( 'ESQ_Models_Post' );

		switch ( $column ) {
			case 'esq_title':
				if ( $title = $post_model->getPostMeta( $post_id, 'title' ) ) {
					echo '<span style="font-weight:600;">' . esc_html( $title ) . '</span>';
				} else {
					echo '<span style="color:#999;font-style:italic;">' . esc_html__( 'Default', 'easy-seo' ) . '</span>';
				}
				break;

			case 'esq_description':
				if ( $description = $post_model->getPostMeta( $post_id, 'description' ) ) {
					echo '<span style="color:#1d2327;">' . esc_html( wp_trim_words( $description, 12, '...' ) ) . '</span>';
				} else {
					echo '<span style="color:#999;font-style:italic;">' . esc_html__( 'Default', 'easy-seo' ) . '</span>';
				}
				break;
		}
	}

}