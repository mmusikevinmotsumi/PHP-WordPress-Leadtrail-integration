<?php
class NF_CustomPlugin_Fields_Country extends NF_Abstracts_List
{
  protected $_name = 'lead-country';
  protected $_type = 'lead-country';
  protected $_nicename = 'Lead Country';

  protected $_section = 'lead_trail';

  protected $_icon = 'map-marker';

  protected $_templates = array('listselect');


  public function __construct()
  {
    parent::__construct();
    $this->_nicename = esc_html__('Lead Country', 'ninja-forms');

    $this->_settings['options']['group'] = '';
    $this->_settings['default'] = array(
      'name' => 'default',
      'type' => 'select',
      'label' => esc_html__('Default Value', 'ninja-forms'),
      'options' => $this->get_default_value_options(),
      'width' => 'one-half',
      'group' => 'primary',
      'value' => 'US',
    );
  }

  public function admin_form_element($id, $value)
  {
    $field = Ninja_Forms()->form()->get_field($id);

    $option = "<option value=''>Please select the country</option>";
    $countries = file_get_contents(plugin_dir_path(__DIR__) . 'json/countries.json');
    $array_countries = json_decode($countries, true);

    $select = "<select name='fields[" . intval($id) . "]'>";
    foreach ($array_countries as $array_country) {
      $select .= "<option value='" . esc_attr($array_country['iso2']) . "'>" . esc_html($array_country['name']) . "</option>";
    }
    $select .= '</select>';

    return wp_kses($select, array(
      'select' => array(
        'id'   => array(),
        'class' => array(),
        'name' => array(),
      ),
      'option' => array(
        'value' => array(),
        'selected' => array(),
      )
    ));
  }

  private function get_default_value_options()
  {
    $options = array();
    // Option to have no default country
    $options[] = array(
      'label' => '- ' . esc_html__('Select Country', 'ninja-forms') . ' -',
      'value' => ''
    );
    $countries = file_get_contents(plugin_dir_path(__DIR__) . 'json/countries.json');
    $array_countries = json_decode($countries, true);

    foreach ($array_countries as $array_country) {
      $options[] = array(
        'label'  => $array_country['emoji'] . " " . $array_country['name'],
        'value' => $array_country['iso2'],
      );
    }

    return $options;
  }
}

add_filter('ninja_forms_register_fields', 'register_fields_country');
function register_fields_country($actions)
{
  $actions['lead-country'] = new NF_CustomPlugin_Fields_Country();

  return $actions;
}
add_filter('ninja_forms_render_options', function ($options, $settings) {

  if ($settings['type'] == 'lead-country') {
    $options = array();
    // Option to have no default country
    $options[] = array(
      'label' => '- ' . esc_html__('Select Country', 'ninja-forms') . ' -',
      'value' => ''
    );
    $countries = file_get_contents(plugin_dir_path(__DIR__) . 'json/countries.json');
    $array_countries = json_decode($countries, true);

    foreach ($array_countries as $array_country) {
      $options[] = array(
        'label'  => $array_country['emoji'] . " " . $array_country['name'],
        'value' => $array_country['iso2'],
        'calc' => 0,
        'selected' => false
      );
    }
    return $options;
  }


  return $options;
}, 10, 2);
