<?php
require_once GHAX_LEADTRAIL_ABSPATH . 'public/ghx-shortcodes.php';
require_once GHAX_LEADTRAIL_ABSPATH . 'public/ghx-capture.php';
require_once GHAX_LEADTRAIL_ABSPATH . 'public/ghx-public-ajax.php';


class GHAX_Leadtrail_Public
{
  private $version;

  public function __construct()
  {
    $this->version = GHAX_VERSION;

    //Enqueues
    add_action('wp_enqueue_scripts', [$this, 'ghx_public_scripts']);
    add_action('wp_enqueue_styles', [$this, 'ghx_public_styles']);
    add_action('wp_enqueue_scripts', [$this, 'ghax_wpdocs_override_stylesheets'], PHP_INT_MAX);

    //Init
    add_action('init', [$this, 'ghx_create_buyer_role']);

    //WP Ajax
    new GHAX_Public_Ajax();

    //3rd party form submissions capture
    new GHAX_Capture();

    //Shortcodes
    new GHAX_Shortcode_Manager();

    //Add to 3rd party forms
    $this->ghx_add_form_fields();

    //Head hook
    add_action('wp_head', [$this, 'ghax_head_hook_function']);
    add_action('admin_head', [$this, 'ghax_head_hook_function']);
    add_action('admin_footer', [$this, 'ghax_footer_css_function']);
    add_action('wp_footer', [$this, 'ghax_footer_css_function']);

    //other hooks
    add_action('gform_editor_js', [$this, 'ghax_custom_option_editor_script']);
  }

