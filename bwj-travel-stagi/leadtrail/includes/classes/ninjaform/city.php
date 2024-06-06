<?php
class NF_CustomPlugin_Fields_City extends NF_Fields_Textbox {
    protected $_name = 'lead-city';
    protected $_type = 'lead-city';

    protected $_nicename = 'Lead City';

    protected $_section = 'lead_trail';

    protected $_icon = 'map-marker';

    protected $_templates = 'textbox';

    protected $_test_value = 'Cleveland';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__( 'Lead City', 'ninja-forms' );

        $this->_settings[ 'custom_name_attribute' ][ 'value' ] = 'lead-city';
    }
}

add_filter( 'ninja_forms_register_fields', 'register_fields_city');
function register_fields_city($actions) {
    $actions['lead-city'] = new NF_CustomPlugin_Fields_City(); 

    return $actions;
}



