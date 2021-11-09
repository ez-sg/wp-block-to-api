<?php
/**
 * Plugin Name:       Block To API
 * Description:       Add Gutenberg Block Data into WP API.
 * Author:            Eric Zhao
 * Text Domain:       wp-block-to-api
 * Domain Path:       /languages
 * Version:           0.1.0
 * Requires at least: 5.5
 * Requires PHP:      7.0
 *
 * @package         WP_BLOCK_TO_API
 */

namespace WP_BLOCK_TO_API;

use WP_BLOCK_TO_API\Posts;
use WP_BLOCK_TO_API\Widgets;

if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( ! class_exists( '\pQuery' ) ) {
	/**
	 * Displays an admin notice about why the plugin is unable to load.
	 *
	 * @return void
	 */
	function admin_notice() {
		$message = sprintf(
			/* translators: %s: build commands. */
			__( ' Please run %s to finish installation.', 'wp-block-to-api' ),
			'<code>composer install</code>'
		);
		?>
		<div class="notice notice-error">
			<p><strong><?php esc_html_e( 'REST API Blocks plugin could not be initialized.', 'wp-block-to-api' ); ?></strong></p>
			<p><?php echo wp_kses( $message, [ 'code' => [] ] ); ?></p>
		</div>
		<?php
	}
	add_action( 'admin_notices', __NAMESPACE__ . '\admin_notice' );

	return;
}

require_once __DIR__ . '/src/data.php';
require_once __DIR__ . '/src/posts.php';
require_once __DIR__ . '/src/widgets.php';

Posts\bootstrap();
Widgets\bootstrap();
