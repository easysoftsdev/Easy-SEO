<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );
/* @var $view ESQ_Controllers_SeoSettings */

$current_tab = $view->tab;
$tabs        = $view->tabs;
?>
<div class="wrap easyseo-settings">

	<h1><?php esc_html_e( 'Easy SEO Settings', 'easy-seo' ); ?></h1>

	<div id="easyseo-message" class="easyseo-admin-notice" style="display:none;">
		<p></p>
	</div>

	<h2 class="nav-tab-wrapper">
		<?php if ( ! empty( $tabs ) ) : foreach ( $tabs as $id => $tab ) :
			$tab_id = explode( '/', $id );
			$tab_name = isset( $tab_id[1] ) ? $tab_id[1] : 'general';
			?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=esq_seosettings&tab=' . $tab_name ) ); ?>" class="nav-tab <?php echo $current_tab == $tab_name ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_html( $tab['title'] ); ?>
			</a>
		<?php endforeach; endif; ?>
	</h2>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=esq_seosettings&tab=' . $current_tab ) ); ?>" id="easyseo-settings-form">
		<input type="hidden" name="action" value="esq_save_settings" />
		<?php ESQ_Classes_Helpers_Tools::setNonce( 'esq_save_settings', 'esq_nonce' ); ?>

		<div class="easyseo-settings-content">
			<?php
			$tab_view = _ESQ_THEME_DIR_ . 'Settings/' . ucfirst( $current_tab ) . '.php';
			if ( file_exists( $tab_view ) ) {
				include $tab_view;
			} else {
				include _ESQ_THEME_DIR_ . 'Settings/General.php';
			}
			?>
		</div>

		<?php submit_button( esc_html__( 'Save Settings', 'easy-seo' ), 'primary', 'esq-save' ); ?>
	</form>

</div>
<script type="text/javascript">
	(function () {
		var form = document.getElementById('easyseo-settings-form');
		if (!form) return;
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var msg = document.getElementById('easyseo-message');
			var data = new FormData(form);
			data.append('action', 'esq_save_settings');
			fetch(form.getAttribute('action'), {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			}).then(function (r) { return r.json(); }).then(function (res) {
				msg.style.display = 'block';
				msg.querySelector('p').textContent = res.message || '';
				msg.className = 'easyseo-admin-notice ' + (res.success ? 'success' : 'error');
				msg.scrollIntoView({behavior: 'smooth'});
			}).catch(function () {
				msg.style.display = 'block';
				msg.querySelector('p').textContent = '<?php echo esc_js( __( 'An error occurred while saving the settings.', 'easy-seo' ) ); ?>';
				msg.className = 'easyseo-admin-notice error';
			});
			return false;
		});
	})();
</script>
