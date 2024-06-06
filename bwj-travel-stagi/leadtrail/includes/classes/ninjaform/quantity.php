<?php
class NF_CustomPlugin_Fields_Quantity extends NF_Abstracts_List {
    protected $_name = 'lead-quantity';
    protected $_type = 'lead-quantity';

    protected $_nicename = 'Lead Quantity';

    protected $_section = 'lead_trail';

    protected $_icon = 'hashtag';

    protected $_templates = 'listselect';

   

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = esc_html__( 'Lead Quantity', 'ninja-forms' );
         //add_filter( 'ninja_forms_merge_tag_calc_value_' . $this->_type, array( $this, 'get_calc_value' ), 10, 2 );
    }

    
}

add_filter( 'ninja_forms_register_fields', 'register_fields_quantity');
function register_fields_quantity($actions) {
    $actions['lead-quantity'] = new NF_CustomPlugin_Fields_Quantity(); 

    return $actions;
}
