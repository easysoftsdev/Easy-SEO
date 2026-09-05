<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

$options = ESQ_Classes_Helpers_Tools::getOptions();
$socials = $options['esq_socials'];
?>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row"><label for="esq_facebook_site"><?php esc_html_e( 'Facebook Page URL', 'easy-seo' ); ?></label></th>
		<td>
			<input type="url" name="esq[esq_socials][facebook_site]" id="esq_facebook_site" value="<?php echo esc_attr( isset( $socials['facebook_site'] ) ? $socials['facebook_site'] : '' ); ?>" class="regular-text" />
			<p class="description"><?php esc_html_e( 'The full URL of your Facebook page, e.g. https://www.facebook.com/yourpage', 'easy-seo' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_twitter_site"><?php esc_html_e( 'Twitter / X Username', 'easy-seo' ); ?></label></th>
		<td>
			<input type="text" name="esq[esq_socials][twitter_site]" id="esq_twitter_site" value="<?php echo esc_attr( isset( $socials['twitter_site'] ) ? $socials['twitter_site'] : '' ); ?>" class="regular-text" placeholder="@username" />
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_twitter_card_type"><?php esc_html_e( 'Twitter Card Type', 'easy-seo' ); ?></label></th>
		<td>
			<select name="esq[esq_socials][twitter_card_type]" id="esq_twitter_card_type">
				<?php foreach ( array( 'summary', 'summary_large_image', 'app', 'player' ) as $card ) : ?>
					<option value="<?php echo esc_attr( $card ); ?>" <?php selected( isset( $socials['twitter_card_type'] ) ? $socials['twitter_card_type'] : 'summary_large_image', $card ); ?>><?php echo esc_html( $card ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_og_locale"><?php esc_html_e( 'Open Graph Locale', 'easy-seo' ); ?></label></th>
		<td>
			<input type="text" name="esq[esq_og_locale]" id="esq_og_locale" value="<?php echo esc_attr( $options['esq_og_locale'] ); ?>" class="regular-text" placeholder="en_US" />
			<p class="description"><?php esc_html_e( 'The locale of your content, e.g. en_US or vi_VN.', 'easy-seo' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_og_image"><?php esc_html_e( 'Default Social Image', 'easy-seo' ); ?></label></th>
		<td>
			<input type="url" name="esq[esq_og_image]" id="esq_og_image" value="<?php echo esc_attr( $options['esq_og_image'] ); ?>" class="regular-text" />
			<p class="description"><?php esc_html_e( 'The default image used on social media when no featured image is set.', 'easy-seo' ); ?></p>
		</td>
	</tr>
</table>
