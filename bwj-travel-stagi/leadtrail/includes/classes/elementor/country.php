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
class Elementor_Country_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

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
		return 'lead-country';
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
		return esc_html__( 'Lead Country', 'elementor-form-country-field' );
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
		$countries = file_get_contents(plugin_dir_path( __DIR__ ).'json/countries.json');
        $array_countries = json_decode($countries, true);
        $option = "<option value=''>Please select the country</option>";
        foreach ($array_countries as $array_country) {
           $option .= "<option value='".$array_country['iso2']."'>".$array_country['name']."</option>";
        }
		$form->add_render_attribute(
			'select' . $item_index,
			[	
				'name' => $this->get_attribute_name( $item ),
				'class' => 'elementor-field-textual',
				'for' => $form_id . $item_index,
				'autocomplete' => 'country',
				'id' =>'lead_country_ef_'.$form_id,
			]
		);

		echo '<select ' . $form->get_render_attribute_string( 'select' . $item_index ) . '>'.$option.'<select>';
	}
	public function get_attribute_name( $item ) {
		return "form_fields[{$item['custom_id']}]";
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
		if ( empty( $field['value'] ) ) {
			return;
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
			'country-placeholder' => [
				'name' => 'country-placeholder',
				'label' => esc_html__( 'Country Placeholder', 'elementor-form-country-field' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Please Enter Country',
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

		$countries = file_get_contents(plugin_dir_path( __DIR__ ).'json/countries.json');
        $array_countries = json_decode($countries, true);
        $option = "<option value=''>Please select the country</option>";
        foreach ($array_countries as $array_country) {
           $option .= "<option value='".$array_country['iso2']."'>".$array_country['name']."</option>";
        }
		
		
		?>
		<script>
		jQuery( document ).ready( () => {

			elementor.hooks.addFilter(
				'elementor_pro/forms/content_template/field/<?php echo $this->get_type(); ?>',
				function ( inputField, item, i ) {
					const fieldType    = 'text';
					const fieldId      = `form_field_${i}`;
					const fieldClass   = `elementor-field-textual elementor-field ${item.css_classes}`;
					const placeholder  = item['country-placeholder'];
					const autocomplete = 'country';
					const name = 'country';

					return `<select name="${name}" id="${fieldId}" class="${fieldClass}" placeholder="${placeholder}" ><?php echo $option; ?><select>`;
				}, 10, 3
			);

		});
		</script>
		<?php
	}

}