<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The configuration file for Easy SEO
 */
if ( ! defined( '_ESQ_NONCE_ID_' ) ) {
	if ( defined( 'NONCE_KEY' ) ) {
		define( '_ESQ_NONCE_ID_', NONCE_KEY );
	} else {
		define( '_ESQ_NONCE_ID_', md5( gmdate( 'Y-d' ) ) );
	}
}

defined( 'ESQ_DEBUG' ) || define( 'ESQ_DEBUG', 0 );
define( 'ESQ_REQUEST_TIME', microtime( true ) );

/* No path file? error ... */
require_once dirname( __FILE__ ) . '/paths.php';

/* Define the record name in the Option and UserMeta tables */
defined( 'ESQ_OPTION' ) || define( 'ESQ_OPTION', 'esq_options' );

$esq_all_separators = array(
	'sc-dash'   => '-',
	'sc-ndash'  => '&ndash;',
	'sc-mdash'  => '&mdash;',
	'sc-middot' => '&middot;',
	'sc-bull'   => '&bull;',
	'sc-star'   => '*',
	'sc-pipe'   => '|',
	'sc-tilde'  => '~',
	'sc-laquo'  => '&laquo;',
	'sc-raquo'  => '&raquo;',
	'sc-lt'     => '&lt;',
	'sc-gt'     => '&gt;',
);

define( 'ESQ_ALL_SEP', wp_json_encode( apply_filters( 'esq_all_sep', $esq_all_separators ) ) );

$esq_all_patterns = array(
	'{{sep}}'         => esc_html__( "Places a separator between the elements of the title", 'easy-seo' ),
	'{{title}}'       => esc_html__( "Adds the title of the post/page once it's published", 'easy-seo' ),
	'{{excerpt}}'     => esc_html__( "Will display an excerpt from the post/page (auto-generated if not set)", 'easy-seo' ),
	'{{keyword}}'     => esc_html__( "Adds the post's keyword to the title", 'easy-seo' ),
	'{{page}}'        => esc_html__( "Displays the number of the current page (i.e. 1 of 6)", 'easy-seo' ),
	'{{sitename}}'    => esc_html__( "Adds the site's name to the title", 'easy-seo' ),
	'{{sitedesc}}'    => esc_html__( "Adds the tagline/description of your site", 'easy-seo' ),
	'{{category}}'    => esc_html__( "Adds the post category", 'easy-seo' ),
	'{{tag}}'         => esc_html__( "Adds the current tag(s)", 'easy-seo' ),
	'{{date}}'        => esc_html__( "Displays the date of the post once it's published", 'easy-seo' ),
	'{{modified}}'    => esc_html__( "Replaces the publication date with the modified one", 'easy-seo' ),
	'{{name}}'        => esc_html__( "Displays the author's nicename", 'easy-seo' ),
	'{{currentdate}}' => esc_html__( "Displays the current date", 'easy-seo' ),
	'{{currentyear}}' => esc_html__( "Adds the current year", 'easy-seo' ),
);

define( 'ESQ_ALL_PATTERNS', wp_json_encode( apply_filters( 'esq_all_patterns', $esq_all_patterns ) ) );