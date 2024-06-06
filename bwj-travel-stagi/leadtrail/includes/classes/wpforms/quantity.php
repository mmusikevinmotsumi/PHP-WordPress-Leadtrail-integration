<?php


/**
 * Quantity field.
 *
 * @since 1.0.0
 */
class WPForms_Field_Quantity extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Lead Quantity', 'wpforms' );
		$this->type  = 'lead-quantity';
		$this->icon = 'fa-caret-square-o-down';
		$this->group = 'fancy';
		$this->order    = 50;
		$this->value    = 1;
		$this->defaults = array(
			1 => array(
				'label'   => esc_html__( 5, 'wpforms' ),
				'value'   => 5,
				'default' => '',
			),
			2 => array(
				'label'   => esc_html__( 10, 'wpforms' ),
				'value'   => 10,
				'default' => '',
			),
			3 => array(
				'label'   => esc_html__( 15, 'wpforms' ),
				'value'   => 15,
				'default' => '',
			),
		);
		add_filter( 'wpforms_field_properties_' . $this->type, array( $this, 'field_properties' ), 5, 3 );
	}
	
	public function field_properties( $properties, $field, $form_data ) {

		// Remove primary input.
		unset( $properties['inputs']['primary'] );

		// Define data.
		$form_id  = absint( $form_data['id'] );
		$field_id = absint( $field['id'] );
		$choices  = $field['choices'];
		$dynamic  = wpforms_get_field_dynamic_choices( $field, $form_id, $form_data );

		if ( $dynamic !== false ) {
			$choices              = $dynamic;
			$field['show_values'] = true;
		}

		// Set options container (<select>) properties.
		$properties['input_container'] = array(
			'class' => array(),
			'data'  => array(),
			'id'    => "wpforms-{$form_id}-field_{$field_id}",
			'attr'  => array(
				'name' => "wpforms[fields][{$field_id}]",
			),
		);

		// Set properties.
		foreach ( $choices as $key => $choice ) {

			// Used for dynamic choices.
			$depth = isset( $choice['depth'] ) ? absint( $choice['depth'] ) : 1;

			$properties['inputs'][ $key ] = array(
				'container' => array(
					'attr'  => array(),
					'class' => array( "choice-{$key}", "depth-{$depth}" ),
					'data'  => array(),
					'id'    => '',
				),
				'label'     => array(
					'attr'  => array(
						'for' => "wpforms-{$form_id}-field_{$field_id}_{$key}",
					),
					'class' => array( 'wpforms-field-label-inline' ),
					'data'  => array(),
					'id'    => '',
					'text'  => $choice['label'],
				),
				'attr'      => array(
					'name'  => "wpforms[fields][{$field_id}]",
					'value' => isset( $field['show_values'] ) ? $choice['value'] : $choice['label'],
				),
				'class'     => array(),
				'data'      => array(),
				'id'        => "wpforms-{$form_id}-field_{$field_id}_{$key}",
				'required'  => ! empty( $field['required'] ) ? 'required' : '',
				'default'   => isset( $choice['default'] ),
			);
		}

		// Add class that changes the field size.
		if ( ! empty( $field['size'] ) ) {
			$properties['input_container']['class'][] = 'wpforms-field-' . esc_attr( $field['size'] );
		}

		// Required class for pagebreak validation.
		if ( ! empty( $field['required'] ) ) {
			$properties['input_container']['class'][] = 'wpforms-field-required';
		}

		// Add additional class for container.
		return $properties;
	}


	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data.
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */
		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'basic-options', $field, $args );

		// Label.
		$this->field_option( 'label', $field );
		$this->field_option( 'choices', $field );


		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'basic-options', $field, $args );
		/*
		 * Advanced field options.
		 */

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'advanced-options', $field, $args );

		// Size.
		$this->field_option( 'size', $field );

		// Placeholder.
		$this->field_option( 'placeholder', $field );

		// Default value.
		$this->field_option( 'default_value', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Hide Label.
		$this->field_option( 'label_hide', $field );

		// Options close markup.
		$args = [
			'markup' => 'close',
		];
		$this->field_option( 'advanced-options', $field, $args );
	}
	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 * @since 1.6.1 Added a `Modern` style select support.
	 *
	 * @param array $field Field settings.
	 */
	public function field_preview( $field ) {

		$args = array();
		// Define data.
		$placeholder   = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';
		$default_value = ! empty( $field['default_value'] ) ? $field['default_value'] : '';

		// Label.
		$this->field_preview_option( 'label', $field );

		// Choices.
		$this->field_preview_option( 'choices', $field, $args );

		// Primary input.
		echo '<select  class="primary-input" readonly>
				<option value="1">1</option>
			</select>
		<script>
			jQuery(document).on("input","ul[data-field-type=lead-quantity] .label,ul[data-field-type=lead-quantity] .value",function(e){
				var $this = jQuery(this);
                $this.val($this.val().replace(/[^0-9]/g, "").replace(/(\..*?)\..*/g, "$1"));
			});
		</script>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Converted to a new format, where all the data are taken not from $deprecated, but field properties.
	 * @since 1.6.1 Added a multiple select support.
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated array of field attributes.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		$container         = $field['properties']['input_container'];
		$field_placeholder = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';
		
		//$is_modern         = ! empty( $field['style'] ) && self::STYLE_MODERN === $field['style'];
		$choices           = $field['properties']['inputs'];

		if ( ! $choices ) {
			return;
		}

		if ( ! empty( $field['required'] ) ) {
			$container['attr']['required'] = 'required';
		}

		

		// Add a class for Choices.js initialization.
		/*if ( $is_modern ) {
			$container['class'][] = 'choicesjs-select';

			// Add a size-class to data attribute - it is used when Choices.js is initialized.
			if ( ! empty( $field['size'] ) ) {
				$container['data']['size-class'] = 'wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] );
			}

			$container['data']['search-enabled'] = $this->is_choicesjs_search_enabled( count( $choices ) );
		}*/

		$has_default = false;

		// Check to see if any of the options were selected by default.
		foreach ( $choices as $choice ) {
			if ( ! empty( $choice['default'] ) ) {
				$has_default = true;
				break;
			}
		}
		// Preselect default if no other choices were marked as default.
		printf(
			'<select %s>',
			wpforms_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] )
		);

		// Optional placeholder.
		if ( ! empty( $field_placeholder ) ) {
			printf(
				'<option value="" class="placeholder" disabled %s>%s</option>',
				selected( false, $has_default || $is_multiple, false ),
				esc_html( $field_placeholder )
			);
		}

		// Build the select options.
		foreach ( $choices as $key => $choice ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $choice['attr']['value'] ),
				selected( true, ! empty( $choice['default'] ), false ),
				esc_html( $choice['label']['text'] )
			);
		}

		echo '</select>';
	}

	/**
	 * Format and sanitize field.
	 *
	 * @since 1.0.2
	 * @since 1.6.1 Added a support for multiple values.
	 *
	 * @param int          $field_id     Field ID.
	 * @param string|array $field_submit Submitted field value (selected option).
	 * @param array        $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		$field    = $form_data['fields'][ $field_id ];
		$dynamic  = ! empty( $field['dynamic_choices'] ) ? $field['dynamic_choices'] : false;
		$multiple = ! empty( $field['multiple'] );
		$name     = sanitize_text_field( $field['label'] );
		$value    = [];

		// Convert submitted field value to array.
		if ( ! is_array( $field_submit ) ) {
			$field_submit = array( $field_submit );
		}

		$value_raw = wpforms_sanitize_array_combine( $field_submit );

		$data = array(
			'name'      => $name,
			'value'     => $value_raw,
			'id'        => absint( $field_id ),
			'type'      => $this->type,
		);
		wpforms()->process->fields[ $field_id ] = $data;
	}

	/**
	 * Validate field on form submit.
	 *
	 * @since 1.5.8
	 *
	 * @param int   $field_id     Field ID.
	 * @param mixed $field_submit Submitted value.
	 * @param array $form_data    Form data and settings.
	 */
	/*public function validate( $field_id, $field_submit, $form_data ) {

		$form_id = $form_data['id'];
		$value   = $field_submit;

		// If field is marked as required, check for entry data.
		if (
			! empty( $form_data['fields'][ $field_id ]['required'] ) &&
			empty( $value )  &&
			! is_numeric( $value )
		) {
			wpforms()->process->errors[ $form_id ][ $field_id ] = wpforms_get_required_label();
		}

// Check if value is numeric.
		if ( ! empty( $value )) {
			if ( intval( $value ) != $value ) {
               wpforms()->process->errors[ $form_id ][ $field_id ] = apply_filters( 'wpforms_valid_number_label', esc_html__( 'Please enter a number. Quantity cannot contain decimals.', 'wpforms-lite' ) );
         
            }elseif (! is_numeric( $value )) {
            	wpforms()->process->errors[ $form_id ][ $field_id ] = apply_filters( 'wpforms_valid_number_label', esc_html__( 'Please enter a number.', 'wpforms-lite' ) );
            } 
			
		}
	} */



	
}

new WPForms_Field_Quantity();
