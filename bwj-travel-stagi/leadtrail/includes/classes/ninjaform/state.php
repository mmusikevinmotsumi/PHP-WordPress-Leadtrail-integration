<?php
class NF_CustomPlugin_Fields_State extends NF_Fields_Textbox {
    protected $_fid='';
    protected $_name = 'lead-state';
    protected $_type = 'lead-state';

    protected $_nicename = 'Lead State';

    protected $_section = 'lead_trail';

    protected $_icon = 'map-marker';

    protected $_templates = 'textbox';

    protected $_test_value = 'Cleveland';

    public function __construct()
    {
        
        parent::__construct();

        $this->_nicename = esc_html__( 'Lead State', 'ninja-forms' );
        
        $this->_settings[ 'custom_name_attribute' ][ 'value' ] = 'lead-state';
       
        add_action( 'ninja_forms_localize_field_' .$this->_type, array( $this, 'display_form_field_localize' ) );
        add_filter( 'ninja_forms_output_templates',array( $this, 'display_form_field' ) );
        //add_filter( 'ninja_forms_display_before_field_type_textbox',array( $this, 'display_form_field' ), 10, 1 );
        //print_r($this->_settings);
    }
    public function display_form_field_localize($field){
        //print_r($field);
        $this->_fid = $field['id'];
        return $field;
    }
    public function display_form_field(){
        
            ?>
        <script>
        jQuery(document).on( 'nfFormReady', function() {
            
        var lead_state_<?php echo $this->_fid; ?> = [];
        document.getElementById('lead_country_nf').addEventListener('change', (event) => {
            lead_state_<?php echo $this->_fid; ?> = []
            var country_id = event.target.value;
            //alert(country_id);
            if(states.length>0){
                for (var i = 0; i < states.length; i++){
                    if (states[i].country_code == country_id){
                        lead_state_<?php echo $this->_fid; ?>.push(states[i].name);
                    } else {
                       
                    }
                }
            }
            autocomplete(document.getElementById('nf-field-<?php echo $this->_fid; ?>'), lead_state_<?php echo $this->_fid; ?>);
        }); 
        });              
         </script>
    <?php
    }
}

add_filter( 'ninja_forms_register_fields', 'register_fields_states');


function register_fields_states($actions) {
    $actions['lead-state'] = new NF_CustomPlugin_Fields_State(); 

    return $actions;
}
