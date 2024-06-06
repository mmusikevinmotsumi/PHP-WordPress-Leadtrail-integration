<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Form Field - Credit Card Number
 *
 * Add a new "Credit Card Number" field to Elementor form widget.
 *
 * @since 1.0.0
 */
class Elementor_Quantity_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	/**
	 * Get field type.
	 *
	 * Retrieve credit card number field unique ID.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Field type.
	 */
	public function get_type() {
		return 'lead-quantity';
	}

	/**
	 * Get field name.
	 *
	 * Retrieve credit card number field label.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Field name.
	 */
	public function get_name() {
		return esc_html__( 'Lead Quantity', 'elementor-form-quantity-field' );
	}

	
	/**
	 * Render field output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param mixed $item
	 * @param mixed $item_index
	 * @param mixed $form
	 * @return void
	 */
	public function render( $item, $item_index, $form ) {



		
		$form_id = $form->get_id();

		$form->add_render_attribute(
			['select-wrapper' . $item_index => [
					'class' => [
						'elementor-field',
						'elementor-select-wrapper',
						'remove-before',
						esc_attr( $item['css_classes'] ),
					],
			],
			'select' . $item_index=>
			[
				'name' =>$form->get_attribute_name( $item ),
				'id' => $form->get_attribute_id( $item ),
				'class' => [
						'elementor-field-textual',
						'elementor-size-' . $item['input_size'],
				],
				'for' => $form_id . $item_index,
				'placeholder' => $item['quantity-placeholder'],
				'autocomplete' => 'quantity',
				'field_options'=>$item['quantity_option']
			]
		]
		);
		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['quantity_option'] );

		if ( ! $options ) {
			return '';
		}
		ob_start();
		?>
		<div <?php $form->print_render_attribute_string( 'select-wrapper' . $item_index ); ?>>
			<div class="select-caret-down-wrapper">
				
			</div>
			<select <?php $form->print_render_attribute_string( 'select' . $item_index ); ?>>
				<?php
				foreach ( $options as $key => $option ) {
					$option_id = $item['custom_id'] . $key;
					$option_value = esc_attr( $option );
					$option_label = esc_html( $option );

					if ( false !== strpos( $option, '|' ) ) {
						list( $label, $value ) = explode( '|', $option );
						$option_value = esc_attr( $value );
						$option_label = esc_html( $label );
					}

					$form->add_render_attribute( $option_id, 'value', $option_value );

					// Support multiple selected values
					if ( ! empty( $item['field_value'] ) && in_array( $option_value, explode( ',', $item['field_value'] ) ) ) {
						$form->add_render_attribute( $option_id, 'selected', 'selected' );
					} ?>
					<option <?php $form->print_render_attribute_string( $option_id ); ?>><?php
						// PHPCS - $option_label is already escaped
						echo esc_html($option_label); ?></option>
				<?php } ?>
			</select>
		</div>
		<?php

		echo $select = ob_get_clean();
	}
	

	/**
	 * Field validation.
	 *
	 * Validate credit card number field value to ensure it complies to certain rules.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Field_Base   $field
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 * @return void
	 */
	public function validation( $field, $record, $ajax_handler ) {
		if ( empty( $field['value'] )) {
			return;
		}

		if ( ! empty( $field['value'])) {
			if ( intval( $field['value'] ) != $field['value'] ) {
               $ajax_handler->add_error( $field['id'], sprintf( esc_html__( 'Please enter a number. Quantity cannot contain decimals.', 'elementor-pro' )) );
         
            }elseif (! is_numeric( $field['value'] )) {
            	$ajax_handler->add_error( $field['id'], sprintf( esc_html__( 'Please enter a number.', 'elementor-pro' )) );
            } 
			
		}

	}

	/**
	 * Update form widget controls.
	 *
	 * Add input fields to allow the user to customize the credit card number field.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \Elementor\Widget_Base $widget The form widget instance.
	 * @return void
	 */
	public function update_controls( $widget ) {
		$elementor = \ElementorPro\Plugin::elementor();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			'quantity-placeholder' => [
				'name' => 'quantity-placeholder',
				'label' => esc_html__( 'Quantity Placeholder', 'elementor-form-quantity-field' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Please Enter Quantity',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'quantity_option'=>
			[
				'name' => 'quantity_option',
				'label' => esc_html__( 'Options', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 1,
				'description' => esc_html__( 'Enter each option in a separate line. To differentiate between label and value, separate them with a pipe number ("|"). For example: 5|5', 'elementor-pro' ),
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			]
			
			

		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}

	/**
	 * Field constructor.
	 *
	 * Used to add a script to the Elementor editor preview.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/preview/init', [ $this, 'editor_preview_footer' ] );
	}

	/**
	 * Elementor editor preview.
	 *
	 * Add a script to the footer of the editor preview screen.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function editor_preview_footer() {
		add_action( 'wp_footer', [ $this, 'content_template_script' ] );
	}

	/**
	 * Content template script.
	 *
	 * Add content template alternative, to display the field in Elemntor editor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template_script() {

		?>
		<script>
		jQuery( document ).ready( () => {

			elementor.hooks.addFilter(
				'elementor_pro/forms/content_template/field/<?php echo $this->get_type(); ?>',
				function ( inputField, item, i ) {
					const fieldType    = 'text';
					const fieldId      = `form_field_${i}`;
					const fieldClass   = `elementor-field-textual elementor-field ${item.css_classes}`;
					const placeholder  = item['quantity-placeholder'];
					const autocomplete = 'quantity';
					const name = 'quantity';
					const quantity_option = item['quantity_option'];

					return `<select name="${name}" id="${fieldId}" class="${fieldClass}" placeholder="${placeholder}" ><select>`;
				}, 10, 3
			);

		});
		</script>
		<?php
	}

}