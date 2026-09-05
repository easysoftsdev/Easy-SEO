<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );
/* @var $view ESQ_Controllers_Post */

global $post;

$post_model = ESQ_Classes_ObjController::getClass( 'ESQ_Models_Post' );
$metas      = $post_model->getPostMeta( $post->ID );
$title_max  = ESQ_Classes_Helpers_Tools::getOption( 'esq_metas' )['title_maxlength'];
$desc_max   = ESQ_Classes_Helpers_Tools::getOption( 'esq_metas' )['description_maxlength'];

$current_title = isset( $metas['title'] ) ? $metas['title'] : '';
$current_desc  = isset( $metas['description'] ) ? $metas['description'] : '';
$current_kw    = isset( $metas['keyword'] ) ? $metas['keyword'] : '';
$current_canon = isset( $metas['canonical'] ) ? $metas['canonical'] : '';
$current_noindex  = isset( $metas['noindex'] ) ? (int) $metas['noindex'] : 0;
$current_nofollow = isset( $metas['nofollow'] ) ? (int) $metas['nofollow'] : 0;
$og_title    = isset( $metas['og_title'] ) ? $metas['og_title'] : '';
$og_desc     = isset( $metas['og_description'] ) ? $metas['og_description'] : '';
$og_image    = isset( $metas['og_image'] ) ? $metas['og_image'] : '';

