<?php
/**
 * Plugin Name: ACZ Slideshow Layers
 * Description: Adds an Elementor nested slideshow widget where every slide is an editable container layer.
 * Version: 1.0.10
 * Author: ACZ
 * Text Domain: acz-slideshow-layers
 * Requires Plugins: elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ACZ_SLIDESHOW_LAYERS_VERSION', '1.0.10' );
define( 'ACZ_SLIDESHOW_LAYERS_FILE', __FILE__ );
define( 'ACZ_SLIDESHOW_LAYERS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACZ_SLIDESHOW_LAYERS_URL', plugin_dir_url( __FILE__ ) );

final class ACZ_Slideshow_Layers_Plugin {
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function init(): void {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'missing_elementor_notice' ] );
			return;
		}

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function register_category( $elements_manager ): void {
		$elements_manager->add_category(
			'acz-elements',
			[
				'title' => esc_html__( 'ACZ Elements', 'acz-slideshow-layers' ),
				'icon' => 'fa fa-plug',
			]
		);
	}

	public function register_styles(): void {
		wp_register_style(
			'acz-slideshow-layers',
			ACZ_SLIDESHOW_LAYERS_URL . 'assets/css/slideshow-layers.css',
			[],
			ACZ_SLIDESHOW_LAYERS_VERSION
		);
	}

	public function register_scripts(): void {
		wp_register_script(
			'acz-slideshow-layers',
			ACZ_SLIDESHOW_LAYERS_URL . 'assets/js/slideshow-layers.js',
			[],
			ACZ_SLIDESHOW_LAYERS_VERSION,
			true
		);
	}

	public function enqueue_editor_scripts(): void {
		wp_enqueue_script(
			'acz-slideshow-layers-editor',
			ACZ_SLIDESHOW_LAYERS_URL . 'assets/js/editor.js',
			[ 'elementor-editor' ],
			ACZ_SLIDESHOW_LAYERS_VERSION,
			true
		);
	}

	public function register_widgets( $widgets_manager ): void {
		if (
			! class_exists( '\Elementor\Widget_Base', false ) ||
			! class_exists( '\Elementor\Modules\NestedElements\Base\Widget_Nested_Base' )
		) {
			add_action( 'admin_notices', [ $this, 'missing_nested_elements_notice' ] );
			return;
		}

		require_once ACZ_SLIDESHOW_LAYERS_PATH . 'includes/widgets/class-acz-slideshow-layers-widget.php';

		$widgets_manager->register( new \ACZ\Slideshow_Layers\Widgets\ACZ_Slideshow_Layers_Widget() );
	}

	public function missing_elementor_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'ACZ Slideshow Layers requires Elementor to be installed and activated.', 'acz-slideshow-layers' );
		echo '</p></div>';
	}

	public function missing_nested_elements_notice(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'ACZ Slideshow Layers requires an Elementor version with Nested Elements support enabled.', 'acz-slideshow-layers' );
		echo '</p></div>';
	}
}

new ACZ_Slideshow_Layers_Plugin();
