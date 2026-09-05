<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

$options   = ESQ_Classes_Helpers_Tools::getOptions();
$separators = json_decode( ESQ_ALL_SEP, true );
$patterns  = json_decode( ESQ_ALL_PATTERNS, true );
?>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row"><label for="esq_title_separator"><?php esc_html_e( 'Title Separator', 'easy-seo' ); ?></label></th>
		<td>
			<select name="esq[esq_title_separator]" id="esq_title_separator">
				<?php if ( ! empty( $separators ) ) : foreach ( $separators as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $options['esq_title_separator'], $key ); ?>><?php echo esc_html( $value ); ?></option>
				<?php endforeach; endif; ?>
			</select>
			<p class="description"><?php esc_html_e( 'The separator used between the elements of the page title.', 'easy-seo' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_home_title"><?php esc_html_e( 'Homepage Title Pattern', 'easy-seo' ); ?></label></th>
		<td>
			<input type="text" name="esq[esq_home_title]" id="esq_home_title" value="<?php echo esc_attr( $options['esq_home_title'] ); ?>" class="regular-text" />
			<p class="description">
				<?php esc_html_e( 'Available patterns:', 'easy-seo' ); ?>
				<?php if ( ! empty( $patterns ) ) : echo esc_html( implode( ' ', array_keys( $patterns ) ) ); endif; ?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_home_description"><?php esc_html_e( 'Homepage Meta Description', 'easy-seo' ); ?></label></th>
		<td>
			<textarea name="esq[esq_home_description]" id="esq_home_description" rows="3" class="large-text"><?php echo esc_textarea( $options['esq_home_description'] ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Leave empty to use the site tagline.', 'easy-seo' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_google_wt"><?php esc_html_e( 'Google Site Verification', 'easy-seo' ); ?></label></th>
		<td>
			<input type="text" name="esq[esq_google_wt]" id="esq_google_wt" value="<?php echo esc_attr( $options['esq_google_wt'] ); ?>" class="regular-text" />
			<p class="description"><?php esc_html_e( 'The content of the Google Site Verification meta tag.', 'easy-seo' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_bing_wt"><?php esc_html_e( 'Bing Site Verification', 'easy-seo' ); ?></label></th>
		<td>
			<input type="text" name="esq[esq_bing_wt]" id="esq_bing_wt" value="<?php echo esc_attr( $options['esq_bing_wt'] ); ?>" class="regular-text" />
			<p class="description"><?php esc_html_e( 'The content of the Bing Webmaster verification meta tag.', 'easy-seo' ); ?></p>
		</td>
	</tr>
</table>
