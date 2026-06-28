<?php
namespace ACZ\Slideshow_Layers\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Plugin;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACZ_Slideshow_Layers_Widget extends Widget_Nested_Base {
	private array $slide_item_settings = [];

	public function get_name(): string {
		return 'acz-slideshow-layers';
	}

	public function get_title(): string {
		return esc_html__( 'Layer Slideshow', 'acz-slideshow-layers' );
	}

	public function get_icon(): string {
		return 'eicon-slider-full-screen';
	}

	public function get_categories(): array {
		return [ 'acz-elements' ];
	}

	public function get_keywords(): array {
		return [ 'slider', 'slideshow', 'tabs', 'nested', 'layers', 'container' ];
	}

	public function get_style_depends(): array {
		return [ 'acz-slideshow-layers' ];
	}

	public function get_script_depends(): array {
		return [ 'acz-slideshow-layers' ];
	}

	public function show_in_panel(): bool {
		return Plugin::$instance->experiments->is_feature_active( 'nested-elements', true );
	}

	protected function get_default_repeater_title_setting_key() {
		return 'tab_title';
	}

	protected function get_default_children_title() {
		return esc_html__( 'Slide #%d', 'acz-slideshow-layers' );
	}

	protected function get_default_children_placeholder_selector() {
		return '.acz-slideshow-layers__slides';
	}

	protected function get_html_wrapper_class() {
		return 'elementor-widget-acz-slideshow-layers';
	}

	protected function slide_container( int $index ): array {
		return [
			'elType' => 'container',
			'settings' => [
				'_title' => sprintf(
					/* translators: %d: Slide index. */
					esc_html__( 'Slide #%d', 'acz-slideshow-layers' ),
					$index
				),
				'content_width' => 'full',
				'flex_direction' => 'column',
			],
		];
	}

	protected function get_default_children_elements(): array {
		return [
			$this->slide_container( 1 ),
			$this->slide_container( 2 ),
			$this->slide_container( 3 ),
		];
	}

