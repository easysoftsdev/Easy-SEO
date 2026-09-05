<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

$options = ESQ_Classes_Helpers_Tools::getOptions();
?>
<table class="form-table" role="presentation">
	<tr>
		<th scope="row"><?php esc_html_e( 'Enable XML Sitemap', 'easy-seo' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="esq[esq_auto_sitemap]" value="1" <?php checked( (int) $options['esq_auto_sitemap'], 1 ); ?> />
				<?php esc_html_e( 'Generate an XML sitemap for your website.', 'easy-seo' ); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'Sitemap Options', 'easy-seo' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="esq[esq_sitemap_ping]" value="1" <?php checked( (int) $options['esq_sitemap_ping'], 1 ); ?> />
				<?php esc_html_e( 'Ping Google and Bing when new content is published.', 'easy-seo' ); ?>
			</label>
			<br />
			<label>
				<input type="checkbox" name="esq[esq_sitemap_exclude_noindex]" value="1" <?php checked( (int) $options['esq_sitemap_exclude_noindex'], 1 ); ?> />
				<?php esc_html_e( 'Exclude noindex posts from the sitemap.', 'easy-seo' ); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_sitemap_frequency"><?php esc_html_e( 'Change Frequency', 'easy-seo' ); ?></label></th>
		<td>
			<select name="esq[esq_sitemap_frequency]" id="esq_sitemap_frequency">
				<?php foreach ( array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' ) as $freq ) : ?>
					<option value="<?php echo esc_attr( $freq ); ?>" <?php selected( $options['esq_sitemap_frequency'], $freq ); ?>><?php echo esc_html( $freq ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="esq_sitemap_perpage"><?php esc_html_e( 'URLs Per Page', 'easy-seo' ); ?></label></th>
		<td>
			<input type="number" name="esq[esq_sitemap_perpage]" id="esq_sitemap_perpage" value="<?php echo esc_attr( (int) $options['esq_sitemap_perpage'] ); ?>" min="1" max="5000" />
			<p class="description"><?php esc_html_e( 'The maximum number of URLs included in each sitemap.', 'easy-seo' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'Sitemap Indexes', 'easy-seo' ); ?></th>
		<td>
			<?php if ( ! empty( $options['esq_sitemap'] ) ) : foreach ( $options['esq_sitemap'] as $type => $data ) : ?>
				<label class="easyseo-sitemap-toggle">
					<input type="checkbox" name="esq[esq_sitemap][<?php echo esc_attr( $type ); ?>]" value="1" <?php checked( ! empty( $data[1] ), true ); ?> />
					<code><?php echo esc_html( isset( $data[0] ) ? $data[0] : $type ); ?></code>
				</label>
			<?php endforeach; endif; ?>
			<p class="description"><?php esc_html_e( 'Enable or disable each sitemap type.', 'easy-seo' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'Sitemap URL', 'easy-seo' ); ?></th>
		<td>
			<a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>" target="_blank"><?php echo esc_html( home_url( '/sitemap.xml' ) ); ?></a>
		</td>
	</tr>
</table>
