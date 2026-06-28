( function ( window ) {
	'use strict';

	const WIDGET_TYPE = 'acz-slideshow-layers';
	const DEFAULT_CHILDREN = [
		{
			elType: 'container',
			settings: {
				_title: 'Slide #1',
				content_width: 'full',
				flex_direction: 'column',
			},
			elements: [],
			isLocked: true,
		},
		{
			elType: 'container',
			settings: {
				_title: 'Slide #2',
				content_width: 'full',
				flex_direction: 'column',
			},
			elements: [],
			isLocked: true,
		},
		{
			elType: 'container',
			settings: {
				_title: 'Slide #3',
				content_width: 'full',
				flex_direction: 'column',
			},
			elements: [],
			isLocked: true,
		},
	];

	function cloneDefaultChildren() {
		return DEFAULT_CHILDREN.map( ( child ) => ( {
			elType: child.elType,
			settings: Object.assign( {}, child.settings ),
			elements: [],
			isLocked: true,
		} ) );
	}

	function patchWidgetCache() {
		if ( ! window.elementor || ! window.elementor.widgetsCache || ! window.elementor.widgetsCache[ WIDGET_TYPE ] ) {
			return false;
		}

		const config = window.elementor.widgetsCache[ WIDGET_TYPE ];

		config.support_nesting = true;
		config.support_improved_repeaters = true;
		config.target_container = [ '.acz-slideshow-layers__slides' ];
		config.node = 'div';
		config.defaults = Object.assign(
			{
				elements: cloneDefaultChildren(),
				elements_title: 'Slide #%d',
				elements_placeholder_selector: '.acz-slideshow-layers__slides',
				child_container_placeholder_selector: '',
				repeater_title_setting: 'tab_title',
			},
			config.defaults || {}
		);

		if ( ! config.defaults.elements || ! config.defaults.elements.length ) {
			config.defaults.elements = cloneDefaultChildren();
		}

		return true;
	}

	function registerNestedElementType() {
		if ( ! patchWidgetCache() ) {
			return false;
		}

		if (
			! window.elementor ||
			! window.elementor.elementsManager ||
			! window.elementor.modules ||
			! window.elementor.modules.elements ||
			! window.elementor.modules.elements.types ||
			! window.elementor.modules.elements.types.NestedElementBase ||
			! window.$e ||
			! window.$e.components
		) {
			return false;
		}

		const nestedComponent = window.$e.components.get( 'nested-elements' );
		const NestedView = nestedComponent && nestedComponent.exports && (
			nestedComponent.exports.NestedView ||
			nestedComponent.exports.NestedViewBase
		);

		if ( ! NestedView ) {
			return false;
		}

		if ( window.elementor.elementsManager.elementTypes[ WIDGET_TYPE ] ) {
			return true;
		}

		class ACZSlideshowView extends NestedView {
			filter( child, index ) {
				child.attributes.dataIndex = index + 1;
				return true;
			}

			onAddChild( childView ) {
				if ( super.onAddChild ) {
					super.onAddChild( childView );
				}

				const widgetRoot = childView._parent.$el.find( '.acz-slideshow-layers' )[ 0 ];
				const index = childView.model.attributes.dataIndex || 1;

				childView.$el.attr( {
					id: `acz-slide-panel-${ widgetRoot ? widgetRoot.dataset.id || childView._parent.model.get( 'id' ) : childView._parent.model.get( 'id' ) }-${ index }`,
					role: 'tabpanel',
					'data-slide-index': index - 1,
				} );

				childView.$el.addClass( 'acz-slideshow-layers__slide' );

				if ( 1 === index && window.elementor.previewView && window.elementor.previewView.isBuffering ) {
					childView.$el.addClass( 'is-active' );
					childView.$el.attr( 'aria-hidden', 'false' );
				} else {
					childView.$el.attr( 'aria-hidden', 'true' );
				}

				const previewWindow = window.elementor.$preview && window.elementor.$preview[ 0 ] && window.elementor.$preview[ 0 ].contentWindow;

				if ( previewWindow && previewWindow.document ) {
					const slideshow = childView._parent.$el.find( '.acz-slideshow-layers' )[ 0 ];

					if ( slideshow ) {
						slideshow.dispatchEvent( new previewWindow.CustomEvent( 'acz-slideshow-layers:refresh', {
							bubbles: true,
						} ) );
					}
				}
			}
		}

		class ACZSlideshowElementType extends window.elementor.modules.elements.types.NestedElementBase {
			getType() {
				return WIDGET_TYPE;
			}

			getView() {
				return ACZSlideshowView;
			}
		}

		try {
			window.elementor.elementsManager.registerElementType( new ACZSlideshowElementType() );
		} catch ( error ) {
			if ( ! /already registered/i.test( error.message || '' ) ) {
				throw error;
			}
		}

		return true;
	}

	function bootWithRetries( attemptsLeft ) {
		if ( registerNestedElementType() || attemptsLeft <= 0 ) {
			return;
		}

		window.setTimeout( () => bootWithRetries( attemptsLeft - 1 ), 150 );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/nested-element-type-loaded elementor:init', () => bootWithRetries( 20 ) );
	}

	bootWithRetries( 20 );
}( window ) );
