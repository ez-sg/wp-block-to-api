<?php
/**
 * Posts.
 *
 * @package WP_BLOCK_TO_API.
 */

namespace WP_BLOCK_TO_API\Posts;

use function WP_BLOCK_TO_API\Data\get_blocks;

/**
 * Bootstrap filters and actions.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'rest_api_init', __NAMESPACE__ . '\\WP_BLOCK_TO_API_init' );
}

/**
 * Get post types with editor.
 *
 * @return array
 */
function get_post_types_with_editor() {
	$post_types = get_post_types( [ 'show_in_rest' => true ], 'names' );
	$post_types = array_values( $post_types );

	if ( ! function_exists( 'use_block_editor_for_post_type' ) ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';
	}

	return array_filter( $post_types, 'use_block_editor_for_post_type' );
}

/**
 * Add rest api fields.
 *
 * @return void
 */
function WP_BLOCK_TO_API_init() {
	$types = get_post_types_with_editor();
	if ( ! $types ) {
		return;
	}

	register_rest_field(
		$types,
		'has_blocks',
		[
			'get_callback'    => __NAMESPACE__ . '\\has_blocks_get_callback',
			'update_callback' => null,
			'schema'          => [
				'description' => __( 'Has blocks.', 'wp-block-to-api' ),
				'type'        => 'boolean',
				'context'     => [ 'embed', 'view', 'edit' ],
				'readonly'    => true,
			],
		]
	);

	register_rest_field(
		$types,
		'blocks',
		[
			'get_callback'    => __NAMESPACE__ . '\\blocks_get_callback',
			'update_callback' => null,
			'schema'          => [
				'description' => __( 'Blocks.', 'wp-block-to-api' ),
				'type'        => 'object',
				'context'     => [ 'embed', 'view', 'edit' ],
				'readonly'    => true,
			],
		]
	);
}

/**
 * Callback to get if post content has block data.
 *
 * @param array $object Array of data rest api request.
 *
 * @return bool
 */
function has_blocks_get_callback( array $object ) {
	$post = get_post( $object['id'] );
	if ( ! $post ) {
		return false;
	}

	return has_blocks( $post );
}

/**
 * Loop around all blocks and get block data.
 *
 * @param array $object Array of data rest api request.
 *
 * @return array
 */
function blocks_get_callback( array $object ) {
	$post   = get_post( $object['id'] );
	$output = [];
	if ( ! $post ) {
		return $output;
	}

	return get_blocks( $post->post_content, $post->ID );
}
