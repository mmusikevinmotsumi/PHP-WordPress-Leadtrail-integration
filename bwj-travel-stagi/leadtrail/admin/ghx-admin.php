<?php

require_once GHAX_LEADTRAIL_ABSPATH . 'admin/ghx-admin-ajax.php';
require_once GHAX_LEADTRAIL_ABSPATH . 'admin/ghx-admin-handlers.php';
require_once GHAX_LEADTRAIL_ABSPATH . 'classes/ghx-traits.php';

class GHAX_Leadtrail_Admin
{
  private $version, $header;

  public function __construct()
  {
    $this->version = GHAX_VERSION;
    $this->header = GHAX_LEADTRAIL_ABSPATH . 'admin/header.php';

    //internationalization
    add_action('plugins_loaded', array($this, 'ghx_load_plugin_textdomain'));

    //Enqueues
    add_action('admin_enqueue_scripts', [$this, 'ghx_admin_scripts']);
    add_action('admin_enqueue_scripts', [$this, 'ghx_admin_styles']);

    //Register Pages
    $this->ghx_leadtrail_register_pages();

    //Submit Handlers
    new GHAX_Leadtrail_Handlers();

    //Admin-Ajax Handlers
    new GHAX_Admin_Ajax();

    //Admin Notices
    if (isset($_GET['lterror'])) {
      add_action('admin_notices', [$this, 'ghx_error_notice']);
    }
    if (isset($_GET['ltsuccess'])) {
      add_action('admin_notices', [$this, 'ghx_success_notice']);
    }
  }

  public function ghx_load_plugin_textdomain()
  {
    load_plugin_textdomain('leadtrail', FALSE, basename(dirname(__FILE__)) . '/languages/');
  }

