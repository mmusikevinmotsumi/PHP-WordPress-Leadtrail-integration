<?php

class GF_Field_Zip extends GF_Field {

    public $type = 'lead-zipcode';

    public function get_form_editor_field_title() {
        return esc_attr__( 'Lead Zipcode', 'gravityforms' );
    }
    public function get_form_editor_field_icon() {
		return 'gform-icon--place';
	}
    public function get_form_editor_button() {
        return array(
            'group' => 'lead_trail',
            'text'  => $this->get_form_editor_field_title(),
        );
    }

    public function get_form_editor_field_settings() {
        return array(
            'conditional_logic_field_setting',
	        'prepopulate_field_setting',
	        'error_message_setting',
	        'label_setting',
	        'label_placement_setting',
	        'admin_label_setting',
	        'size_setting',
	        'rules_setting',
	        'visibility_setting',
	        'duplicate_setting',
	        'default_value_setting',
	        'placeholder_setting',
	        'description_setting',
	        'css_class_setting',
        );
    }

    public function is_conditional_logic_supported() {
        return true;
    }

    public function get_field_label( $force_frontend_label, $value ) {

        $this->label='Zipcode';
        $label = $force_frontend_label ? $this->label : GFCommon::get_label( $this );

        if ( '' === $label ) {
            if ( '' !== rgar( $this, 'placeholder' ) ) {
                $label = $this->get_placeholder_value( $this->placeholder );
            } elseif ( '' !== $this->description ) {
                $label = wp_strip_all_tags( $this->description );
            }
        }

        return $label;
    }

    public function get_field_input( $form, $value = '', $entry = null ) {


    	$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

	

		$form_id  = absint( $form['id'] );
		$id       = absint( $this->id );
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;

		$size          = $this->size;
		$disabled_text = $is_form_editor ? "disabled='disabled'" : '';
		$class_suffix  = $is_entry_detail ? '_admin' : '';

		$class         = $size . $class_suffix; //Size only applies when confirmation is disabled
		$class         = esc_attr( $class );

		$form_sub_label_placement  = rgar( $form, 'subLabelPlacement' );
		$field_sub_label_placement = $this->subLabelPlacement;
		$is_sub_label_above        = $field_sub_label_placement == 'above' || ( empty( $field_sub_label_placement ) && $form_sub_label_placement == 'above' );
		$sub_label_class_attribute = $field_sub_label_placement == 'hidden_label' ? "class='hidden_sub_label screen-reader-text'" : '';

		$html_input_type = RGFormsModel::is_html5_enabled() ? 'text' : 'text';

		$required_attribute    = $this->isRequired ? 'aria-required="true"' : '';
		$invalid_attribute     = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
		$aria_describedby      = $this->get_aria_describedby();

         $zip =  $value;

        if ( is_array( $value ) ) {
            $zip = esc_attr( rgget( $this->id, $value ) );
        }
        
        
        $disabled_text = $is_form_editor ? "disabled='disabled'" : '';
		
		

		$single_placeholder_attribute        = $this->get_field_placeholder_attribute();
		

		$single_autocomplete_attribute        = $this->get_field_autocomplete_attribute();

		
			
				return "
                        <div class='ginput_complex ginput_container ginput_container_zip ginput_zip'  id='{$field_id}_container'>
                            <span id='{$field_id}_1_container' class='ginput_left'>
                                <input class='{$class}' type='text' name='input_{$id}' id='{$field_id}' value='{$zip}' {$disabled_text} {$single_placeholder_attribute} {$required_attribute} {$invalid_attribute} {$single_autocomplete_attribute} />
                              
                            </span>
                            <div class='gf_clear gf_clear_complex'></div>
                        </div>";
		
		

    }

    public function get_css_class() {
        $zip_input = GFFormsModel::get_input( $this, $this->id);

        $css_class           = '';
        $visible_input_count = 0;

        if ( $zip_input && ! rgar( $zip_input, 'isHidden' ) ) {
            $visible_input_count ++;
            $css_class .= 'has_zip_name ';
        } else {
            $css_class .= 'no_zip_name ';
        }

        
        $css_class .= "gf_zip_has_{$visible_input_count} ginput_container_zip ";

        return trim( $css_class );
    }



    public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
        if ( is_array( $value ) ) {
            $zip = trim( rgget( $this->id, $value ) );

            $return = $zip;

        } else {
            $return = '';
        }

        if ( $format === 'html' ) {
            $return = esc_html( $return );
        }

        return $return;
    }

}

GF_Fields::register( new GF_Field_Zip() );