	protected function register_controls(): void {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', 'acz-slideshow-layers' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => esc_html__( 'Title', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Slide Title', 'acz-slideshow-layers' ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'element_id',
			[
				'label' => esc_html__( 'CSS ID', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::TEXT,
				'ai' => [
					'active' => false,
				],
				'classes' => 'elementor-control-direction-ltr',
				'title' => esc_html__( 'Add a custom ID without the # symbol.', 'acz-slideshow-layers' ),
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => esc_html__( 'Slide Items', 'acz-slideshow-layers' ),
				'type' => Control_Nested_Repeater::CONTROL_TYPE,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => esc_html__( 'Slide #1', 'acz-slideshow-layers' ),
					],
					[
						'tab_title' => esc_html__( 'Slide #2', 'acz-slideshow-layers' ),
					],
					[
						'tab_title' => esc_html__( 'Slide #3', 'acz-slideshow-layers' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
				'button_text' => esc_html__( 'Add Slide', 'acz-slideshow-layers' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Slider Settings', 'acz-slideshow-layers' ),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__( 'Autoplay Speed', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'min' => 1000,
				'step' => 250,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label' => esc_html__( 'Transition Speed', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 350,
				'min' => 0,
				'step' => 50,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => esc_html__( 'Pause on Hover', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label' => esc_html__( 'Arrows', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label' => esc_html__( 'Dots', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label' => esc_html__( 'Minimum Height', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', 'em', 'rem' ],
				'default' => [
					'size' => 420,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 120,
						'max' => 1200,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers' => '--acz-slide-min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->register_style_controls();
	}

	private function register_style_controls(): void {
		$this->start_controls_section(
			'section_slide_style',
			[
				'label' => esc_html__( 'Slide Area', 'acz-slideshow-layers' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'slide_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .acz-slideshow-layers__slide',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'slide_border',
				'selector' => '{{WRAPPER}} .acz-slideshow-layers__slide',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'slide_shadow',
				'selector' => '{{WRAPPER}} .acz-slideshow-layers__slide',
			]
		);

		$this->add_responsive_control(
			'slide_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers__slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_arrows_style',
			[
				'label' => esc_html__( 'Arrows', 'acz-slideshow-layers' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'prev_arrow_icon',
			[
				'label' => esc_html__( 'Previous Icon', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'next_arrow_icon',
			[
				'label' => esc_html__( 'Next Icon', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				],
			]
		);

		$this->start_controls_tabs( 'arrows_style_tabs' );

		$this->start_controls_tab(
			'arrows_style_normal',
			[
				'label' => esc_html__( 'Normal', 'acz-slideshow-layers' ),
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => esc_html__( 'Color', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers__arrow' => 'color: {{VALUE}};',
					'{{WRAPPER}} .acz-slideshow-layers__arrow svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label' => esc_html__( 'Background', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers__arrow' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_style_hover',
			[
				'label' => esc_html__( 'Hover', 'acz-slideshow-layers' ),
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label' => esc_html__( 'Color', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers__arrow:hover, {{WRAPPER}} .acz-slideshow-layers__arrow:focus-visible' => 'color: {{VALUE}};',
					'{{WRAPPER}} .acz-slideshow-layers__arrow:hover svg, {{WRAPPER}} .acz-slideshow-layers__arrow:focus-visible svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label' => esc_html__( 'Background', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers__arrow:hover, {{WRAPPER}} .acz-slideshow-layers__arrow:focus-visible' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'arrows_size',
			[
				'label' => esc_html__( 'Icon Size', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 8,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers__arrow' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .acz-slideshow-layers__arrow svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dots_style',
			[
				'label' => esc_html__( 'Dot Pagination', 'acz-slideshow-layers' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_dots' => 'yes',
				],
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label' => esc_html__( 'Position', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'below',
				'options' => [
					'below' => esc_html__( 'Below', 'acz-slideshow-layers' ),
					'top-left' => esc_html__( 'Top Left', 'acz-slideshow-layers' ),
					'top-center' => esc_html__( 'Top Center', 'acz-slideshow-layers' ),
					'top-right' => esc_html__( 'Top Right', 'acz-slideshow-layers' ),
					'bottom-left' => esc_html__( 'Bottom Left', 'acz-slideshow-layers' ),
					'bottom-center' => esc_html__( 'Bottom Center', 'acz-slideshow-layers' ),
					'bottom-right' => esc_html__( 'Bottom Right', 'acz-slideshow-layers' ),
					'left-center' => esc_html__( 'Left Center', 'acz-slideshow-layers' ),
					'right-center' => esc_html__( 'Right Center', 'acz-slideshow-layers' ),
				],
				'selectors_dictionary' => [
					'below' => '--acz-dots-position: static; --acz-dots-top: auto; --acz-dots-right: auto; --acz-dots-bottom: auto; --acz-dots-left: auto; --acz-dots-transform: none; --acz-dots-margin-top: var(--acz-dots-edge-offset); --acz-dots-flex-direction: row; --acz-dots-justify-content: center;',
					'top-left' => '--acz-dots-position: absolute; --acz-dots-top: var(--acz-dots-edge-offset); --acz-dots-right: auto; --acz-dots-bottom: auto; --acz-dots-left: var(--acz-dots-edge-offset); --acz-dots-transform: none; --acz-dots-margin-top: 0; --acz-dots-flex-direction: row; --acz-dots-justify-content: flex-start;',
					'top-center' => '--acz-dots-position: absolute; --acz-dots-top: var(--acz-dots-edge-offset); --acz-dots-right: auto; --acz-dots-bottom: auto; --acz-dots-left: 50%; --acz-dots-transform: translateX(-50%); --acz-dots-margin-top: 0; --acz-dots-flex-direction: row; --acz-dots-justify-content: center;',
					'top-right' => '--acz-dots-position: absolute; --acz-dots-top: var(--acz-dots-edge-offset); --acz-dots-right: var(--acz-dots-edge-offset); --acz-dots-bottom: auto; --acz-dots-left: auto; --acz-dots-transform: none; --acz-dots-margin-top: 0; --acz-dots-flex-direction: row; --acz-dots-justify-content: flex-end;',
					'bottom-left' => '--acz-dots-position: absolute; --acz-dots-top: auto; --acz-dots-right: auto; --acz-dots-bottom: var(--acz-dots-edge-offset); --acz-dots-left: var(--acz-dots-edge-offset); --acz-dots-transform: none; --acz-dots-margin-top: 0; --acz-dots-flex-direction: row; --acz-dots-justify-content: flex-start;',
					'bottom-center' => '--acz-dots-position: absolute; --acz-dots-top: auto; --acz-dots-right: auto; --acz-dots-bottom: var(--acz-dots-edge-offset); --acz-dots-left: 50%; --acz-dots-transform: translateX(-50%); --acz-dots-margin-top: 0; --acz-dots-flex-direction: row; --acz-dots-justify-content: center;',
					'bottom-right' => '--acz-dots-position: absolute; --acz-dots-top: auto; --acz-dots-right: var(--acz-dots-edge-offset); --acz-dots-bottom: var(--acz-dots-edge-offset); --acz-dots-left: auto; --acz-dots-transform: none; --acz-dots-margin-top: 0; --acz-dots-flex-direction: row; --acz-dots-justify-content: flex-end;',
					'left-center' => '--acz-dots-position: absolute; --acz-dots-top: 50%; --acz-dots-right: auto; --acz-dots-bottom: auto; --acz-dots-left: var(--acz-dots-edge-offset); --acz-dots-transform: translateY(-50%); --acz-dots-margin-top: 0; --acz-dots-flex-direction: column; --acz-dots-justify-content: center;',
					'right-center' => '--acz-dots-position: absolute; --acz-dots-top: 50%; --acz-dots-right: var(--acz-dots-edge-offset); --acz-dots-bottom: auto; --acz-dots-left: auto; --acz-dots-transform: translateY(-50%); --acz-dots-margin-top: 0; --acz-dots-flex-direction: column; --acz-dots-justify-content: center;',
				],
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'dots_edge_offset',
			[
				'label' => esc_html__( 'Edge Offset', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'default' => [
					'size' => 14,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers' => '--acz-dots-edge-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_gap',
			[
				'label' => esc_html__( 'Gap', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers' => '--acz-dots-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'dots_color_tabs' );

		$this->start_controls_tab(
			'dots_color_normal',
			[
				'label' => esc_html__( 'Normal', 'acz-slideshow-layers' ),
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => esc_html__( 'Color', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers__dot' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'dots_color_active',
			[
				'label' => esc_html__( 'Active', 'acz-slideshow-layers' ),
			]
		);

		$this->add_control(
			'dots_active_color',
			[
				'label' => esc_html__( 'Color', 'acz-slideshow-layers' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .acz-slideshow-layers__dot.is-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$slides = $settings['tabs'] ?? $settings['slides'] ?? [];
		$widget_number = $this->get_id_int();
		$options = [
			'autoplay' => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplaySpeed' => absint( $settings['autoplay_speed'] ?? 5000 ),
			'transitionSpeed' => absint( $settings['transition_speed'] ?? 350 ),
			'loop' => 'yes' === ( $settings['loop'] ?? '' ),
			'pauseOnHover' => 'yes' === ( $settings['pause_on_hover'] ?? '' ),
		];

		$this->add_render_attribute(
			'slideshow',
			[
				'class' => 'acz-slideshow-layers',
				'data-options' => wp_json_encode( $options ),
			]
		);
		?>
		<div <?php $this->print_render_attribute_string( 'slideshow' ); ?>>
			<div class="acz-slideshow-layers__viewport">
				<div class="acz-slideshow-layers__slides">
					<?php $this->prepare_slide_item_settings( $slides, $widget_number ); ?>
					<?php $this->render_slide_containers( $slides ); ?>
				</div>
				<?php if ( 'yes' === ( $settings['show_arrows'] ?? '' ) ) : ?>
					<button class="acz-slideshow-layers__arrow acz-slideshow-layers__arrow--prev" type="button" aria-label="<?php echo esc_attr__( 'Previous slide', 'acz-slideshow-layers' ); ?>">
						<?php $this->render_arrow_icon( $settings['prev_arrow_icon'] ?? [], '‹' ); ?>
					</button>
					<button class="acz-slideshow-layers__arrow acz-slideshow-layers__arrow--next" type="button" aria-label="<?php echo esc_attr__( 'Next slide', 'acz-slideshow-layers' ); ?>">
						<?php $this->render_arrow_icon( $settings['next_arrow_icon'] ?? [], '›' ); ?>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' === ( $settings['show_dots'] ?? '' ) ) : ?>
				<div class="acz-slideshow-layers__dots" aria-hidden="true">
					<?php foreach ( $slides as $index => $item ) : ?>
						<button class="acz-slideshow-layers__dot<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button" data-slide-index="<?php echo esc_attr( (string) $index ); ?>"></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_arrow_icon( array $icon, string $fallback ): void {
		if ( ! empty( $icon['value'] ) ) {
			Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
			return;
		}

		echo esc_html( $fallback );
	}

	private function prepare_slide_item_settings( array $slides, int $widget_number ): void {
		$this->slide_item_settings = [];

		foreach ( $slides as $index => $item ) {
			$slide_count = $index + 1;
			$title_id = 'acz-slide-title-' . $widget_number . '-' . $slide_count;
			$tab_id = empty( $item['element_id'] ) ? $title_id : sanitize_html_class( $item['element_id'] );

			$this->slide_item_settings[ $index ] = [
				'index' => $index,
				'slide_count' => $slide_count,
				'tab_id' => $tab_id,
				'panel_id' => 'acz-slide-panel-' . $widget_number . '-' . $slide_count,
				'item' => $item,
			];
		}
	}

	private function render_slide_containers( array $slides ): void {
		foreach ( $slides as $index => $item ) {
			if ( isset( $this->slide_item_settings[ $index ] ) ) {
				$this->print_child( $index, $this->slide_item_settings[ $index ] );
			}
		}
	}

	public function print_child( $index, $item_settings = [] ) {
		$children = $this->get_children();
		$child_ids = [];

		foreach ( $children as $child ) {
			$child_ids[] = $child->get_id();
		}

		$add_attribute_to_container = function ( $should_render, $container ) use ( $item_settings, $child_ids ) {
			if ( in_array( $container->get_id(), $child_ids, true ) ) {
				$this->add_attributes_to_container( $container, $item_settings );
			}

			return $should_render;
		};

		add_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container, 10, 3 );

		if ( isset( $children[ $index ] ) ) {
			$children[ $index ]->print_element();
		}

		remove_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container );
	}

	protected function add_attributes_to_container( $container, array $item_settings ): void {
		$is_active = 0 === (int) $item_settings['index'];

		$container->add_render_attribute(
			'_wrapper',
			[
				'id' => $item_settings['panel_id'],
				'class' => 'acz-slideshow-layers__slide' . ( $is_active ? ' is-active' : '' ),
				'role' => 'tabpanel',
				'aria-labelledby' => $item_settings['tab_id'],
				'aria-hidden' => $is_active ? 'false' : 'true',
				'data-slide-index' => $item_settings['index'],
			]
		);
	}

	protected function get_initial_config(): array {
		return array_merge(
			parent::get_initial_config(),
			[
				'support_improved_repeaters' => true,
				'target_container' => [ '.acz-slideshow-layers__slides' ],
				'node' => 'div',
			]
		);
	}

	protected function content_template_single_repeater_item(): void {
		?>
		<#
		view.addRenderAttribute( 'slide-marker', {
			class: 'acz-slideshow-layers__marker',
			'aria-hidden': 'true',
		}, null, true );
		#>
		<div {{{ view.getRenderAttributeString( 'slide-marker' ) }}}></div>
		<?php
	}

	protected function content_template(): void {
		?>
		<#
		const slideItems = settings.tabs || settings.slides || [];
		const options = {
			autoplay: 'yes' === settings.autoplay,
			autoplaySpeed: parseInt( settings.autoplay_speed || 5000, 10 ),
			transitionSpeed: parseInt( settings.transition_speed || 350, 10 ),
			loop: 'yes' === settings.loop,
			pauseOnHover: 'yes' === settings.pause_on_hover,
		};
		#>
		<div class="acz-slideshow-layers" data-options='{{ JSON.stringify( options ) }}'>
			<div class="acz-slideshow-layers__viewport">
				<div class="acz-slideshow-layers__slides"></div>
				<# if ( 'yes' === settings.show_arrows ) { #>
					<#
					const prevIcon = elementor.helpers.renderIcon( view, settings.prev_arrow_icon, { 'aria-hidden': true }, 'i', 'object' );
					const nextIcon = elementor.helpers.renderIcon( view, settings.next_arrow_icon, { 'aria-hidden': true }, 'i', 'object' );
					#>
					<button class="acz-slideshow-layers__arrow acz-slideshow-layers__arrow--prev" type="button" aria-label="<?php echo esc_attr__( 'Previous slide', 'acz-slideshow-layers' ); ?>">
						{{{ prevIcon.value || '‹' }}}
					</button>
					<button class="acz-slideshow-layers__arrow acz-slideshow-layers__arrow--next" type="button" aria-label="<?php echo esc_attr__( 'Next slide', 'acz-slideshow-layers' ); ?>">
						{{{ nextIcon.value || '›' }}}
					</button>
				<# } #>
			</div>
			<# if ( 'yes' === settings.show_dots ) { #>
				<div class="acz-slideshow-layers__dots" aria-hidden="true">
					<# _.each( slideItems, function( item, index ) { #>
						<button class="acz-slideshow-layers__dot{{ 0 === index ? ' is-active' : '' }}" type="button" data-slide-index="{{ index }}"></button>
					<# } ); #>
				</div>
			<# } #>
		</div>
		<?php
	}
}