$display_title   = $current_title !== '' ? $current_title : get_the_title( $post->ID );
$display_url     = home_url( $_SERVER['REQUEST_URI'] );
$display_desc    = $current_desc !== '' ? $current_desc : wp_trim_words( wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : $post->post_content ), 30, '...' );
?>
<div class="easyseo-metabox">

	<?php ESQ_Classes_Helpers_Tools::setNonce( 'esq_save_post', 'esq_nonce' ); ?>

	<!-- Google Snippet Preview -->
	<div class="easyseo-preview">
		<div class="easyseo-preview__label">
			<span class="dashicons dashicons-search"></span>
			<?php esc_html_e( 'Search Engine Preview', 'easy-seo' ); ?>
		</div>
		<div class="easyseo-preview__title" id="easyseo-live-title"><?php echo esc_html( $display_title ? $display_title : __( 'Page Title', 'easy-seo' ) ); ?></div>
		<div class="easyseo-preview__url"><?php echo esc_html( $display_url ); ?></div>
		<div class="easyseo-preview__desc" id="easyseo-live-desc"><?php echo esc_html( $display_desc ? $display_desc : __( 'No description set. Add one below.', 'easy-seo' ) ); ?></div>
	</div>

	<!-- Tabs -->
	<div class="easyseo-tabs" role="tablist">
		<button type="button" class="easyseo-tab active" data-tab="general" role="tab">
			<span class="dashicons dashicons-edit"></span>
			<?php esc_html_e( 'General', 'easy-seo' ); ?>
		</button>
		<button type="button" class="easyseo-tab" data-tab="social" role="tab">
			<span class="dashicons dashicons-share"></span>
			<?php esc_html_e( 'Social', 'easy-seo' ); ?>
		</button>
		<button type="button" class="easyseo-tab" data-tab="advanced" role="tab">
			<span class="dashicons dashicons-admin-generic"></span>
			<?php esc_html_e( 'Advanced', 'easy-seo' ); ?>
		</button>
	</div>

	<!-- General Tab -->
	<div class="easyseo-section active" data-panel="general">

		<div class="easyseo-field">
			<label for="easyseo_title" class="easyseo-label">
				<?php esc_html_e( 'SEO Title', 'easy-seo' ); ?>
			</label>
			<input type="text" id="easyseo_title" name="esq[title]" value="<?php echo esc_attr( $current_title ); ?>" maxlength="200" placeholder="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>" data-preview="title" />
			<div class="easyseo-field__footer">
				<p class="easyseo-hint"><?php printf( esc_html__( 'Recommended: %d characters. Currently used:', 'easy-seo' ), (int) $title_max ); ?></p>
				<span class="easyseo-char-count" data-counter="easyseo_title" data-max="<?php echo (int) $title_max; ?>">0</span>
				<div class="easyseo-char-bar"><div class="easyseo-char-bar__fill" data-bar="easyseo_title"></div></div>
			</div>
		</div>

		<div class="easyseo-field">
			<label for="easyseo_description" class="easyseo-label">
				<?php esc_html_e( 'Meta Description', 'easy-seo' ); ?>
			</label>
			<textarea id="easyseo_description" name="esq[description]" rows="3" placeholder="<?php esc_attr_e( 'Enter a compelling description for search engines...', 'easy-seo' ); ?>" data-preview="description"><?php echo esc_textarea( $current_desc ); ?></textarea>
			<div class="easyseo-field__footer">
				<p class="easyseo-hint"><?php printf( esc_html__( 'Recommended: %d characters. Currently used:', 'easy-seo' ), (int) $desc_max ); ?></p>
				<span class="easyseo-char-count" data-counter="easyseo_description" data-max="<?php echo (int) $desc_max; ?>">0</span>
				<div class="easyseo-char-bar"><div class="easyseo-char-bar__fill" data-bar="easyseo_description"></div></div>
			</div>
		</div>

		<div class="easyseo-field">
			<label for="easyseo_keyword" class="easyseo-label">
				<?php esc_html_e( 'Focus Keyword', 'easy-seo' ); ?>
			</label>
			<input type="text" id="easyseo_keyword" name="esq[keyword]" value="<?php echo esc_attr( $current_kw ); ?>" placeholder="<?php esc_attr_e( 'e.g. polo shirt', 'easy-seo' ); ?>" />
			<p class="easyseo-hint"><?php esc_html_e( 'The main keyword this page should rank for.', 'easy-seo' ); ?></p>
		</div>

	</div>

	<!-- Social Tab -->
	<div class="easyseo-section" data-panel="social">

		<h4 class="easyseo-section-title">
			<span class="dashicons dashicons-facebook-alt"></span>
			<?php esc_html_e( 'Facebook / Open Graph', 'easy-seo' ); ?>
		</h4>

		<div class="easyseo-field">
			<label for="easyseo_og_title" class="easyseo-label">
				<?php esc_html_e( 'OG Title', 'easy-seo' ); ?>
			</label>
			<input type="text" id="easyseo_og_title" name="esq[og_title]" value="<?php echo esc_attr( $og_title ); ?>" placeholder="<?php esc_attr_e( 'Defaults to SEO Title', 'easy-seo' ); ?>" />
		</div>

		<div class="easyseo-field">
			<label for="easyseo_og_desc" class="easyseo-label">
				<?php esc_html_e( 'OG Description', 'easy-seo' ); ?>
			</label>
			<textarea id="easyseo_og_desc" name="esq[og_description]" rows="2" placeholder="<?php esc_attr_e( 'Defaults to Meta Description', 'easy-seo' ); ?>"><?php echo esc_textarea( $og_desc ); ?></textarea>
		</div>

		<div class="easyseo-field">
			<label for="easyseo_og_image" class="easyseo-label">
				<?php esc_html_e( 'OG Image URL', 'easy-seo' ); ?>
			</label>
			<input type="url" id="easyseo_og_image" name="esq[og_image]" value="<?php echo esc_attr( $og_image ); ?>" placeholder="https://" />
			<p class="easyseo-hint"><?php esc_html_e( 'Leave empty to use the featured image or the default from Settings.', 'easy-seo' ); ?></p>
		</div>

	</div>

	<!-- Advanced Tab -->
	<div class="easyseo-section" data-panel="advanced">

		<h4 class="easyseo-section-title">
			<span class="dashicons dashicons-admin-links"></span>
			<?php esc_html_e( 'Canonical URL', 'easy-seo' ); ?>
		</h4>

		<div class="easyseo-field">
			<label for="easyseo_canonical" class="easyseo-label">
				<?php esc_html_e( 'Canonical URL', 'easy-seo' ); ?>
			</label>
			<input type="url" id="easyseo_canonical" name="esq[canonical]" value="<?php echo esc_attr( $current_canon ); ?>" placeholder="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>" />
			<p class="easyseo-hint"><?php esc_html_e( 'Leave empty to use the default URL.', 'easy-seo' ); ?></p>
		</div>

		<hr class="easyseo-divider" />

		<h4 class="easyseo-section-title">
			<span class="dashicons dashicons-visibility"></span>
			<?php esc_html_e( 'Robots', 'easy-seo' ); ?>
		</h4>

		<div class="easyseo-field">
			<div class="easyseo-checkbox-group">
				<label class="easyseo-checkbox">
					<input type="checkbox" name="esq[noindex]" value="1" <?php checked( $current_noindex, 1 ); ?> />
					<?php esc_html_e( 'No Index', 'easy-seo' ); ?>
				</label>
				<label class="easyseo-checkbox">
					<input type="checkbox" name="esq[nofollow]" value="1" <?php checked( $current_nofollow, 1 ); ?> />
					<?php esc_html_e( 'No Follow', 'easy-seo' ); ?>
				</label>
			</div>
			<p class="easyseo-hint" style="margin-top:6px;"><?php esc_html_e( 'No Index: prevent this page from appearing in search results. No Follow: prevent search engines from following links on this page.', 'easy-seo' ); ?></p>
		</div>

	</div>

