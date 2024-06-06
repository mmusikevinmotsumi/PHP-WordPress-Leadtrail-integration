<?php

class GF_Field_Quanity extends GF_Field {

    public $type = 'lead-quantity';
    public $choices = array(
             array(
               'text'       => 1,
               'value'      => 1,
               'isSelected' => true,
              
             ),
             array(
               'text'       => 5,
               'value'      => 5,
               'isSelected' => false,
              
             ),
             array(
               'text'       => 10,
               'value'      => 10,
               'isSelected' => false,
             ),
             array(
               'text'       => 15,
               'value'      => 15,
               'isSelected' => false,
             ),
           );
    public function get_form_editor_field_title() {
        return esc_attr__( 'Lead Quanity', 'gravityforms' );
    }
    public function get_form_editor_field_icon() {
		return 'gform-icon--quantity';
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
            'choices_setting',
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

        $this->label='Quanity';
        $label = $force_frontend_label ? $this->label : GFCommon::get_label( $this );

        if ( 'lead-quantity' === $label ) {
            if ( '' !== rgar( $this, 'placeholder' ) ) {
                $label = $this->get_placeholder_value( $this->placeholder );
            } elseif ( '' !== $this->description ) {
                $label = wp_strip_all_tags( $this->description );
            }
        }

        return $label;
    }

    public function validate( $value, $form ) {

        // The POST value has already been converted from currency or decimal_comma to decimal_dot and then cleaned in get_field_value().
        $value = GFCommon::maybe_add_leading_zero( $value );

        // Raw value will be tested against the is_numeric() function to make sure it is in the right format.
        // If the POST value is an array then the field is inside a repeater so use $value.
        $raw_value = isset( $_POST[ 'input_' . $this->id ] ) && ! is_array( $_POST[ 'input_' . $this->id ] ) ? GFCommon::maybe_add_leading_zero( rgpost( 'input_' . $this->id ) ) : $value;

        //$requires_valid_number = ! rgblank( $raw_value ) && ! $this->has_calculation();
        //$is_valid_number       = $this->validate_range( $value ) && GFCommon::is_numeric( $raw_value, $this->numberFormat );

        if ( $this->type == 'lead-quantity' ) {
            if ( intval( $value ) != $value ) {
                $this->failed_validation  = true;
                $this->validation_message = empty( $field['errorMessage'] ) ? esc_html__( 'Please enter a number. Quantity cannot contain decimals.', 'gravityforms' ) : $field['errorMessage'];
            } elseif ( ! empty( $value ) && ( ! is_numeric( $value ) || intval( $value ) != floatval( $value ) || intval( $value ) < 0 ) ) {
                $this->failed_validation  = true;
                $this->validation_message = empty( $field['errorMessage'] ) ? esc_html__( 'Please enter a number', 'gravityforms' ) : $field['errorMessage'];
            }
        }

    }

    
   
    public function get_field_input( $form, $value = '', $entry = null ) {
        $form_id         = absint( $form['id'] );
        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();

        $id       = $this->id;
        $field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

        $size                   = $this->size;
        $class_suffix           = $is_entry_detail ? '_admin' : '';
        $class                  = $size . $class_suffix;
        $css_class              = trim( esc_attr( $class ) . ' gfield_select' );
        $tabindex               = $this->get_tabindex();
        $disabled_text          = $is_form_editor ? 'disabled="disabled"' : '';
        $required_attribute     = $this->isRequired ? 'aria-required="true"' : '';
        $invalid_attribute      = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
        $describedby_attribute = $this->get_aria_describedby();
        $autocomplete_attribute = $this->enableAutocomplete ? $this->get_field_autocomplete_attribute() : '';

        return sprintf( "<div class='ginput_container ginput_container_select'><select name='input_%d' id='%s' class='%s' $tabindex $describedby_attribute %s %s %s %s>%s</select></div>", $id, $field_id, $css_class, $disabled_text, $required_attribute, $invalid_attribute, $autocomplete_attribute, $this->get_choices( $value ) );

    }

    public function get_css_class() {
        $quantity_input = GFFormsModel::get_input( $this, $this->id);

        $css_class           = '';
        $visible_input_count = 0;

        if ( $quantity_input && ! rgar( $quantity_input, 'isHidden' ) ) {
            $visible_input_count ++;
            $css_class .= 'has_quantity_name ';
        } else {
            $css_class .= 'no_quantity_name ';
        }

        
        $css_class .= "gf_quantity_has_{$visible_input_count} ginput_container_quantity ";

        return trim( $css_class );
    }
 

    public function get_choices( $value ) {

        return GFCommon::get_select_choices( $this, $value );
    }


    public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
        if ( is_array( $value ) ) {
            $quantity = trim( rgget( $this->id, $value ) );

            $return = $quantity;

        } else {
            $return = '';
        }

        if ( $format === 'html' ) {
            $return = esc_html( $return );
        }

        return $return;
    }

}

GF_Fields::register( new GF_Field_Quanity() );
