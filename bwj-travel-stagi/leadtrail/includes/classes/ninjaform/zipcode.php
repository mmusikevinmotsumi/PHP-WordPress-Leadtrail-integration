<?php
class NF_CustomPlugin_Fields_Zipcode extends NF_Fields_Textbox {
    protected $_name = 'lead-zipcode';
    protected $_type = 'lead-zipcode';

    protected $_nicename = 'Lead Zipcode';

    protected $_section = 'lead_trail';

    protected $_icon = 'map-marker';

    protected $_templates = 'textbox';

    protected $_test_value = 'Cleveland';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__( 'Lead Zipcode', 'ninja-forms' );

        $this->_settings[ 'custom_name_attribute' ][ 'value' ] = 'lead-zipcode';
    }
}

add_filter( 'ninja_forms_register_fields', 'register_fields_zip');
function register_fields_zip($actions) {
    $actions['lead-zipcode'] = new NF_CustomPlugin_Fields_Zipcode(); 

    return $actions;
}