</div>

<script type="text/javascript">
(function () {
	/* ---- Tab Switching ---- */
	var tabs = document.querySelectorAll('.easyseo-tab');
	[].forEach.call(tabs, function (tab) {
		tab.addEventListener('click', function () {
			var target = this.getAttribute('data-tab');
			/* deactivate all */
			[].forEach.call(document.querySelectorAll('.easyseo-tab'), function (t) { t.classList.remove('active'); });
			[].forEach.call(document.querySelectorAll('.easyseo-section'), function (s) { s.classList.remove('active'); });
			/* activate clicked */
			this.classList.add('active');
			var panel = document.querySelector('.easyseo-section[data-panel="' + target + '"]');
			if (panel) panel.classList.add('active');
		});
	});

	/* ---- Character Counters ---- */
	var counters = document.querySelectorAll('[data-counter]');
	[].forEach.call(counters, function (el) {
		var id    = el.getAttribute('data-counter');
		var max   = parseInt(el.getAttribute('data-max'), 10) || 160;
		var input = document.getElementById(id);
		var bar   = document.querySelector('[data-bar="' + id + '"]');
		if (!input) return;

		var update = function () {
			var len = input.value.length;
			el.textContent = len + ' / ' + max;

			/* color class */
			el.className = 'easyseo-char-count';
			if (bar) bar.className = 'easyseo-char-bar__fill';
			if (len <= max * 0.7) {
				el.classList.add('good');
				if (bar) bar.classList.add('good');
			} else if (len <= max) {
				el.classList.add('warning');
				if (bar) bar.classList.add('warning');
			} else {
				el.classList.add('danger');
				if (bar) bar.classList.add('danger');
			}

			/* bar width */
			if (bar) {
				var pct = Math.min((len / max) * 100, 100);
				bar.style.width = pct + '%';
			}
		};

		input.addEventListener('input', update);
		update();
	});

	/* ---- Live Snippet Preview ---- */
	var titleInput    = document.getElementById('easyseo_title');
	var descInput     = document.getElementById('easyseo_description');
	var liveTitle     = document.getElementById('easyseo-live-title');
	var liveDesc      = document.getElementById('easyseo-live-desc');
	var postTitle     = '<?php echo esc_js( get_the_title( $post->ID ) ); ?>';

	function updatePreview() {
		if (liveTitle) {
			var t = titleInput && titleInput.value ? titleInput.value : postTitle;
			liveTitle.textContent = t || '<?php echo esc_js( __( 'Page Title', 'easy-seo' ) ); ?>';
			liveTitle.className = 'easyseo-preview__title' + (t ? '' : ' empty');
		}
		if (liveDesc) {
			var d = descInput ? descInput.value : '';
			liveDesc.textContent = d || '<?php echo esc_js( __( 'No description set. Add one below.', 'easy-seo' ) ); ?>';
			liveDesc.className = 'easyseo-preview__desc' + (d ? '' : ' empty');
		}
	}
	if (titleInput) titleInput.addEventListener('input', updatePreview);
	if (descInput)  descInput.addEventListener('input', updatePreview);
	updatePreview();
})();
</script>