  public function ghx_public_scripts()
  {
    $buy_lead_page = get_option('buy_lead_page');

    wp_enqueue_script(GHAX_LEADTRAIL_SLUG . '-custom-js', GHAX_LEADTRAIL_RELPATH . 'public/assets/js/custom_jquery.js', array('jquery'), $this->version, 'all');
    wp_localize_script(
      GHAX_LEADTRAIL_SLUG . '-custom-js',
      'ajax_script',
      array('ajaxurl' => admin_url('admin-ajax.php'), 'nc' => wp_create_nonce('ltfrontend'), 'redirecturl' => get_permalink($buy_lead_page))
    );

    //short-codes.php
    wp_enqueue_style('leadtrail-bt', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/bootstrap.min.css', [], $this->version);

    //wp_enqueue_style('leadtrail-comps', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/material-components-web.min.css');
    wp_enqueue_style('leadtrail-dt', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/jquery.dataTables.min.css', [], $this->version);
    wp_enqueue_style('leadtrail-datatable', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/dataTables.material.min.css', [], $this->version);

    wp_enqueue_script('leadtrail-btjs', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/bootstrap.min.js', array('jquery'), GHAX_VERSION);
    wp_enqueue_script('leadtrail-swt', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/sweetalert2@11.js', array('jquery'), GHAX_VERSION);
    wp_enqueue_script('leadtrail-dtjs', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/jquery.dataTables.min.js', array('jquery'), GHAX_VERSION);

    if (!is_admin() && $GLOBALS['pagenow'] != 'wp-login.php') {
      $component_css_path = GHAX_LEADTRAIL_RELPATH . 'public/assets/css/components.css';
      wp_enqueue_style('components', $component_css_path, array(), $this->version, 'all');
    }
  }

  public function ghx_public_styles()
  {
    wp_enqueue_style(GHAX_LEADTRAIL_SLUG . '-components', plugin_dir_url(dirname(dirname(__FILE__))) . 'public/assets/global/css/components.css', array(), $this->version, 'all');
  }

  public function ghx_create_buyer_role()
  {
    if (get_option('GHAXlt_custom_roles_version') < 1) {
      add_role('ghaxlt_buyer', 'Leadtrail Buyer', array('read' => true, 'level_0' => true));
      add_role('ghaxlt_annual_buyer', 'Annual Lead Buyer', array('read' => true, 'level_0' => true));
      add_role('ghaxlt_monthly_buyer', 'Monthly Lead Buyer', array('read' => true, 'level_0' => true));
      update_option('GHAXlt_custom_roles_version', 1);
    }
  }

  private function ghx_add_form_fields()
  {

    if (!function_exists('is_plugin_active')) {
      include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    if (is_plugin_active('elementor-pro/elementor-pro.php')) {
      function add_new_form_field($form_fields_registrar)
      {

        require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/elementor/quantity.php';
        require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/elementor/country.php';
        require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/elementor/state.php';
        require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/elementor/city.php';
        require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/elementor/zipcode.php';

        $form_fields_registrar->register(new \Elementor_Quantity_Field());
        $form_fields_registrar->register(new \Elementor_Country_Field());
        $form_fields_registrar->register(new \Elementor_State_Field());
        $form_fields_registrar->register(new \Elementor_City_Field());
        $form_fields_registrar->register(new \Elementor_Zipcode_Field());
      }
      add_action('elementor_pro/forms/fields/register', 'add_new_form_field');
    }

    if (is_plugin_active('gravityforms/gravityforms.php')) {
      add_filter('gform_field_groups_form_editor', 'add_new_group', 10, 1);
      function add_new_group($field_groups)
      {
        $field_groups[] = array(
          'name'   => 'lead_trail',
          'label'  => __('Lead Trail Field', 'Lead-Trail'),
          'fields' => array()
        );
        return $field_groups;
      }
      if (class_exists('GF_Field')) {
      } else {
        require_once(GHAX_LEADTRAIL_PLUGIN_DIR . '/gravityforms/includes/fields/class-gf-field.php');
      }

      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/gravity/quantity.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/gravity/country.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/gravity/state.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/gravity/city.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/gravity/zipcode.php';
    }

    if (is_plugin_active('ninja-forms/ninja-forms.php')) {
      add_filter('ninja_forms_field_type_sections',  'add_leadtrail_plugin_settings_group');
      function add_leadtrail_plugin_settings_group($sections)
      {
        $sections['lead_trail'] = array(
          'id' => 'lead_trail',
          'nicename' => __('Lead Trail Field', 'ninja-forms-lead-trail'),
          'fieldTypes' => array(),
        );

        return $sections;
      }
      require_once(GHAX_LEADTRAIL_PLUGIN_DIR . '/ninja-forms/includes/Abstracts/Element.php');
      require_once(GHAX_LEADTRAIL_PLUGIN_DIR . '/ninja-forms/includes/Abstracts/Field.php');
      require_once(GHAX_LEADTRAIL_PLUGIN_DIR . '/ninja-forms/includes/Abstracts/Input.php');
      require_once(GHAX_LEADTRAIL_PLUGIN_DIR . '/ninja-forms/includes/Fields/Textbox.php');
      require_once(GHAX_LEADTRAIL_PLUGIN_DIR . '/ninja-forms/includes/Abstracts/List.php');
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/ninjaform/country.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/ninjaform/state.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/ninjaform/city.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/ninjaform/zipcode.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/ninjaform/quantity.php';
    }

    if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
      if (class_exists('WPCF7_TagGenerator')) {
      } else {
        require_once(GHAX_LEADTRAIL_PLUGIN_DIR . '/contact-form-7/admin/includes/tag-generator.php');
      }

      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/contactform7/quantity.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/contactform7/country.php';

      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/contactform7/state.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/contactform7/city.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/contactform7/zipcode.php';
    }

    if (is_plugin_active('wpforms-lite/wpforms.php') || is_plugin_active('wpforms/wpforms.php')) {
      add_filter('wpforms_settings_tabs',  'register_settings_tabs_lite', 5, 1);
      function register_settings_tabs_lite($tabs)
      {

        // Add Payments tab.
        $payments = array(
          'lead-trail' => array(
            'name'   => esc_html__('Lead Trail', 'wpforms'),
            'form'   => true,
            'submit' => esc_html__('Save Settings', 'wpforms'),
          ),
        );

        $tabs = wpforms_array_insert($tabs, $payments, 'validation');

        return $tabs;
      }

      if (is_plugin_active('wpforms-lite/wpforms.php')) {
        $wfpugin_path = GHAX_LEADTRAIL_PLUGIN_DIR . '/wpforms-lite/';
      } else {
        $wfpugin_path = GHAX_LEADTRAIL_PLUGIN_DIR . '/wpforms/';
      }
      if (defined('WPFORMS_PLUGIN_DIR')) {
      } else {
        define('WPFORMS_PLUGIN_DIR', $wfpugin_path);
      }

      if (class_exists('WPForms_Fields')) {
      } else {
        require_once(WPFORMS_PLUGIN_DIR . 'includes/class-fields.php');
      }

      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/wpforms/quantity.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/wpforms/country.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/wpforms/city.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/wpforms/state.php';
      require_once GHAX_LEADTRAIL_ABSPATH . 'includes/classes/wpforms/zipcode.php';
    }

    add_filter('ninja_forms_field_template_file_paths', 'ghax_customfile_path');
    function ghax_customfile_path($paths)
    {

      $paths[] = GHAX_LEADTRAIL_ABSPATH . '/LeadTrail/includes/classes/ninjaform/templates/';

      return $paths;
    }
  }

  function ghax_head_hook_function()
  {
?>
    <script type="text/javascript">
      var states;
      jQuery.getJSON("<?php echo plugin_dir_url(__dir__); ?>classes/json/states.json").done(function(data) {
        states = data;
      });

      function autocomplete(inp, arr) {
        var currentFocus;
        var positionInfo = inp.getBoundingClientRect();
        inp.addEventListener("input", function(e) {
          var a, b, i, val = this.value;
          closeAllLists();
          if (!val) {
            return false;
          }
          currentFocus = -1;
          a = document.createElement("DIV");
          a.setAttribute("id", this.id + "autocomplete-list");
          a.setAttribute("class", "autocomplete-items");
          a.style.width = positionInfo.width + 'px';
          this.parentNode.appendChild(a);
          for (i = 0; i < arr.length; i++) {
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              b = document.createElement("DIV");
              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
              b.innerHTML += arr[i].substr(val.length);
              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
              b.addEventListener("click", function(e) {
                //console.log(this.getElementsByTagName("input")[0].value);
                inp.value = this.getElementsByTagName("input")[0].value;
                closeAllLists();
                var event = document.createEvent("UIEvents"); // See update below
                event.initUIEvent("change", true, true); // See update below
                inp.dispatchEvent(event);
              });
              a.appendChild(b);
            }
          }


        });
        inp.addEventListener("keydown", function(e) {
          var x = document.getElementById(this.id + "autocomplete-list");
          if (x) x = x.getElementsByTagName("div");
          if (e.keyCode == 40) {
            currentFocus++;
            addActive(x);
          } else if (e.keyCode == 38) {
            currentFocus--;
            addActive(x);
          } else if (e.keyCode == 13) {
            e.preventDefault();
            if (currentFocus > -1) {
              if (x) x[currentFocus].click();
            }
          }
        });

        function addActive(x) {
          if (!x) return false;
          removeActive(x);
          if (currentFocus >= x.length) currentFocus = 0;
          if (currentFocus < 0) currentFocus = (x.length - 1);
          x[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(x) {
          for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
          }
        }

        function closeAllLists(elmnt) {
          var x = document.getElementsByClassName("autocomplete-items");
          for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
              x[i].parentNode.removeChild(x[i]);
            }
          }

        }
        document.addEventListener("click", function(e) {
          closeAllLists(e.target);
        });
      }
    </script>
  <?php
  }

  function ghax_footer_css_function()
  { ?>
    <style type="text/css">
      .select-state-text {
        position: relative !important;
      }

      .autocomplete-items {
        position: absolute !important;
        border: 1px solid #d4d4d4 !important;
        border-bottom: none !important;
        border-top: none !important;
        z-index: 99 !important;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100% !important;
        left: 0;
        right: 0;
      }

      .autocomplete-items div {
        padding: 10px !important;
        cursor: pointer !important;
        background-color: #fff !important;
        border-bottom: 1px solid #d4d4d4 !important;
      }

      /*when hovering an item:*/
      .autocomplete-items div:hover {
        background-color: #e9e9e9 !important;
      }

      /*when navigating through the items using the arrow keys:*/
      .autocomplete-active {
        background-color: DodgerBlue !important;
        color: #ffffff !important;
      }
    </style>
  <?php
  }

  function ghax_custom_option_editor_script()
  {
  ?>
    <script type='text/javascript'>
      jQuery('.choices_setting')
        .on('input', '.field-choice-text--lead-quantity,.field-choice-value--lead-quantity', function() {
          var $this = jQuery(this);
          $this.val($this.val().replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1'));
        });
    </script>
<?php
  }

  function ghax_wpdocs_override_stylesheets()
  {
    $dir = plugin_dir_url(__FILE__);
    wp_enqueue_style('theme-override', 'https://fonts.googleapis.com/css2?family=Fredericka+the+Great&family=Jost:ital,wght@0,700;0,800;0,900;1,100&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap', array(), '0.1.0', 'all');
  }
}
