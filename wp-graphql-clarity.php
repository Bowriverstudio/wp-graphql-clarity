<?php
/**
 * Plugin Name: WP GraphQL Clarity
 * Plugin URI: https://github.com/Bowriverstudio/wp-graphql-clarity
 * GitHub Plugin URI: https://github.com/Bowriverstudio/wp-graphql-clarity
 * Description: GraphQL API for Microsoft Clarity
 * Author: Maurice Tadros
 * Author URI: http://www.bowriverstudio.com
 * Version: 1.0.0
 * Text Domain: wp-graphql-clarity
 * Domain Path: /languages/
 * Requires PHP: 7.1
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use WPGraphQL\AppContext;
/**
 * Ensures core dependencies are active
 *
 * @see https://github.com/ashhitch/wp-graphql-yoast-seo/blob/master/wp-graphql-yoast-seo.php
 */
add_action(
	'admin_init',
	function () {

		$options = get_option( 'active_plugins' );

		$core_dependencies = array(
			'WPGraphQL plugin'             => class_exists( 'WPGraphQL' ),
			'Google Site Kit'              => in_array( 'google-site-kit/google-site-kit.php', $options ),
			'Google Site Kit Dev Settings' => in_array( 'google-site-kit-dev-settings/google-site-kit-dev-settings.php', $options ),
		);

		$missing_dependencies = array_keys(
			array_diff( $core_dependencies, array_filter( $core_dependencies ) )
		);
		$display_admin_notice = static function () use ( $missing_dependencies ) {
			?>
			<div class="notice notice-error">
			  <p>
			  <?php
				esc_html_e(
					'The WPGraphQL Site Kite plugin can\'t be loaded because these dependencies are missing:',
					'wp-graphql-site-kit'
				);
				?>
			  </p>
			  <ul>
				<?php foreach ( $missing_dependencies as $missing_dependency ) : ?>
				  <li><?php echo esc_html( $missing_dependency ); ?></li>
				<?php endforeach; ?>
			  </ul>
			</div>
				<?php
		};

		if ( ! empty( $missing_dependencies ) ) {
			add_action( 'network_admin_notices', $display_admin_notice );
			add_action( 'admin_notices', $display_admin_notice );

			return;
		}
	}
);


add_action(
	'graphql_register_types',
	function() {

		register_graphql_object_type(
			'ClaritySettings',
			array(
				'fields' => array(
					'projectId' => array(
						'type'        => array( 'non_null' => 'String' ),
						'description' => __( 'Settings for Analytics', 'wp-graphql-clarity' ),
					),
				),
			)
		);

		register_graphql_field(
			'RootQuery',
			'ClaritySettings',
			array(
				'type'        => 'ClaritySettings',
				'description' => __( 'Data for Clarity', 'wp-graphql-clarity' ),
				'args'        => array(),
				'resolve'     => function( $root, $args, $context, $info ) {

					$clarity_project_id = get_option( 'clarity_project_id' );
					// graphql_debug( $clarity_project_id, array( 'type' => 'clarity_project_id' ) );
					return array(
						'projectId' => $clarity_project_id,
					);

				},
			)
		);

	}
);