  public function ghx_admin_styles()
  {
    $screen = get_current_screen();

    if ($screen->base == 'toplevel_page_leadtrail') {
      //dashboard
      wp_enqueue_style('leadtrail-waves', GHAX_LEADTRAIL_RELPATH . 'admin/assets/pages/waves/css/waves.min.css', [], $this->version);
      wp_enqueue_style('leadtrail-bootstrap', GHAX_LEADTRAIL_RELPATH . 'admin/assets/css/bootstrap/css/bootstrap.min.css', [], $this->version);
      wp_enqueue_style('leadtrail-fawesome', GHAX_LEADTRAIL_RELPATH . 'admin/fontawesome/css/all.min.css', [], $this->version);
      wp_enqueue_style('leadtrail-adminfonts', GHAX_LEADTRAIL_RELPATH . 'admin/css/admin-fonts.css', [], $this->version);

      wp_enqueue_style('leadtrail-themify', GHAX_LEADTRAIL_RELPATH . 'admin/assets/icon/themify-icons/themify-icons.css', [], $this->version);

      wp_enqueue_style('leadtrail-fawes', GHAX_LEADTRAIL_RELPATH . 'admin/fontawesome/css/fontawesome.min.css', [], $this->version);
      wp_enqueue_style('leadtrail-morris', GHAX_LEADTRAIL_RELPATH . 'admin/assets/css/morris.js/css/morris.css', [], $this->version);

      wp_enqueue_style('leadtrail-scroll', GHAX_LEADTRAIL_RELPATH . 'admin/assets/css/jquery.mCustomScrollbar.css', [], $this->version);

      wp_enqueue_style('leadtrail-stylee', GHAX_LEADTRAIL_RELPATH . 'admin/assets/css/style.css', [], $this->version);
      wp_enqueue_style('leadtrail-admin', GHAX_LEADTRAIL_RELPATH . 'admin/css/admin.css', [], $this->version);
    } else if (stripos($screen->base, 'leadtrail') !== false || stripos($screen->base, 'create_') !== false || stripos($screen->base, 'edit_') !== false || stripos($screen->base, 'display_form_') !== false) {

      wp_enqueue_style('leadtrail-comps', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/material-components-web.min.css', [], $this->version);
      wp_enqueue_style('leadtrail-datatable', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/dataTables.material.min.css', [], $this->version);

      wp_enqueue_style('leadtrail-admin', GHAX_LEADTRAIL_RELPATH . 'admin/css/admin.css', [], $this->version);
      wp_enqueue_style('leadtrail-adminfont', GHAX_LEADTRAIL_RELPATH . 'admin/css/admin-fonts.css', [], $this->version);

      wp_enqueue_style('leadtrail-gfont', "https://fonts.googleapis.com/css2?family=Fredericka+the+Great&family=Jost:ital,wght@0,700;0,800;0,900;1,100&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
    } else {
      wp_enqueue_style('leadtrail-global', GHAX_LEADTRAIL_RELPATH . 'admin/css/global.css', [], $this->version);
    }
  }

  public function ghx_admin_scripts()
  {
    $screen = get_current_screen();

    if ($screen->base == 'toplevel_page_leadtrail') {
      //dashboard
      wp_enqueue_script('leadtrail-pop', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/popper.js/popper.min.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-btjs', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/bootstrap/js/bootstrap.min.js ', array('jquery'), $this->version);

      wp_enqueue_script('leadtrail-waves', GHAX_LEADTRAIL_RELPATH . 'admin/assets/pages/waves/js/waves.min.js', array('jquery'), $this->version);

      wp_enqueue_script('leadtrail-slim', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/jquery-slimscroll/jquery.slimscroll.js', array('jquery'), $this->version);

      wp_enqueue_script('leadtrail-raphael', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/raphael/raphael.min.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-morris', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/morris.js/morris.js', array('jquery'), $this->version);

      wp_enqueue_script('leadtrail-chart', GHAX_LEADTRAIL_RELPATH . 'admin/assets/pages/chart/morris/morris-custom-chart.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-pcoded', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/pcoded.min.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-vertical', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/vertical/vertical-layout.min.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-cscroll', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/jquery.mCustomScrollbar.concat.min.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-chartjs', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/chart.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-scrp', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/script.js', array('jquery'), $this->version);
    } else {
      wp_enqueue_script('leadtrail-swtjs', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/sweetalert2@11.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-dtjs', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/jquery.dataTables.min.js', array('jquery'), $this->version);
      wp_enqueue_script('leadtrail-dtmaterial', GHAX_LEADTRAIL_RELPATH . 'admin/assets/js/cdn/dataTables.material.min.js', array('jquery'), $this->version);
    }

    wp_enqueue_script('leadtrail-customjs', GHAX_LEADTRAIL_RELPATH . 'admin/js/ghx_custom.js', array('jquery', 'leadtrail-swtjs', 'leadtrail-dtjs', 'leadtrail-dtmaterial'), $this->version, true);
  }

  public function ghx_error_notice()
  {
?>
    <div class="notice notice-error is-dismissible">
      <p><?php echo esc_html(get_option('leadtrail_error')); ?></p>
    </div>
  <?php
  }

  public function ghx_success_notice()
  {
  ?>
    <div class="notice notice-success is-dismissible">
      <p><?php echo esc_html(get_option('leadtrail_success')); ?></p>
    </div>
  <?php
  }

  private function ghx_leadtrail_register_pages()
  {
    if (get_option('leadtrail_license_key') && (get_option('leadtrail_license_status') == 'active')) {

      $expp = get_option('leadtrail_license_expiry_date');
      $curDateTime = date("Y-m-d H:i:s");
      $myDate = date("Y-m-d H:i:s", strtotime($expp));

      if ($myDate < $curDateTime) {
        add_action('admin_menu', array(&$this, 'register_leadtrail_license_expired_page'));
      } else {
        add_action('admin_menu', array(&$this, 'register_leadtrail_menu_pages'));
      }
    } else {
      add_action('admin_menu', array(&$this, 'register_leadtrail_initial_licensing'));
    }
  }

  public function register_leadtrail_initial_licensing()
  {
    $pluginIcon = GHAX_LEADTRAIL_RELPATH . "icon-256x256.png";
    add_menu_page(__('LeadTrail', 'leadtrail'), __('LeadTrail', 'leadtrail'),  'manage_options', 'leadtrail', array(&$this, 'leadtrail_dashboard'), $pluginIcon);

    add_submenu_page('leadtrail', __('Activate License', 'leadtrail'), __('Activate License', 'leadtrail'), 'manage_options', 'leadtrail-license', array(&$this, 'leadtrail_license'));
  }

  public function register_leadtrail_license_expired_page()
  {
    $pluginIcon = GHAX_LEADTRAIL_RELPATH . "icon-256x256.png";
    add_menu_page(__('LeadTrail', 'leadtrail'), __('LeadTrail', 'leadtrail'), 'manage_options', 'leadtrail-license-expired', array(&$this, 'leadtrail_license_expired'), $pluginIcon);
  }

  public function register_leadtrail_menu_pages()
  {
    $pluginIcon = GHAX_LEADTRAIL_RELPATH . "icon-256x256.png";
    add_menu_page(__('LeadTrail', 'leadtrail'), __('LeadTrail', 'leadtrail'),  'manage_options', 'leadtrail', array(&$this, 'leadtrail_dashboard'), $pluginIcon);

    add_submenu_page('leadtrail', __('Leads', 'leadtrail'), __('Leads', 'leadtrail'), 'manage_options', 'leads', array(&$this, 'GHAXlt_leads_display'));
    add_submenu_page('leadtrail', __('Groups', 'leadtrail'), __('Groups', 'leadtrail'), 'manage_options', 'lead_groups', array(&$this, 'GHAXlt_lead_groups_display'));
    add_submenu_page('leadtrail', __('Qualities', 'leadtrail'), __('Qualities', 'leadtrail'), 'manage_options', 'lead_qualities', array(&$this, 'GHAXlt_lead_qualities_display'));
    add_submenu_page('leadtrail', __('Categories', 'leadtrail'), __('Categories', 'leadtrail'), 'manage_options', 'lead_categories', array(&$this, 'GHAXlt_lead_categories_display'));
    add_submenu_page('leadtrail', __('CSV Import', 'leadtrail'), __('CSV Import', 'leadtrail'), 'manage_options', 'lead_import', array(&$this, 'GHAXlt_lead_import'));
    add_submenu_page('leadtrail', __('Settings', 'leadtrail'), __('Settings', 'leadtrail'), 'manage_options', 'lead_settings', array(&$this, 'GHAXlt_lead_settings'));
    add_submenu_page('leadtrail', __('Payments', 'leadtrail'), __('Payments', 'leadtrail'), 'manage_options', 'lead_payments', array(&$this, 'GHAXlt_lead_payments'));

    //Creations
    add_submenu_page('', __('Create New Group', 'leadtrail'), __('Create New Group', 'leadtrail'), 'manage_options', 'create_group', array(&$this, 'GHAXlt_create_group'));
    add_submenu_page('', __('Create New Quality', 'leadtrail'), __('Create New Quality', 'leadtrail'), 'manage_options', 'create_quality', array(&$this, 'GHAXlt_create_quality'));
    add_submenu_page('', __('Create New Category', 'leadtrail'), __('Create New Category', 'leadtrail'), 'manage_options', 'create_category', array(&$this, 'GHAXlt_create_category'));

    //Editions
    add_submenu_page('', __('Edit Lead Data', 'leadtrail'), __('Edit Lead Data', 'leadtrail'), 'manage_options', 'edit_lead_data', array(&$this, 'edit_lead_data'));
    add_submenu_page('', __('Edit Group', 'leadtrail'), __('Edit Group', 'leadtrail'), 'manage_options', 'edit_group', array(&$this, 'GHAXlt_edit_group'));
    add_submenu_page('', __('Edit Quality', 'leadtrail'), __('Edit Quality', 'leadtrail'), 'manage_options', 'edit_quality', array(&$this, 'GHAXlt_edit_quality'));
    add_submenu_page('', __('Edit Category', 'leadtrail'), __('Edit Category', 'leadtrail'), 'manage_options', 'edit_category', array(&$this, 'GHAXlt_edit_category'));

    //others
    add_submenu_page('', __('Form Submission Data', 'leadtrail'), __('Form Submission Data', 'leadtrail'), 'manage_options', 'display_form_submission', array(&$this, 'display_form_submissions_page'));

    add_submenu_page('leadtrail', __('Getting Started', 'leadtrail'), __('Getting Started', 'leadtrail'), 'manage_options', 'leads_about', array(&$this, 'GHAXlt_leads_about'));
    add_submenu_page('leadtrail', __('Support', 'leadtrail'), __('Support', 'leadtrail'), 'manage_options', 'lead-support', array(&$this, 'leadtrail_support'));
    add_submenu_page('leadtrail', __('Licensing', 'leadtrail'), __('Licensing', 'leadtrail'), 'manage_options', 'leadtrail-license', array(&$this, 'leadtrail_license'));
  }

  function leadtrail_license_expired()
  {
  ?>
    <div class="wrap">
      <div class="expireddiv">
        <h2>License Expired</h2>
        <p>Your license has expired. In order to continue using LeadTrail, please renew your license.</p>
        <form method="post" action="">
          <?php wp_nonce_field('ltlicense', 'leadtrail_nc', false, true); ?>
          <input type="submit" name="renew_license" class="btn-primary" value="Renew License">
        </form>
      </div>
    </div>
  <?php
  }

  function leadtrail_license()
  {

    include $this->header;
  ?>
    <div class="wrap">
      <h1>Activate LeadTrail License</h1>
      <div class="licensecontainer">
        <form method="post" action="">
          <div class="form-group">
            <label>License Key</label>
            <div class="form-control">
              <?php wp_nonce_field('ltlicense', 'leadtrail_nc', false, true); ?>
              <input type="text" name="leadtrail_license_key" value="<?php echo esc_attr(get_option('leadtrail_license_key')); ?>">
              <input class="btn-primary" type="submit" name="activate_key" value="Activate Key">
            </div>

          </div>
        </form>
        <?php
        if (get_option('leadtrail_license_key') && (get_option('leadtrail_license_status') == 'active')) {
        ?>
          <span><b>This license expires on: <?php echo date('m-d-Y h:i:s A', strtotime(get_option('leadtrail_license_expiry_date'))); ?></b></span>
        <?php
        }
        ?>
      </div>
    </div>
  <?php
  }

  public function leadtrail_dashboard()
  {
    include GHAX_LEADTRAIL_ABSPATH . 'admin/dashboard.php';
  }

  function GHAXlt_leads_display()
  {
    global $wpdb;
    $tbllds = $wpdb->prefix . 'ghaxlt_leads';
    $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';
    $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';
    $tblqlty = $wpdb->prefix . 'ghaxlt_lead_qualities';
    $tblpymts = $wpdb->prefix . 'ghaxlt_leads_payments';
    $tblrole = $wpdb->prefix . 'ghaxlt_lead_roles';
    //include("./include_once.php");
  ?>

    <?php $admin_lead_field_display = get_option('admin_lead_field_display');
    if ($admin_lead_field_display) {
    } else {
      $admin_lead_field_display = array();
    }

    include $this->header;
    ?>

    <div class="wrap">
      <h1>Display all leads </h1>
      <div class="filter-holder">
        <div class="filter-left-wrap">
          <div class="row">
            <?php
            $price_result = $wpdb->get_results("select MIN(COALESCE(grps.price,0)+COALESCE(qlty.price,0)+COALESCE(cats.price,0)) as min_price,MAX(COALESCE(grps.price,0)+COALESCE(qlty.price,0)+COALESCE(cats.price,0)) as max_price from {$wpdb->prefix}ghaxlt_leads as gaxlead  left join  {$tblgrps} as grps on gaxlead.group=grps.id left join  {$tblqlty} as qlty on gaxlead.quality=qlty.id left join {$tblcats} as cats on gaxlead.category=cats.id");
            $countries = file_get_contents(GHAX_LEADTRAIL_ABSPATH . '/includes/classes/json/countries.json');
            $array_countries = json_decode($countries, true);
            $option = "<option value=''>Please select the country</option>";
            foreach ($array_countries as $array_country) {
              $option .= "<option value='" . esc_attr($array_country['iso2']) . "'>" . esc_html($array_country['name']) . "</option>";
            }  ?>
            <div class="col-md-4">
              <div class="form-holder">
                <label>Country:</label>
                <select name="country" id="country" data-column="7" class="custom-input"><?php echo $option; ?></select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-holder select-state-text">
                <label>State:</label>
                <input type="text" name="state" id="state" data-column="8" placeholder="State" class="custom-input">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-holder">
                <label>City:</label>
                <input type="text" name="city" id="city" data-column="9" placeholder="City" class="custom-input">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-holder">
                <label>Enter Zip</label>
                <input type="text" name="zipcode" id="zipcode" data-column="10" class="custom-input" placeholder="zip">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-holder">
                <label>Email</label>
                <input type="text" id="email" name="search" data-column="0" class="custom-input" placeholder="Search">
              </div>
            </div>
            <?php
            if (count($price_result) > 0 && $price_result[0]->max_price > 0) { ?>
              <div class="col-md-4 label-wrap">
                <div class="form-holder filter_and_max_price">
                  <div class="label">
                    <label>Price:</label>
                  </div>
                  <div slider id="slider-distance">
                    <div class="filter_sets">
                      <div inverse-left style="width:70%;"></div>
                      <div inverse-right style="width:70%;"></div>
                      <div range style="left:0%;right:0%;"></div>
                      <span thumb style="left:0%;"></span>
                      <span thumb style="left:100%;"></span>
                      <div sign style="left:0%;">
                        <span id="value">0</span>
                      </div>
                      <div sign style="left:100%;">
                        <span id="value"><?php echo esc_html($price_result[0]->max_price) ?></span>
                      </div>
                    </div>
                    <input id="pricemin" type="range" tabindex="0" value="0" max="<?php echo $price_result[0]->max_price ?>" min="0" step="1" oninput="
                                      this.value=Math.min(this.value,this.parentNode.childNodes[5].value-1);
                                      var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                      var children = this.parentNode.childNodes[1].childNodes;
                                      children[1].style.width=value+'%';
                                      children[5].style.left=value+'%';
                                      children[7].style.left=value+'%';children[11].style.left=value+'%';
                                      children[11].childNodes[1].innerHTML=this.value;" />

                    <input id="pricemax" type="range" tabindex="0" value="<?php echo $price_result[0]->max_price ?>" max="<?php echo $price_result[0]->max_price ?>" min="0" step="1" oninput="
                                      this.value=Math.max(this.value,this.parentNode.childNodes[3].value-(-1));
                                      var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                      var children = this.parentNode.childNodes[1].childNodes;
                                      children[3].style.width=(100-value)+'%';
                                      children[5].style.right=(100-value)+'%';
                                      children[9].style.left=value+'%';children[13].style.left=value+'%';
                                      children[13].childNodes[1].innerHTML=this.value;" />
                  </div>
                </div>
                <button class="btn btn-primary Reset_button" id="resetBtn">Reset</button>
              </div>
              <!--<div class="col-md-4"></div>
							<div class="col-md-4"></div>
							<div class="col-md-4">
								<button class="btn btn-primary" id="resetBtn">Reset</button>
							</div> -->
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="leads_container display-view-holder">

        <?php
        $style = 'style="display:none"'; ?>
        <table id="leadstbl" class="display mdl-data-table">
          <thead class="top-arrow">
            <tr>
              <th>Email</th>
              <th <?php echo (in_array('from_name', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Form Name</th>
              <th <?php echo (in_array('purchased_count', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Purchase Count</th>
              <th <?php echo (in_array('category', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Category</th>
              <th <?php echo (in_array('group', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Group</th>
              <th <?php echo (in_array('status', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Status</th>
              <th <?php echo (in_array('quality', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Quality</th>
              <th <?php echo (in_array('country', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Country</th>
              <th <?php echo (in_array('state', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>State</th>
              <th <?php echo (in_array('city', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>City</th>
              <th <?php echo (in_array('zipcode', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Zipcode</th>
              <th <?php echo (in_array('price', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Price</th>
              <th style="display:none"></th>
              <th <?php echo (in_array('published', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Published</th>
              <th <?php echo (in_array('created', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Created On</th>
              <th class="classone">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            //$results = $wpdb->get_results( "select *,cat.name as cat_name,grps.name as group_name,qlty.name as quality_name,grps.price+qlty.price as totalprice,(SELECT count(*) as count FROM {$tblpymts} WHERE lead_id=lead.id  order by id desc) as buylead,substring_index(substring_index(data, '\"lead-country\":\"', -1), '\",', 1) AS country,substring_index(substring_index(data, '\"lead-state\":\"', -1), '\",', 1) AS state,substring_index(substring_index(data, '\"lead-city\":\"', -1), '\",', 1) AS city,substring_index(substring_index(data, '\"lead-zipcode\":\"', -1), '\",', 1) AS zipcode from {$wpdb->prefix}ghaxlt_leads as lead left join  {$tblcats} as cat on lead.category=cat.id left join  {$tblgrps} as grps on lead.group=grps.id left join  {$tblqlty} as qlty on lead.quality=qlty.id");
            $results = $wpdb->get_results("select gaxlead.*,coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),1) as new_lead_quantity,cat.name as cat_name,grps.name as group_name,qlty.name as quality_name,COALESCE(grps.price,0)+COALESCE(qlty.price,0)+COALESCE(cat.price,0) as totalprice,(SELECT count(*) as count FROM {$tblpymts} WHERE lead_id=gaxlead.id  order by id desc)  as buylead from {$wpdb->prefix}ghaxlt_leads as gaxlead left join  {$tblcats} as cat on gaxlead.category=cat.id left join  {$tblgrps} as grps on gaxlead.group=grps.id left join  {$tblqlty} as qlty on gaxlead.quality=qlty.id");
            if (count($results) > 0) {
              foreach ($results as $result) {

                $myarr = json_decode($result->data, true);
                $myemail = "N/A";
                $city = "N/A";
                $state = "N/A";
                $zipcode = "N/A";
                $country = "N/A";
                foreach ($myarr as $key => $value) {
                  if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $myemail = $value;
                  }
                  if ($key == 'lead-city') {
                    $city = $value;
                  }
                  if ($key == 'lead-state') {
                    $state = $value;
                  }
                  if ($key == 'lead-zipcode') {
                    $zipcode = $value;
                  }
                  if ($key == 'lead-country') {
                    $country = $value;
                  }
                }
                $price = $result->totalprice;
            ?>
                <tr id="delete_<?php echo esc_attr($result->id); ?>">
                  <td><?php echo esc_html($myemail); ?></td>
                  <td <?php echo (in_array('from_name', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->form_name) ? esc_html($result->form_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('purchased_count', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($result->buylead) . '/' . esc_html($result->new_lead_quantity); ?></td>
                  <td <?php echo (in_array('category', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->cat_name) ? esc_html($result->cat_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('group', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->group_name) ? esc_html($result->group_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('status', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->status) ? esc_html($result->status) : 'N/A'; ?></td>
                  <td <?php echo (in_array('quality', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->quality_name) ? esc_html($result->quality_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('country', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($country); ?></td>
                  <td <?php echo (in_array('state', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($state); ?></td>
                  <td <?php echo (in_array('city', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($city); ?></td>
                  <td <?php echo (in_array('zipcode', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($zipcode); ?></td>
                  <td <?php echo (in_array('price', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php
                    if ($price) {
                      if ($result->discount_quantity) {
                        if ($result->lead_discount) {
                          if ($result->buylead >= $result->discount_quantity) {
                            $discount_multi = floor($result->buylead / $result->discount_quantity);
                            echo "<del>" . esc_html(get_option('lead_currency')) . $price . "</del> ";
                            $price = $price - ((($result->lead_discount * $price) / 100) * $discount_multi);
                            if ($price <= 0) {
                              $price = 0;
                            }
                          }
                        }
                      }
                      echo esc_html(get_option('lead_currency')) . $price;
                    } else {
                      echo 'N/A';
                    } ?>
                  </td>
                  <td style="display:none">
                    <?php if ($price && $price >= 0) {
                      echo esc_html($price);
                    } else {
                      echo 0;
                    } ?>
                  </td>
                  <td <?php echo (in_array('published', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php if ($result->publish == 1) {
                      echo "Yes";
                    } else {
                      echo "No";
                    } ?>
                  </td>
                  <td <?php echo (in_array('created', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?></td>
                  <td><a class="leadbtn" href="?page=display_form_submission&id=<?php echo esc_attr($result->id); ?>"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1024.png"></a>
                    <a class="leadbtn" href="?page=edit_lead_data&id=<?php echo esc_attr($result->id); ?>"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1023.png"></i></a>
                    <a href="javascript:void(0)" data-lead-id="<?php echo esc_attr($result->id); ?>" class="cust_b_delete tb-leadstbl leadbtn"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1022.png"></a>
                  </td>

                </tr>
            <?php
              }
            }
            ?>
          </tbody>
          <tfoot>
            <tr>
              <th>Email</th>
              <th <?php echo (in_array('from_name', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Form Name</th>
              <th <?php echo (in_array('purchased_count', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Purchase Count</th>
              <th <?php echo (in_array('category', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Category</th>
              <th <?php echo (in_array('group', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Group</th>
              <th <?php echo (in_array('status', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Status</th>
              <th <?php echo (in_array('quality', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Quality</th>
              <th <?php echo (in_array('country', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Country</th>
              <th <?php echo (in_array('state', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>State</th>
              <th <?php echo (in_array('city', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>City</th>
              <th <?php echo (in_array('zipcode', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Zipcode</th>
              <th <?php echo (in_array('price', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Price</th>
              <th style="display:none"></th>
              <th <?php echo (in_array('published', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Published</th>
              <th <?php echo (in_array('created', $admin_lead_field_display)) ? '' : 'style="display:none"'; ?>>Created On</th>
              <th>Action</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <script>
      var lead_state = []
      document.getElementById('country').addEventListener('change', (event) => {
        lead_state = []
        var country_id = event.target.value;
        if (states.length > 0) {
          for (var i = 0; i < states.length; i++) {
            if (states[i].country_code == country_id) {
              lead_state.push(states[i].name);
            } else {

            }
          }
        }
        autocomplete(document.getElementById('state'), lead_state);
      });
    </script>
    <script>
      jQuery(document).ready(function($) {

        <?php if ($price_result[0]->max_price > 0) { ?>

          jQuery.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
              var min = parseFloat(jQuery('#pricemin').val());
              var max = parseFloat(jQuery('#pricemax').val());
              var col = parseFloat(data[12]) || 0;
              if ((isNaN(min) && isNaN(max)) ||
                (isNaN(min) && col <= max) ||
                (min <= col && isNaN(max)) ||
                (min <= col && col <= max)) {
                return true;
              }
              return false;
            }
          );
        <?php } ?>
      });
    </script>
  <?php
  }

  function GHAXlt_lead_groups_display()
  {
    global $wpdb;
    $tbllds = $wpdb->prefix . 'ghaxlt_leads';
    $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';
    $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';

    include $this->header;
  ?>
    <div class="wrap">
      <div class="wrap-inn">
        <h1>Lead Groups</h1>
        <p class="text-right"><a href="?page=create_group" class="button-back">Create Group</a></p>
      </div>


      <table id="leadsgrptbl" class="display mdl-data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <!--<th>Image</th>-->
            <!--<th>Price</th>-->
            <th>Shortcode</th>
            <th>Created On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ghaxlt_lead_groups");
          if (count($results) > 0) {
            foreach ($results as $result) {
          ?>
              <tr id="delete_<?php echo esc_attr($result->id); ?>">
                <td><?php echo esc_html($result->id); ?></td>
                <td><?php echo esc_html($result->name); ?></td>
                <!--<td><?php //echo $result->image; 
                        ?></td>-->
                <!--<td><?php //echo $result->price; 
                        ?></td>-->
                <td>[display-group-leads group='<?php echo esc_attr($result->id); ?>']</td>
                <td><?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?></td>
                <td><a href="?page=edit_group&id=<?php echo esc_attr($result->id); ?>" class="leadbtn"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1023.png"></a>
                  <a href="javascript:void(0)" data-group-id="<?php echo esc_attr($result->id); ?>" class="cust_b_delete tb-leadsgrptbl leadbtn"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1022.png"></a>


                </td>
              </tr>
            <?php
            }
          } else {
            ?>
            <tr>
              <td colspan="6">No Groups Found</td>
            </tr>
          <?php
          }
          ?>

        </tbody>
        <tfoot>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <!--<th>Image</th>-->
            <!--<th>Price</th>-->
            <th>Shortcode</th>
            <th>Created On</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php
  }


  function GHAXlt_lead_qualities_display()
  {
    global $wpdb;
    $tblqlty = $wpdb->prefix . 'ghaxlt_lead_qualities';

    include $this->header;
  ?>
    <div class="wrap">
      <div class="wrap-inn">
        <h1>Lead Quality</h1>
        <p class="text-right"><a href="?page=create_quality" class="button-back">Create Quality</a></p>
      </div>
      <table id="leadsgrptbl" class="display mdl-data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <!--<th>Image</th>-->
            <!--<th>Price</th>-->
            <th>Shortcode</th>
            <th>Created On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $results = $wpdb->get_results("SELECT * FROM {$tblqlty}");
          if (count($results) > 0) {
            foreach ($results as $result) {
          ?>
              <tr id="delete_<?php echo esc_attr($result->id); ?>">
                <td><?php echo esc_html($result->id); ?></td>
                <td><?php echo esc_html($result->name); ?></td>
                <!--<td><?php //echo $result->image; 
                        ?></td>-->
                <!--<td><?php //echo $result->price; 
                        ?></td>-->
                <td>[display-quality-leads quality='<?php echo esc_attr($result->id); ?>']</td>
                <td><?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?></td>
                <td><a href="?page=edit_quality&id=<?php echo esc_attr($result->id); ?>" class="leadbtn"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1023.png"></a> <a href="javascript:void(0)" data-quality-id="<?php echo esc_attr($result->id); ?>" class="cust_b_delete tb-qualities leadbtn"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1022.png"></a></td>
              </tr>
            <?php
            }
          } else {
            ?>
            <tr>
              <td colspan="6">No Qualities Found</td>
            </tr>
          <?php
          }
          ?>

        </tbody>
        <tfoot>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <!--<th>Image</th>-->
            <!--<th>Price</th>-->
            <th>Shortcode</th>
            <th>Created On</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php
  }

  function GHAXlt_lead_categories_display()
  {
    global $wpdb;
    $tbllds = $wpdb->prefix . 'ghaxlt_leads';
    $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';
    $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';

    include $this->header;
  ?>

    <div class="wrap">
      <div class="wrap-inn">
        <h1>Lead Categories</h1>
        <p class="text-right"><a href="?page=create_category" class="button-back">Create Category</a></p>
      </div>
      <table id="leadscattbl" class="display mdl-data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <!--<th>Image</th>-->
            <th>Type</th>
            <th>Shortcode</th>
            <th>Created On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ghaxlt_lead_cats");
          if (count($results) > 0) {
            foreach ($results as $result) {
          ?>
              <tr id="delete_<?php echo esc_attr($result->id); ?>">
                <td><?php echo esc_html($result->id); ?></td>
                <td><?php echo esc_html($result->name); ?></td>
                <!--<td><?php //echo $result->image; 
                        ?></td>-->
                <td><?php echo esc_html($result->type); ?></td>
                <td>[display-category-leads category='<?php echo esc_attr($result->id); ?>']</td>
                <td><?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?></td>
                <td>
                  <a href="?page=edit_category&id=<?php echo esc_attr($result->id); ?>" class="leadbtn"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1023.png"></a>
                  <a href="javascript:void(0)" data-cat-id="<?php echo esc_attr($result->id); ?>" class="cust_b_delete tb-leadscattbl leadbtn"><img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/Frame-1022.png"></a>
                </td>
              </tr>
            <?php
            }
          } else {
            ?>
            <tr>
              <td colspan="6">No Categories Found</td>
            </tr>
          <?php
          }
          ?>

        </tbody>
        <tfoot>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <!--<th>Image</th>-->
            <th>Type</th>
            <th>Shortcode</th>
            <th>Created On</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php
  }

  function GHAXlt_lead_import()
  {
    global $wpdb;
    $csv = array();
    $message = "";
    $tbllds = $wpdb->prefix . 'ghaxlt_leads';

    include $this->header;
  ?>

    <div class="wrap">
      <h1>CSV Import</h1>
      <div class="csv_import_container">
        <h2>Import Options</h2>
        <form id="csv_import_form" enctype='multipart/form-data' action="" method="post">
          <div class="c_wrap">
            <div class="csv_wrap">



              <img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/bx_bxs-cloud-download.png" class="import-img">
              <div class="form-group">

                <label>Upload CSV file</label>
                <div class="or-row">
                  <h3>or</h3>
                </div>
                <div class="form-class">
                  <button class="browse-btn" onclick="document.getElementById('getFile').click();return false;">Browse File</button>
                  <input type='file' id="getFile" onchange="document.getElementById('file_name').innerHTML='<b>'+this.files[0].name+'</b>'" style="display:none" name="csv">
                  <span id="file_name"></span>
                  <!-- <input type="file" name="csv"> -->
                </div>
              </div>



            </div>
          </div>
          <div class="bottom-page">
            <div class="form-group">
              <div class="form-class">
                <?php wp_nonce_field('ltimport', 'leadtrail_nc', false, true); ?>
                <input type="submit" class="btn btn-primary" name="csv_upload" value="Upload">
              </div>
            </div>
            <div class="ca-sample-file">
              <a class="btn btn-primary" href="data:text/csv;charset=utf-8,First Name,Last Name,Email,Country,City,State,Zipcode,Quantity,Group,Category,Quality
John,Smith,jsmith@gmail.com,US,Houston,Texas,77027,4,Group1,Category 1,Unqualified
Cary,Robinson,caryr@yahoo.com,US,Houston,Texas,77027,3,Group 2,Category 2,Marketing Qualified
Andy,Smith,andy@yahoo.com,US,Houston,Texas,77027,1,Group 2,Category 2,Sales Qualified" download="sample.csv">
                Download Sample File
              </a>
            </div>
          </div>
        </form>
      </div>

    </div>
  <?php
  }

  function GHAXlt_lead_settings()
  {
    $lead_publish = get_option('lead_publish');
    $lead_currency = get_option('lead_currency');
    $paypal_mode = get_option('paypal_mode');
    $paypal_api_username = get_option('paypal_api_username');
    $paypal_api_password = get_option('paypal_api_password');
    $paypal_api_signature = get_option('paypal_api_signature');
    $stripe_mode = get_option('stripe_mode');
    $stripe_publishable_key = get_option('stripe_publishable_key');
    $stripe_secret_key = get_option('stripe_secret_key');
    $buy_lead_page = get_option('buy_lead_page');
    $_leadbuyerdashboard_page = get_option('_leadbuyerdashboard_page');
    $_leaddisplayall_page = get_option('_leaddisplayall_page');
    $multiple_lead = get_option('multiple_lead_show');
    $max_lead_purchase = get_option('max_lead_purchase');
    $_leaddetail_page = get_option('_leaddetail_page');
    $admin_lead_field_display = get_option('admin_lead_field_display');
    $lead_field_display = get_option('lead_field_display');
    $cat_lead_field_display = get_option('cat_lead_field_display');
    $group_lead_field_display = get_option('group_lead_field_display');
    $quality_lead_field_display = get_option('quality_lead_field_display');
    $daily_limit_annual = get_option('daily_limit_annual');
    $monthly_limit_annual = get_option('monthly_limit_annual');
    $yearly_limit_annual = get_option('yearly_limit_annual');
    $daily_limit_monthly = get_option('daily_limit_monthly');
    $monthly_limit_monthly = get_option('monthly_limit_monthly');
    $yearly_limit_monthly = get_option('yearly_limit_monthly');


    if ($admin_lead_field_display) {
    } else {
      $admin_lead_field_display = array();
    }
    if ($lead_field_display) {
    } else {
      $lead_field_display = array();
    }
    if ($cat_lead_field_display) {
    } else {
      $cat_lead_field_display = array();
    }
    if ($group_lead_field_display) {
    } else {
      $group_lead_field_display = array();
    }
    if ($quality_lead_field_display) {
    } else {
      $quality_lead_field_display = array();
    }

    include $this->header;
  ?>
    <div class="wrap">
      <h1>Settings</h1>
      <div class="settingcontainer custom-build">
        <form action="" method="post">
          <h2>General</h2>
          <div class="form-group">
            <label> Admin Lead Field Display</label>
            <div class="form-class lead-check-wrapper">
              <table>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="from_name" <?php echo (in_array('from_name', $admin_lead_field_display)) ? 'checked' : ''; ?>>Form Name</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="purchased_count" <?php echo in_array('purchased_count', $admin_lead_field_display) ? 'checked' : ''; ?>>Purchased Count</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="category" <?php echo in_array('category', $admin_lead_field_display) ? 'checked' : ''; ?>>Category</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="group" <?php echo (in_array('group', $admin_lead_field_display)) ? 'checked' : ''; ?>>Group</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="status" <?php echo (in_array('status', $admin_lead_field_display)) ? 'checked' : ''; ?>>Status</div>
                  </th>
                </tr>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="quality" <?php echo (in_array('quality', $admin_lead_field_display)) ? 'checked' : ''; ?>>Quality</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="country" <?php echo (in_array('country', $admin_lead_field_display)) ? 'checked' : ''; ?>>Country</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="state" <?php echo (in_array('state', $admin_lead_field_display)) ? 'checked' : ''; ?>>State</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="city" <?php echo (in_array('city', $admin_lead_field_display)) ? 'checked' : ''; ?>>City</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="zipcode" <?php echo (in_array('zipcode', $admin_lead_field_display)) ? 'checked' : ''; ?>>Zipcode</div>
                  </th>
                </tr>

                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="price" <?php echo (in_array('price', $admin_lead_field_display)) ? 'checked' : ''; ?>>Price</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="published" <?php echo (in_array('published', $admin_lead_field_display)) ? 'checked' : ''; ?>>Published</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="admin_lead_field_display[]" value="created" <?php echo (in_array('created', $admin_lead_field_display)) ? 'checked' : ''; ?>>Created On</div>
                  </th>
                </tr>
              </table>


            </div>
          </div>
          <hr>
          <div class="form-group">
            <label> Lead Field Display</label>
            <div class="form-class lead-check-wrapper">
              <table>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="email" <?php echo (in_array('email', $lead_field_display)) ? 'checked' : ''; ?>>Email</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="from_name" <?php echo (in_array('from_name', $lead_field_display)) ? 'checked' : ''; ?>>Form Name</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="purchased_count" <?php echo in_array('purchased_count', $lead_field_display) ? 'checked' : ''; ?>>Purchased Count</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="category" <?php echo in_array('category', $lead_field_display) ? 'checked' : ''; ?>>Category</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="group" <?php echo (in_array('group', $lead_field_display)) ? 'checked' : ''; ?>>Group</div>
                  </th>
                </tr>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="status" <?php echo (in_array('status', $lead_field_display)) ? 'checked' : ''; ?>>Status</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="quality" <?php echo (in_array('quality', $lead_field_display)) ? 'checked' : ''; ?>>Quality</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="country" <?php echo (in_array('country', $lead_field_display)) ? 'checked' : ''; ?>>Country</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="state" <?php echo (in_array('state', $lead_field_display)) ? 'checked' : ''; ?>>State</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="city" <?php echo (in_array('city', $lead_field_display)) ? 'checked' : ''; ?>>City</div>
                  </th>
                </tr>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="zipcode" <?php echo (in_array('zipcode', $lead_field_display)) ? 'checked' : ''; ?>>Zipcode</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="price" <?php echo (in_array('price', $lead_field_display)) ? 'checked' : ''; ?>>Price</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="published" <?php echo (in_array('published', $lead_field_display)) ? 'checked' : ''; ?>>Published</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="created" <?php echo (in_array('created', $lead_field_display)) ? 'checked' : ''; ?>>Created On</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="lead_field_display[]" value="additional_info" <?php echo (in_array('additional_info', $lead_field_display)) ? 'checked' : ''; ?>>Additional Info</div>
                  </th>
                </tr>
              </table>

            </div>
          </div>

          <hr>
          <div class="form-group">
            <label>Category Shortcode Lead Field Display</label>
            <div class="form-class lead-check-wrapper">

              <table>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="email" <?php echo (in_array('email', $cat_lead_field_display)) ? 'checked' : ''; ?>>Email</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="from_name" <?php echo (in_array('from_name', $cat_lead_field_display)) ? 'checked' : ''; ?>>Form Name</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="purchased_count" <?php echo in_array('purchased_count', $cat_lead_field_display) ? 'checked' : ''; ?>>Purchased Count</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="group" <?php echo (in_array('group', $cat_lead_field_display)) ? 'checked' : ''; ?>>Group</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="quality" <?php echo (in_array('quality', $cat_lead_field_display)) ? 'checked' : ''; ?>>Quality</div>
                  <th>

                </tr>

                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="country" <?php echo (in_array('country', $cat_lead_field_display)) ? 'checked' : ''; ?>>Country</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="state" <?php echo (in_array('state', $cat_lead_field_display)) ? 'checked' : ''; ?>>State</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="city" <?php echo (in_array('city', $cat_lead_field_display)) ? 'checked' : ''; ?>>City</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="zipcode" <?php echo (in_array('zipcode', $cat_lead_field_display)) ? 'checked' : ''; ?>>Zipcode</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="price" <?php echo (in_array('price', $cat_lead_field_display)) ? 'checked' : ''; ?>>Price</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="published" <?php echo (in_array('published', $cat_lead_field_display)) ? 'checked' : ''; ?>>Published</div>
                  </th>
                </tr>


                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="cat_lead_field_display[]" value="created" <?php echo (in_array('created', $cat_lead_field_display)) ? 'checked' : ''; ?>>Created On</div>
                  </th>
                </tr>
              </table>


            </div>
          </div>
          <hr>
          <div class="form-group">
            <label>Group Shortcode Lead Field Display</label>
            <div class="form-class lead-check-wrapper">
              <table>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="email" <?php echo (in_array('email', $group_lead_field_display)) ? 'checked' : ''; ?>>Email</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="from_name" <?php echo (in_array('from_name', $group_lead_field_display)) ? 'checked' : ''; ?>>Form Name</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="purchased_count" <?php echo in_array('purchased_count', $group_lead_field_display) ? 'checked' : ''; ?>>Purchased Count</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="category" <?php echo in_array('category', $group_lead_field_display) ? 'checked' : ''; ?>>Category</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="status" <?php echo (in_array('status', $group_lead_field_display)) ? 'checked' : ''; ?>>Status</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="quality" <?php echo (in_array('quality', $group_lead_field_display)) ? 'checked' : ''; ?>>Quality</div>
                  </th>
                </tr>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="country" <?php echo (in_array('country', $group_lead_field_display)) ? 'checked' : ''; ?>>Country</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="state" <?php echo (in_array('state', $group_lead_field_display)) ? 'checked' : ''; ?>>State</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="city" <?php echo (in_array('city', $group_lead_field_display)) ? 'checked' : ''; ?>>City</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="zipcode" <?php echo (in_array('zipcode', $group_lead_field_display)) ? 'checked' : ''; ?>>Zipcode</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="price" <?php echo (in_array('price', $group_lead_field_display)) ? 'checked' : ''; ?>>Price</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="published" <?php echo (in_array('published', $group_lead_field_display)) ? 'checked' : ''; ?>>Published</div>
                  </th>
                </tr>

                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="group_lead_field_display[]" value="created" <?php echo (in_array('created', $group_lead_field_display)) ? 'checked' : ''; ?>>Created On</div>
                  </th>
                </tr>
              </table>

            </div>
          </div>
          <hr>
          <div class="form-group">
            <label>Quality Lead Field Display</label>
            <div class="form-class lead-check-wrapper">
              <table>
                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="email" <?php echo (in_array('email', $quality_lead_field_display)) ? 'checked' : ''; ?>>Email</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="from_name" <?php echo (in_array('from_name', $quality_lead_field_display)) ? 'checked' : ''; ?>>Form Name</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="purchased_count" <?php echo in_array('purchased_count', $quality_lead_field_display) ? 'checked' : ''; ?>>Purchased Count</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="category" <?php echo in_array('category', $quality_lead_field_display) ? 'checked' : ''; ?>>Category</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="group" <?php echo (in_array('group', $quality_lead_field_display)) ? 'checked' : ''; ?>>Group</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="status" <?php echo (in_array('status', $quality_lead_field_display)) ? 'checked' : ''; ?>>Status</div>
                  </th>
                </tr>

                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="country" <?php echo (in_array('country', $quality_lead_field_display)) ? 'checked' : ''; ?>>Country</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="state" <?php echo (in_array('state', $quality_lead_field_display)) ? 'checked' : ''; ?>>State</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="city" <?php echo (in_array('city', $quality_lead_field_display)) ? 'checked' : ''; ?>>City</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="zipcode" <?php echo (in_array('zipcode', $quality_lead_field_display)) ? 'checked' : ''; ?>>Zipcode</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="price" <?php echo (in_array('price', $quality_lead_field_display)) ? 'checked' : ''; ?>>Price</div>
                  </th>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="published" <?php echo (in_array('published', $quality_lead_field_display)) ? 'checked' : ''; ?>>Published</div>
                  </th>
                </tr>

                <tr>
                  <th>
                    <div class="lead-input-checkbox"><input type="checkbox" name="quality_lead_field_display[]" value="created" <?php echo (in_array('created', $quality_lead_field_display)) ? 'checked' : ''; ?>>Created On</div>
                  </th>
                </tr>
              </table>

            </div>
          </div>
          <hr>
          <div class="container">
            <div class="row  form-inn">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Auto-publish Leads</label>
                  <div class="form-class">
                    <select name="lead_publish">
                      <option value="">-SELECT-</option>
                      <option value="yes" <?php if ($lead_publish == 'yes') {
                                            echo "selected";
                                          } ?>>Yes</option>
                      <option value="no" <?php if ($lead_publish == 'no') {
                                            echo "selected";
                                          } ?>>No</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group two">
                  <label class="admin" style="float: none;position: absolute;margin-left: 30px;">Multiple lead Purchases</label>
                  <div class="form-class">
                    <input type="checkbox" name="multiple_lead" value="1" <?php if ($multiple_lead) {
                                                                            echo "checked";
                                                                          } ?>>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Maximum lead purchase per user</label>
                  <div class="form-class">
                    <input type="text" name="max_lead_purchase" value="<?php echo esc_attr($max_lead_purchase); ?>">
                  </div>
                </div>
              </div>


              <div class="col-md-3">
                <div class="form-group">
                  <label>Currency</label>
                  <div class="form-class">
                    <select name="lead_currency">
                      <option value="">-SELECT-</option>
                      <option value="$" <?php if ($lead_currency == '$') {
                                          echo "selected";
                                        } ?>>USD</option>
                      <option value="£" <?php if ($lead_currency == '£') {
                                          echo "selected";
                                        } ?>>Pounds sterling</option>
                      <option value="€" <?php if ($lead_currency == '€') {
                                          echo "selected";
                                        } ?>>Euro</option>
                      <option value="CAD $" <?php if ($lead_currency == 'CAD $') {
                                              echo "selected";
                                            } ?>>Canadian Dollars</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>

      <div class="settingcontainer custom-build buyer-details">
        <h2>Buyer Details</h2>

        <div class="container">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>User role</label>
                <div class="form-class">
                  <select name="paypal_mode">
                    <option value="">-SELECT-</option>
                    <option value="leadtrail-buyer" selected>Annual Lead Buyer</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Daily limit</label>
                <div class="form-class">
                  <input type="text" name="daily_limit_annual" value="<?php echo esc_attr($daily_limit_annual); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Monthly limit</label>
                <div class="form-class">
                  <input type="text" name="monthly_limit_annual" value="<?php echo esc_attr($monthly_limit_annual); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Yearly limit</label>
                <div class="form-class">
                  <input type="text" name="yearly_limit_annual" value="<?php echo esc_attr($yearly_limit_annual); ?>">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>User role</label>
                <div class="form-class">
                  <select name="paypal_mode">
                    <option value="">-SELECT-</option>
                    <option value="leadtrail-buyer" selected>Monthly Lead Buyer</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Daily limit</label>
                <div class="form-class">
                  <input type="text" name="daily_limit_monthly" value="<?php echo esc_attr($daily_limit_monthly); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Monthly limit</label>
                <div class="form-class">
                  <input type="text" name="monthly_limit_monthly" value="<?php echo esc_attr($monthly_limit_monthly); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Yearly limit</label>
                <div class="form-class">
                  <input type="text" name="yearly_limit_monthly" value="<?php echo esc_attr($yearly_limit_monthly); ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="settingcontainer custom-build payment-details">
        <h2>Payment Details</h2>

        <div class="container">
          <div class="row">
            <h3 class="pay">Paypal</h3>
            <div class="col-md-3">
              <div class="form-group <?php echo esc_attr(get_option('stripe_mode')); ?>">
                <label>Mode</label>
                <div class="form-class">
                  <select name="paypal_mode">
                    <option value="">-SELECT-</option>
                    <option value="live" <?php if ($paypal_mode == 'live') {
                                            echo "selected";
                                          } ?>>Live</option>
                    <option value="sandbox" <?php if ($paypal_mode == 'sandbox') {
                                              echo "selected";
                                            } ?>>Sandbox</option>
                  </select>
                </div>
              </div>
            </div>

            <!--<div class="form-group">
							<label>Email</label>
							<div class="form-class">
								<input type="text" name="paypal_email" value="<?php //echo $paypal_email; 
                                                              ?>">
							</div>
						</div>-->
            <div class="col-md-3">
              <div class="form-group">
                <label>API Username</label>
                <div class="form-class">
                  <input type="text" name="paypal_api_username" value="<?php echo esc_attr($paypal_api_username); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>API Password</label>
                <div class="form-class">
                  <input type="text" name="paypal_api_password" value="<?php echo esc_attr($paypal_api_password); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>API Signature</label>
                <div class="form-class">
                  <input type="text" name="paypal_api_signature" value="<?php echo esc_attr($paypal_api_signature); ?>">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <h3 class="str">Stripe</h3>
            <div class="col-md-3">
              <div class="mode form-group">
                <label>Mode</label>
                <div class="form-class">
                  <select name="stripe_mode">
                    <option value="">-SELECT-</option>
                    <option value="live" <?php if ($stripe_mode == 'live') {
                                            echo "selected";
                                          } ?>>Live</option>
                    <option value="sandbox" <?php if ($stripe_mode == 'sandbox') {
                                              echo "selected";
                                            } ?>>Sandbox</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Publishable Key</label>
                <div class="form-class">
                  <input type="text" name="stripe_publishable_key" value="<?php echo esc_attr($stripe_publishable_key); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Client Secret</label>
                <div class="form-class">
                  <input type="text" name="stripe_secret_key" value="<?php echo esc_attr($stripe_secret_key); ?>">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label></label>
                <div class="form-class">
                  <?php wp_nonce_field('ltsettings', 'leadtrail_nc', false, true); ?>
                  <input class="btn btn-primary" type="submit" name="update_setting" value="Update Settings">
                </div>
              </div>
            </div>


          </div>
        </div>
      </div>

      <div class="settingcontainer custom-build payment-details">
        <h2>Pages</h2>
        <div class="container">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Buy Lead Page</label>
                <div class="form-class">
                  <?php
                  $args = array(
                    'sort_order' => 'asc',
                    'sort_column' => 'post_title',
                    'hierarchical' => 1,
                    'exclude' => '',
                    'include' => '',
                    'meta_key' => '',
                    'meta_value' => '',
                    'authors' => '',
                    'child_of' => 0,
                    'parent' => -1,
                    'exclude_tree' => '',
                    'number' => '',
                    'offset' => 0,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                  );
                  $pages = get_pages($args); // get all pages based on supplied args
                  ?>
                  <select name="buy_lead_page">
                    <option value="">-SELECT-</option>
                    <?php
                    foreach ($pages as $page) {
                    ?>
                      <option <?php if ($buy_lead_page == $page->ID) {
                                echo 'selected';
                              } ?> value="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html($page->post_title); ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Buyer Dashboard Page</label>
                <div class="form-class">
                  <?php
                  $args1 = array(
                    'sort_order' => 'asc',
                    'sort_column' => 'post_title',
                    'hierarchical' => 1,
                    'exclude' => '',
                    'include' => '',
                    'meta_key' => '',
                    'meta_value' => '',
                    'authors' => '',
                    'child_of' => 0,
                    'parent' => -1,
                    'exclude_tree' => '',
                    'number' => '',
                    'offset' => 0,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                  );
                  $pages1 = get_pages($args1); // get all pages based on supplied args
                  ?>
                  <select name="_leadbuyerdashboard_page">
                    <option value="">-SELECT-</option>
                    <?php
                    foreach ($pages1 as $page1) {
                    ?>
                      <option <?php if ($_leadbuyerdashboard_page == $page1->ID) {
                                echo 'selected';
                              } ?> value="<?php echo esc_attr($page1->ID); ?>"><?php echo esc_html($page1->post_title); ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Display All Leads Page</label>
                <div class="form-class">
                  <?php
                  $args2 = array(
                    'sort_order' => 'asc',
                    'sort_column' => 'post_title',
                    'hierarchical' => 1,
                    'exclude' => '',
                    'include' => '',
                    'meta_key' => '',
                    'meta_value' => '',
                    'authors' => '',
                    'child_of' => 0,
                    'parent' => -1,
                    'exclude_tree' => '',
                    'number' => '',
                    'offset' => 0,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                  );
                  $pages2 = get_pages($args2); // get all pages based on supplied args
                  ?>
                  <select name="_leaddisplayall_page">
                    <option value="">-SELECT-</option>
                    <?php
                    foreach ($pages2 as $page2) {
                    ?>
                      <option <?php if ($_leaddisplayall_page == $page2->ID) {
                                echo 'selected';
                              } ?> value="<?php echo esc_attr($page2->ID); ?>"><?php echo esc_html($page2->post_title); ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Lead Detail Page</label>
                <div class="form-class">
                  <?php
                  $args2 = array(
                    'sort_order' => 'asc',
                    'sort_column' => 'post_title',
                    'hierarchical' => 1,
                    'exclude' => '',
                    'include' => '',
                    'meta_key' => '',
                    'meta_value' => '',
                    'authors' => '',
                    'child_of' => 0,
                    'parent' => -1,
                    'exclude_tree' => '',
                    'number' => '',
                    'offset' => 0,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                  );
                  $pages2 = get_pages($args2); // get all pages based on supplied args
                  ?>
                  <select name="_leaddetail_page">
                    <option value="">-SELECT-</option>
                    <?php
                    foreach ($pages2 as $page2) {
                    ?>
                      <option <?php if ($_leaddetail_page == $page2->ID) {
                                echo 'selected';
                              } ?> value="<?php echo esc_attr($page2->ID); ?>"><?php echo esc_html($page2->post_title); ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label></label>
              <div class="form-class two-btn">
                <input class="btn btn-primary" type="submit" name="update_setting" value="Update Settings">
              </div>
            </div>

            </form>
          </div>


        </div>
      <?php
    }

    function GHAXlt_lead_payments()
    {
      global $wpdb;

      include $this->header;
      ?>
        <div class="wrap">
          <h1 class="payment">Payments</h1>
          <div class="leads_container">


            <table id="paymentstbl" class="display mdl-data-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User Id</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Lead Id</th>
                  <th>Payment By</th>
                  <th>Payment Id</th>
                  <th>Amount</th>
                  <th>Created On</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ghaxlt_leads_payments");
                if (count($results) > 0) {
                  foreach ($results as $result) {
                    $user_info = get_userdata($result->user_id);
                    $date = date_create($result->created_date);
                    if ($user_info) {

                ?>
                      <tr>
                        <td><?php echo esc_html($result->id); ?></td>
                        <td><?php echo esc_html($result->user_id); ?></td>
                        <td><?php echo esc_html($user_info->display_name); ?></td>
                        <td><?php echo esc_html($user_info->user_email); ?></td>
                        <td><?php echo esc_html($result->lead_id); ?></td>
                        <td><?php echo esc_html($result->payment_by); ?></td>
                        <td><?php echo esc_html($result->payment_id); ?></td>
                        <td>$<?php echo esc_html($result->amount); ?></td>
                        <td><?php echo date_format($date, "m-d-Y h:i:s A"); ?></td>
                      </tr>
                  <?php
                    }
                  }
                } else {
                  ?>
                  <tr>
                    <td colspan=8>No Records Found</td>
                  </tr>
                <?php
                }
                ?>

              </tbody>
              <tfoot>
                <tr>
                  <th>ID</th>
                  <th>User Id</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Lead Id</th>
                  <th>Payment By</th>
                  <th>Payment Id</th>
                  <th>Amount</th>
                  <th>Created On</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <script>
          jQuery(document).ready(function($) {
            $('#paymentstbl').DataTable({
              autoWidth: false,
              stateSave: true,
              columnDefs: [{
                targets: ['_all'],
                className: 'mdc-data-table__cell'
              }]
            });


          });
        </script>
      <?php
    }

    function leadtrail_support()
    {
      include $this->header;
      ?>

        <div class="wrap">
          <h1 class="title">Leadtrail Support</h1>
          <div class="aboutcontainer">
            <p>We’ve got a variety of resources to help you get the most out of lead plugin. Please <a href="https://leadtrail.io/support/" target="_blank">click here</a></p>
          </div>
        </div>
      <?php

    }

    function GHAXlt_leads_about()
    {
      include $this->header;
      ?>
        <div class="wrap">
          <h1 class="title">Lead Trail Documentation</h1>
          <div class="aboutcontainer">
            <p>LeadTrail is a plugin that can be used to fetch leads by using forms of different plugins like Contact
              Form7, Ninja Forms, WP Forms, Elementor Forms, and Forminator.</p>
            <p>Here you can create leads, display leads, and also sell leads to your buyers.</p>
            <p><strong>We have the following features in this plugin : </strong></p>
            <ul>
              <li><strong><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/tick-circle.png"></strong>Integrated with different plugins to capture leads - Contact Form7, Ninja Forms, WP Forms, Elementor Forms, and Forminator.</li>
              <li><strong><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/tick-circle.png"></strong>CSV import for leads</li>
              <li><strong><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/tick-circle.png"></strong>Create groups and categories for leads</li>
              <li><strong><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/tick-circle.png"></strong>Create user role buyer who can purchase leads</li>
              <li><strong><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/tick-circle.png"></strong>Admin analytics dashboard</li>
            </ul>

            <p><strong>Key Points :</strong></p>
            <p><strong><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/tick-circle.png"></strong>On plugin activation, default pages are created: Buyer Dashboard, Lead Detail Page, Display All Leads, Lead Purchase Page.</p>
            <p><strong><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/tick-circle.png"></strong>Also, there are shortcodes for the above pages if required -</p>
            <p><strong>For Buyer Dashboard - [buyer-dashboard]</strong></p>
            <p><strong>For Lead Purchase - [buy-lead]</strong></p>
            <p><strong>For Display All Leads - [display-all-leads]</strong></p>
            <p><strong>For Lead Detail Page - [lead-detail]</strong></p>

            <h4>Make sure to select the above page in the <a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=lead_settings">settings</a> section.</h4>
            <?php /*<img style="width:90%;height:300px" src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>/settings.png"> */ ?>

            <h4>You can connect either PayPal or Stripe to accept payments.</h4>
            <?php /* <img style="width:90%;height:400px" src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>/payments.png"> */ ?>
          </div>

        </div>
      <?php
    }

    //creation functions
    function GHAXlt_create_group()
    {
      global $wpdb;
      require_once(ABSPATH . 'wp-admin/includes/plugin.php');

      $forms = array();
      if (is_plugin_active('elementor-pro/elementor-pro.php')) {
        $elemontor_form_data =  $wpdb->get_results("select pmeta.meta_value,pmeta.post_id from {$wpdb->prefix}postmeta as pmeta left join {$wpdb->prefix}posts as p on p.id=pmeta.post_id where meta_key='_elementor_data' and meta_value like '%\"settings\":{\"form_name\"%' and p.post_status ='publish'");
        if ($elemontor_form_data) {
          foreach ($elemontor_form_data as $elemontor_form_list) {
            if (isset($elemontor_form_list->meta_value) && $elemontor_form_list->meta_value) {
              $elemontor_form_array = json_decode($elemontor_form_list->meta_value, true);
              if ($elemontor_form_list->meta_value && is_array($elemontor_form_array)) {
                $parent_array = ghax_search($elemontor_form_array, 'widgetType', 'form');
                $form_id   = $elemontor_form_list->post_id . '-' . $parent_array[0]['id'];
                $form_name   = $parent_array[0]['settings']['form_name'];
                $forms[$form_id] = $form_name . '(elementor forms)';
              }
            }
          }
        }
      }
      /*
			$argse = array(
							'post_type' => array('post','page'),
							'meta_query' => array(
											  array(
												 'key' => '__elementor_forms_snapshot',
												 'compare' => 'EXISTS'
											  ),
							   )
						);
			$fposts = new WP_Query($argse);
			//print_r($fposts);
			while($fposts->have_posts()):$fposts->the_post();
				$efdata = get_post_meta(get_the_ID(),'__elementor_forms_snapshot',true);
				$efdata1 = json_decode($efdata,true);
				$form_id 	= get_the_ID().'-'.$efdata1[0]['id'];
				$form_name 	= $efdata1[0]['name'];
				$forms[$form_id]=$form_name.'(elementor forms)';
			
			endwhile;
			wp_reset_postdata(); */

      if (is_plugin_active('ninja-forms/ninja-forms.php')) {
        $ninjaforms = Ninja_Forms()->form()->get_forms();

        foreach ($ninjaforms as $ninjaform) {
          $form_id   = $ninjaform->get_id();
          $form_name   = $ninjaform->get_setting('title');
          // Do more stuff
          $forms[$form_id] = $form_name . '(ninja forms)';
        }
      }

      if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        $cargs = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        $cforms = new WP_Query($cargs);
        if ($cforms) {
          while ($cforms->have_posts()) : $cforms->the_post();
            $form_id = get_the_ID();
            $form_name = get_the_title();
            $forms[$form_id] = $form_name . '(contact form7)';
          endwhile;
          wp_reset_postdata();
        }
      }

      if (is_plugin_active('wpforms-lite/wpforms.php') || is_plugin_active('wpforms/wpforms.php')) {
        $wargs = array('post_type' => 'wpforms', 'posts_per_page' => -1);
        $wforms = new WP_Query($wargs);
        if ($wforms) {
          while ($wforms->have_posts()) : $wforms->the_post();
            $form_id = get_the_ID();
            $form_name = get_the_title();
            $forms[$form_id] = $form_name . '(Wp forms)';
          endwhile;
          wp_reset_postdata();
        }
      }

      if (is_plugin_active('gravityforms/gravityforms.php')) {
        $gforms = GFAPI::get_forms();
        foreach ($gforms as $gform) {
          $form_id = $gform['id'];
          $form_name = $gform['title'];
        }
        $forms[$form_id] = $form_name . '(Gravity forms)';
      }

      if (is_plugin_active('forminator/forminator.php')) {
        $fargs = array('post_type' => 'forminator_forms', 'posts_per_page' => -1);
        $fforms = new WP_Query($fargs);
        if ($fforms) {
          while ($fforms->have_posts()) : $fforms->the_post();
            $form_id = get_the_ID();
            $form_name = get_the_title();
            $forms[$form_id] = $form_name . '(Frominator forms)';
          endwhile;
          wp_reset_postdata();
        }
      }

      include $this->header;
      ?>
        <div class="wrap">
          <div class="top-head">
            <h1>Create New Group</h1>
            <a href="?page=lead_groups" class="button-back">View Groups</a>
          </div>
          <div class="group_container custom-build">

            <form method="post" action="">
              <div class="form-group">
                <label>Name</label>
                <div class="form-class">
                  <input type="text" name="group_name" required value="">
                </div>
              </div>

              <div class="form-group">
                <label>Price</label>
                <div class="form-class">
                  <input type="number" name="group_price" required value="">
                </div>
              </div>

              <div class="form-group">
                <label>Forms</label>
                <div class="form-class">
                  <select multiple name="group_form[]" required>
                    <option value="">-SELECT-</option>
                    <?php
                    foreach ($forms as $key => $value) {
                    ?>
                      <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="form-class">
                  <?php wp_nonce_field('ltcgroup', 'leadtrail_nc', false, true); ?>
                  <input type="submit" name="create_group" value="Create Group" class="btn">
                </div>
              </div>
            </form>
          </div>
        </div>
      <?php
    }


    function GHAXlt_create_quality()
    {

      include $this->header;
      ?>

        <div class="wrap">
          <div class="top-head">
            <h1>Create New Quality</h1>
            <a href="?page=lead_qualities" class="button-back">View Qualities</a>
          </div>
          <div class="group_container custom-build">
            <form method="post" action="">
              <div class="form-group">
                <label>Name</label>
                <div class="form-class">
                  <input type="text" name="quality_name" value="" required>
                </div>
              </div>

              <div class="form-group">
                <label>Price</label>
                <div class="form-class">
                  <input type="number" required name="quality_price" value="0">
                </div>
              </div>


              <div class="form-group">
                <div class="form-class">
                  <?php wp_nonce_field('ltcquality', 'leadtrail_nc', false, true); ?>
                  <input type="submit" name="create_quality" value="Create Quality" class="btn">
                </div>
              </div>
            </form>
          </div>
        </div>
      <?php
    }

    function GHAXlt_create_category()
    {

      include $this->header;
      ?>

        <div class="wrap categories">
          <div class="top-head">
            <h1>Create New Category</h1>
            <a href="?page=lead_categories" class="button-back">View Categories</a>
          </div>
          <div class="group_container custom-build">

            <form method="post" action="">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label>Name</label>
                  <div class="form-class">
                    <input type="text" name="category_name" value="" required placeholder="Please enter your Name">
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label>Price</label>
                  <div class="form-class">
                    <input type="text" name="category_price" value="" required placeholder="Please enter Price">
                  </div>
                </div>
              </div>

              <div class="form-group" style="display:none;">
                <label>Type</label>
                <div class="form-class">

                  <select name="category_type">
                    <option value="category" selected>Category</option>
                    <!-- <option value="tag">Tag</option> -->
                  </select>
                </div>
              </div>


              <div class="form-group">
                <div class="form-class">
                  <?php wp_nonce_field('ltccat', 'leadtrail_nc', false, true); ?>
                  <input type="submit" name="create_category" value="Create Category" class="btn">
                </div>
              </div>
            </form>

          </div>
        </div>
      <?php
    }

    /** Editions */
    function edit_lead_data()
    {
      global $wpdb;
      $tbllds = $wpdb->prefix . 'ghaxlt_leads';
      $id = (int) $_GET['id'];

      $res = $wpdb->get_row("SELECT * FROM " . $tbllds . " WHERE id=" . $id);
      ?>
        <div class="wrap">
          <div class="leaddatacontainer">
            <div class="top-head">
              <h1>Edit Lead Data</h1>
              <div class="button-holder">
                <a href="?page=leads" class="button-back">View Leads</a>
              </div>
            </div>
            <form method="post" action="" class="row">
              <div class="form-group">
                <label>Lead Category</label>
                <div class="form-class">
                  <?php
                  $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';
                  $cqry = "SELECT * FROM " . $tblcats;
                  $cresults = $wpdb->get_results($cqry);
                  ?>
                  <select id="" name="lead_category" class="custom-input">
                    <option value="">-SELECT-</option>
                    <?php foreach ($cresults as $cresult) {
                    ?>
                      <option value="<?php echo esc_attr($cresult->id); ?>" <?php if ($res->category == $cresult->id) {
                                                                              echo "selected";
                                                                            } ?>><?php echo esc_html($cresult->name); ?></option>
                    <?php
                    }
                    ?>

                  </select>
                </div>
              </div>
              <div class="form-group">
                <label>Lead Quality</label>
                <div class="form-class">
                  <?php
                  $tblqlty = $wpdb->prefix . 'ghaxlt_lead_qualities';
                  $qqry = "SELECT * FROM " . $tblqlty;
                  $qresults = $wpdb->get_results($qqry);
                  ?>
                  <select id="" name="lead_quality" class="custom-input">
                    <option value="">-SELECT-</option>
                    <?php foreach ($qresults as $qresult) {
                    ?>
                      <option value="<?php echo esc_attr($qresult->id); ?>" <?php if ($res->quality == $qresult->id) {
                                                                              echo "selected";
                                                                            } ?>><?php echo esc_html($qresult->name); ?></option>
                    <?php
                    }
                    ?>

                  </select>
                </div>
              </div>
              <div class="form-group">
                <label>Lead Group</label>
                <div class="form-class">
                  <?php
                  $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';
                  $gqry = "SELECT * FROM " . $tblgrps;
                  $gresults = $wpdb->get_results($gqry);
                  ?>
                  <select id="" name="lead_group" class="custom-input">
                    <option value="">-SELECT-</option>
                    <?php foreach ($gresults as $gresult) {
                    ?>
                      <option value="<?php echo esc_attr($gresult->id); ?>" <?php if ($res->group == $gresult->id) {
                                                                              echo "selected";
                                                                            } ?>><?php echo esc_html($gresult->name); ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>

              </div>
              <div class="form-group">
                <label>Lead Status</label>
                <div class="form-class">
                  <select id="" name="lead_status" class="custom-input">
                    <option value="">-SELECT-</option>
                    <option value="open" <?php if ($res->status == 'open') {
                                            echo "selected";
                                          } ?>>Open</option>
                    <option value="sold" <?php if ($res->status == 'sold') {
                                            echo "selected";
                                          } ?>>Sold</option>
                    <option value="dead" <?php if ($res->status == 'dead') {
                                            echo "selected";
                                          } ?>>Dead</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label>Lead Publish</label>
                <div class="form-class">
                  <select name="lead_publish" class="custom-input">
                    <option value="">-SELECT-</option>
                    <option value="yes" <?php if ($res->publish == 1) {
                                          echo "selected";
                                        } ?>>Yes</option>
                    <option value="no" <?php if ($res->publish == 0) {
                                          echo "selected";
                                        } ?>>No</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label>Max Quantity</label>
                <div class="form-class">
                  <input class="custom-input" type="number" min="1" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="lead_quantity" value="<?php echo ($res->lead_quantity > 0) ? esc_attr($res->lead_quantity) : 1; ?>" style="width: 60%;">
                </div>
              </div>
              <div class="form-group">
                <label>Discount Quantity</label>
                <div class="form-class">
                  <input class="custom-input" type="number" min="1" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="discount_quantity" value="<?php echo esc_attr($res->discount_quantity); ?>" style="width: 60%;">
                </div>
              </div>

              <div class="form-group">
                <label>Discount(%)</label>
                <div class="form-class">
                  <input class="custom-input" type="number" id="lead_discount" name="lead_discount" value="<?php echo esc_attr($res->lead_discount); ?>" style="width: 60%;">
                </div>
              </div>
              <div class="form-group-wrap text-left">
                <div class="form-class">
                  <?php wp_nonce_field('ltulead', 'leadtrail_nc', false, true); ?>
                  <input type="submit" name="update_lead_data" class="btn btn-primary" value="Update Data">
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php
    }

    function GHAXlt_edit_group()
    {
      global $wpdb;
      include_once(ABSPATH . 'wp-admin/includes/plugin.php');


      $forms = array();
      if (is_plugin_active('elementor-pro/elementor-pro.php')) {
        $elemontor_form_data =  $wpdb->get_results("select pmeta.meta_value,pmeta.post_id from {$wpdb->prefix}postmeta as pmeta left join {$wpdb->prefix}posts as p on p.id=pmeta.post_id where meta_key='_elementor_data' and meta_value like '%\"settings\":{\"form_name\"%' and p.post_status ='publish'");
        if ($elemontor_form_data) {
          foreach ($elemontor_form_data as $elemontor_form_list) {
            if (isset($elemontor_form_list->meta_value) && $elemontor_form_list->meta_value) {
              $elemontor_form_array = json_decode($elemontor_form_list->meta_value, true);
              if ($elemontor_form_list->meta_value && is_array($elemontor_form_array)) {
                $parent_array = ghax_search($elemontor_form_array, 'widgetType', 'form');
                $form_id   = $elemontor_form_list->post_id . '-' . $parent_array[0]['id'];
                $form_name   = $parent_array[0]['settings']['form_name'];
                $forms[$form_id] = $form_name . '(elementor forms)';
              }
            }
          }
        }
      }
      /*
			$argse = array(
							'post_type' => array('post','page'),
							'meta_query' => array(
											  array(
												 'key' => '__elementor_forms_snapshot',
												 'compare' => 'EXISTS'
											  ),
							   )
						);
			$fposts = new WP_Query($argse);
			while($fposts->have_posts()):$fposts->the_post();
				$efdata = get_post_meta(get_the_ID(),'__elementor_forms_snapshot',true);
				$efdata1 = json_decode($efdata,true);
				$form_id 	= get_the_ID().'-'.$efdata1[0]['id'];
				$form_name 	= $efdata1[0]['name'];
				$forms[$form_id]=$form_name.'(elementor forms)';
			
			endwhile;
			wp_reset_postdata(); */

      if (is_plugin_active('ninja-forms/ninja-forms.php')) {
        $ninjaforms = Ninja_Forms()->form()->get_forms();

        foreach ($ninjaforms as $ninjaform) {
          $form_id   = $ninjaform->get_id();
          $form_name   = $ninjaform->get_setting('title');
          // Do more stuff
          $forms[$form_id] = $form_name . '(ninja forms)';
        }
      }

      if (is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        $cargs = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        $cforms = new WP_Query($cargs);
        if ($cforms) {
          while ($cforms->have_posts()) : $cforms->the_post();
            $form_id = get_the_ID();
            $form_name = get_the_title();
            $forms[$form_id] = $form_name . '(contact form7)';
          endwhile;
          wp_reset_postdata();
        }
      }



      if (is_plugin_active('wpforms-lite/wpforms.php') || is_plugin_active('wpforms/wpforms.php')) {
        $wargs = array('post_type' => 'wpforms', 'posts_per_page' => -1);
        $wforms = new WP_Query($wargs);
        if ($wforms) {
          while ($wforms->have_posts()) : $wforms->the_post();
            $form_id = get_the_ID();
            $form_name = get_the_title();
            $forms[$form_id] = $form_name . '(Wp forms)';
          endwhile;
          wp_reset_postdata();
        }
      }

      if (is_plugin_active('gravityforms/gravityforms.php')) {
        $gforms = GFAPI::get_forms();
        foreach ($gforms as $gform) {
          $form_id = $gform['id'];
          $form_name = $gform['title'];
        }
        $forms[$form_id] = $form_name . '(Gravity forms)';
      }

      if (is_plugin_active('forminator/forminator.php')) {
        $fargs = array('post_type' => 'forminator_forms', 'posts_per_page' => -1);
        $fforms = new WP_Query($fargs);
        if ($fforms) {
          while ($fforms->have_posts()) : $fforms->the_post();
            $form_id = get_the_ID();
            $form_name = get_the_title();
            $forms[$form_id] = $form_name . '(Frominator forms)';
          endwhile;
          wp_reset_postdata();
        }
      }

      $id = (int) $_GET['id'];
      $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';
      $qry = "SELECT * FROM " . $tblgrps . " WHERE id=" . $id;
      $res = $wpdb->get_row($qry);
      $fr = explode(',', $res->forms);

    ?>
      <div class="wrap">

        <div class="group_container custom-build">
          <div class="top-head">
            <h1>Edit Group</h1>
            <a href="?page=lead_groups" class="button-back">View Groups</a>
          </div>
          <form method="post" action="">
            <div class="form-group">
              <label>Name</label>
              <div class="form-class">
                <input type="text" required name="group_name" value="<?php echo esc_attr($res->name); ?>">
              </div>
            </div>

            <div class="form-group">
              <label>Price</label>
              <div class="form-class">
                <input type="number" required name="group_price" value="<?php echo esc_attr($res->price); ?>">
              </div>
            </div>

            <div class="form-group">
              <label>Forms</label>
              <div class="form-class">
                <select multiple name="group_form[]" required>
                  <option value="">-SELECT-</option>
                  <?php
                  foreach ($forms as $key => $value) {
                  ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $fr)) {
                                                                    echo 'selected';
                                                                  } ?>><?php echo esc_html($value); ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="form-class">
                <?php wp_nonce_field('ltugroup', 'leadtrail_nc', false, true); ?>
                <input type="hidden" name="id" value="<?php echo intval($id); ?>">
                <input type="submit" name="edit_group" value="Update Group" class="btn">
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php
    }


    function GHAXlt_edit_quality()
    {
      global $wpdb;
      $tblcats = $wpdb->prefix . 'ghaxlt_lead_qualities';

      $id = (int) $_GET['id'];
      $qry = "SELECT * FROM " . $tblcats . " WHERE id=" . $id;
      $res = $wpdb->get_row($qry);
    ?>
      <div class="wrap">
        <div class="group_container custom-build">
          <div class="top-head">
            <h1>Edit Quality</h1>
            <a href="?page=lead_qualities" class="button-back">View Qualities</a>
          </div>
          <form method="post" action="">
            <div class="form-group">
              <label>Name</label>
              <div class="form-class">
                <input type="text" name="quality_name" required value="<?php echo esc_attr($res->name); ?>">
              </div>
            </div>
            <div class="form-group">
              <label>Price</label>
              <div class="form-class">
                <input type="number" name="quality_price" required value="<?php echo esc_attr($res->price); ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="form-class">
                <?php wp_nonce_field('ltuquality', 'leadtrail_nc', false, true); ?>
                <input type="hidden" name="id" value="<?php echo intval($id); ?>">
                <input type="submit" name="edit_quality" value="Update Quality" class="btn">
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php
    }

    function GHAXlt_edit_category()
    {
      global $wpdb;
      $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';

      $id = (int) $_GET['id'];
      $qry = "SELECT * FROM " . $tblcats . " WHERE id=" . $id;
      $res = $wpdb->get_row($qry);
    ?>
      <div class="wrap">

        <div class="group_container custom-build">
          <div class="top-head">
            <h1>Edit Category</h1>
            <a href="?page=lead_categories" class="button-back">View Categories</a>
          </div>
          <form method="post" action="">
            <div class="form-group">
              <label>Name</label>
              <div class="form-class">
                <input type="text" name="category_name" required value="<?php echo esc_attr($res->name); ?>">
              </div>
            </div>
            <div class="form-group">
              <label>Price</label>
              <div class="form-class">
                <input type="text" name="category_price" required value="<?php echo esc_attr($res->price); ?>">
              </div>
            </div>

            <div class="form-group" style="display:none;">
              <label>Type</label>
              <div class="form-class">
                <select name="category_type" required>
                  <option value="">-SELECT-</option>
                  <!-- <option value="category" <?php if ($res->type == 'category') {
                                                  echo 'selected';
                                                } ?>>Category</option>
								<option value="tag" <?php if ($res->type == 'tag') {
                                      echo 'selected';
                                    } ?>>Tag</option> -->
                  <option value="category" selected>Category</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="form-class">
                <?php wp_nonce_field('ltucat', 'leadtrail_nc', false, true); ?>
                <input type="hidden" name="id" value="<?php echo intval($id); ?>">
                <input type="submit" name="edit_category" value="Update Category" class="btn">
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php
    }

    function display_form_submissions_page()
    {
      global $wpdb;
      $id = (int) $_GET['id'];
      $tbllds = $wpdb->prefix . 'ghaxlt_leads';
    ?>
      <div class="wrap">

        <h1>Form Submission Data</h1>
        <br>
        <a href="?page=leads" class="button-back">Back</a>
        <?php
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ghaxlt_leads WHERE id=$id");

        if ($result) {
        ?>
          <table class="leaddat">
            <tr>
              <th>Id</th>
              <td><?php echo $result->id; ?></td>
            </tr>
            <?php
            $myarr = json_decode($result->data, true);
            foreach ($myarr as $key => $value) {
              if (is_array($value)) {
                $vdata = '';
                foreach ($value as $v) {
                  $vdata .= $v . ',';
                }
            ?>
                <tr>
                  <th><?php echo esc_html(ucfirst($key)); ?></th>
                  <td><?php echo esc_html($vdata); ?></td>
                </tr>
              <?php
              } else {
              ?>
                <tr>
                  <th><?php echo esc_html(ucfirst($key)); ?></th>
                  <td><?php echo esc_html($value); ?></td>
                </tr>
            <?php
              }
            }
            ?>

          </table>
          <div class="note-info">
            <h3 style="color:red;">IMPORTANT: Any comments left here will be transferred to all buyers of this lead.</h3>
          </div>
          <?php
          wp_enqueue_script('jquery');

          $settings = array(
            'teeny' => true,
            'textarea_rows' => 10,
            'tabindex' => 1,
            'media_buttons' => false
          );
          wp_editor(__(get_option('admin_note', $result->admin_note)), 'admin_note', $settings);
          ?>
          <input type="submit" name="submit" value="Save" class="button-back add_admin_note" data-lead-id="<?php echo esc_attr($id); ?>">

          <h3 class="note-response" style="color: green;"></h3>
        <?php
        }
        ?>
      </div>
      <script>
        jQuery(document).ready(function($) {
          jQuery(document).on('click', '.add_admin_note', function() {
            var id = jQuery(this).attr('data-lead-id');
            var note = tinyMCE.get('admin_note');
            // let countWord = note.getCharacterCount();
            const wordCount = tinyMCE.activeEditor.plugins.wordcount;
            console.log("countWord:::", note.length)
            jQuery.ajax({
              type: 'POST',
              url: ajaxurl,
              data: {
                "action": "add_admin_note_action",
                "id": id,
                "table": "<?php echo $tbllds; ?>",
                "note": note.getContent()
              },
              success: function(data) {
                console.log(data)
                jQuery('.note-response').append(data);
                setTimeout(() => {
                  jQuery('.note-response').html('');
                }, 4000);
              }
            });

          });
        });
      </script>
  <?php
    }
  }
