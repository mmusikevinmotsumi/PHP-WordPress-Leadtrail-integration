<?php

require_once GHAX_LEADTRAIL_ABSPATH . 'classes/ghx-traits.php';

class GHAX_Leadtrail_Handlers
{

  public function __construct()
  {
    add_action('admin_init', [$this, 'ghx_admin_handlers']);
  }

  public function ghx_admin_handlers()
  {
    $this->renew_license();
    $this->activate_license();
    $this->lead_import();
    $this->update_settings();

    $this->create_group();
    $this->create_quality();
    $this->create_category();

    $this->edit_lead();
    $this->edit_group();
    $this->edit_quality();
    $this->edit_category();
  }

  private function renew_license()
  {
    if (isset($_POST['renew_license'])) {

      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltlicense') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }

      $leadtrail_license_key = sanitize_text_field(get_option('leadtrail_license_key'));

      $act = wp_remote_get(GHAX_LICENSE_PURCHASE_URL . '/?ltdeactivate=' . $leadtrail_license_key);
      $result = wp_remote_retrieve_body($act);
      $result = json_decode($result, true);


      if ($result['data']['status'] == '404') {
        GHAX_Traits::leadtrail_error($result['message']);
      } else {
        if (isset($result['success']) && $result['success'] == true) {
          GHAX_Traits::leadtrail_activate_license($leadtrail_license_key);
        } else {
          GHAX_Traits::leadtrail_success($result['message']);
        }
      }
    }
  }

  private function activate_license()
  {
    if (isset($_POST['activate_key'])) {
      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltlicense') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }

      $leadtrail_license_key = sanitize_text_field($_POST['leadtrail_license_key']);

      $act = wp_remote_get(GHAX_LICENSE_PURCHASE_URL . '/?ltactivate=' . $leadtrail_license_key);
      $result = wp_remote_retrieve_body($act);
      $result = json_decode($result, true);


      if ($result['data']['status'] == '404') {
        GHAX_Traits::leadtrail_error($result['message']);
      } else {
        if ($result['success'] == true) {
          update_option('leadtrail_license_key', $leadtrail_license_key);
          update_option('leadtrail_license_status', 'active');
          update_option('leadtrail_license_expiry_date', $result['data']['expiresAt']);
          wp_redirect(admin_url('admin.php?page=leadtrail'));
        }
      }
    }
  }

  private function lead_import()
  {
    global $wpdb;
    $csv = array();
    $message = "";
    $tbllds = $wpdb->prefix . 'ghaxlt_leads';

    if (isset($_POST['csv_upload'])) {

      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltimport') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }

      // check there are no errors
      if ($_FILES['csv']['error'] == 0) {
        $name = $_FILES['csv']['name'];
        $ext =  pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION);
        $type = $_FILES['csv']['type'];
        $tmpName = $_FILES['csv']['tmp_name'];
        $header = NULL;
        $datan = array();
        // check the file is a csv
        if ($ext === 'csv') {
          $datan = GHAX_Traits::ghax_csv_to_array($tmpName, ',');
        } else {
          return;
        }

        if (get_option('lead_publish')) {
          if (get_option('lead_publish') == 'yes') {
            $publish = 1;
          } else {
            $publish = 0;
          }
        } else {
          $publish = 1;
        }
        foreach ($datan as $dat) {

          if (isset($dat['Group']) && $dat['Group']) {
            $group = sanitize_text_field($dat['Group']);
            $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';
            $gqry = "SELECT id FROM " . $tblgrps . " WHERE name='" . $group . "'";
            $gresults = $wpdb->get_results($gqry);
            if ($gresults && $gresults[0]->id) {
              $group_id = $gresults[0]->id;
            } else {
              $wpdb->insert($tblgrps, array(
                'name' => $group,
                'price' => 0
              ));
              $group_id = $wpdb->insert_id;
            }
            unset($dat['Group']);
          } else if (isset($dat['group']) && $dat['group']) {
            $group = sanitize_text_field($dat['group']);
            $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';
            $gqry = "SELECT id FROM " . $tblgrps . " WHERE name='" . $group . "'";
            $gresults = $wpdb->get_results($gqry);
            if ($gresults && $gresults[0]->id) {
              $group_id = $gresults[0]->id;
            } else {
              $wpdb->insert($tblgrps, array(
                'name' => $group,
                'price' => 0
              ));
              $group_id = $wpdb->insert_id;
            }
            unset($dat['group']);
          } else {
            $group_id = "";
          }
          if (isset($dat['Category']) && $dat['Category']) {
            $category = sanitize_text_field($dat['Category']);
            $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';
            $cqry = "SELECT id FROM " . $tblcats . " WHERE name='" . $category . "'";
            $cresults = $wpdb->get_results($cqry);
            if ($cresults && $cresults[0]->id) {
              $category_id = $cresults[0]->id;
            } else {
              $wpdb->insert($tblcats, array(
                'name' => $category,
                'type' => 'category'
              ));
              $category_id = $wpdb->insert_id;
            }
            unset($dat['Category']);
          } else if (isset($dat['category']) && $dat['category']) {
            $category = sanitize_text_field($dat['category']);
            $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';
            $cqry = "SELECT id FROM " . $tblcats . " WHERE name='" . $category . "'";
            $cresults = $wpdb->get_results($cqry);
            if ($cresults && $cresults[0]->id) {
              $category_id = $cresults[0]->id;
            } else {
              $wpdb->insert($tblcats, array(
                'name' => $category,
                'type' => 'category'
              ));
              $category_id = $wpdb->insert_id;
            }
            unset($dat['category']);
          } else {
            $category_id = "";
          }
          if (isset($dat['Quality']) && $dat['Quality']) {

            $quality = sanitize_text_field($dat['Quality']);
            $tblqlty = $wpdb->prefix . 'ghaxlt_lead_qualities';
            $qqry = "SELECT id FROM " . $tblqlty . " WHERE name='" . $quality . "'";
            $qresults = $wpdb->get_results($qqry);
            if ($qresults && $qresults[0]->id) {
              $quality_id = $qresults[0]->id;
            } else {
              $wpdb->insert($tblqlty, array(
                'name' => $quality,
                'price' => 0
              ));
              $quality_id = $wpdb->insert_id;
            }
            unset($dat['Quality']);
          } else if (isset($dat['quality']) && $dat['quality']) {
            $quality = sanitize_text_field($dat['quality']);
            $tblqlty = $wpdb->prefix . 'ghaxlt_lead_qualities';
            $qqry = "SELECT id FROM " . $tblqlty . " WHERE name='" . $quality . "'";
            $qresults = $wpdb->get_results($qqry);
            if ($qresults && $qresults[0]->id) {
              $quality_id = $qresults[0]->id;
            } else {
              $wpdb->insert($tblqlty, array(
                'name' => $quality,
                'price' => 0
              ));
              $quality_id = $wpdb->insert_id;
            }
            unset($dat['quality']);
          } else {
            $quality = '';
          }



          if (isset($dat['Country']) && $dat['Country']) {
            $dat['lead-country'] = $dat['Country'];
            unset($dat['Country']);
          } else if (isset($dat['country']) && $dat['country']) {
            $dat['lead-country'] = $dat['country'];
            unset($dat['country']);
          }

          if (isset($dat['State']) && $dat['State']) {
            $dat['lead-state'] = $dat['State'];
            unset($dat['State']);
          } else if (isset($dat['state']) && $dat['state']) {
            $dat['lead-state'] = $dat['state'];
            unset($dat['state']);
          }

          if (isset($dat['City']) && $dat['City']) {
            $dat['lead-city'] = $dat['City'];
            unset($dat['City']);
          } else if (isset($dat['city']) && $dat['city']) {
            $dat['lead-city'] = $dat['city'];
            unset($dat['city']);
          }

          if (isset($dat['Zipcode']) && $dat['Zipcode']) {
            $dat['lead-zipcode'] = $dat['Zipcode'];
            unset($dat['Zipcode']);
          } else if (isset($dat['zipcode']) && $dat['zipcode']) {
            $dat['lead-zipcode'] = $dat['zipcode'];
            unset($dat['zipcode']);
          }

          if (isset($dat['Quantity']) && $dat['Quantity']) {
            $lead_quantity = $dat['Quantity'];
            $dat['lead-quantity'] = $dat['Quantity'];
            unset($dat['Quantity']);
          } else if (isset($dat['quantity']) && $dat['quantity']) {
            $dat['lead-quantity'] = $dat['quantity'];
            $lead_quantity = $dat['Quantity'];
            unset($dat['quantity']);
          }
          $lead_quantity = ($lead_quantity != '' || $lead_quantity > 0) ? $lead_quantity : 1;

          $fdata = json_encode($dat);

          $wpdb->insert($tbllds, array(
            'data' => $fdata,
            'created_date' => date('Y-m-d H:i:s'), 'status' => 'open', 'publish' => intval($publish), 'lead_quantity' => $lead_quantity, 'category' => $category_id, 'group' => $group_id, 'quality' => $quality_id
          ));
        }

        GHAX_Traits::leadtrail_success("leads uploaded successfully, view them <a href='?page=leads'>here</a>");
      }
    }
  }

  private function update_settings()
  {

    if (isset($_POST['update_setting'])) {

      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltsettings') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }

      $lead_publish = sanitize_text_field($_POST['lead_publish']);
      $lead_currency = sanitize_text_field($_POST['lead_currency']);
      $paypal_mode = sanitize_text_field($_POST['paypal_mode']);
      $paypal_api_username = sanitize_text_field($_POST['paypal_api_username']);
      $paypal_api_password = sanitize_text_field($_POST['paypal_api_password']);
      $paypal_api_signature = sanitize_text_field($_POST['paypal_api_signature']);
      $stripe_mode = sanitize_text_field($_POST['stripe_mode']);
      $stripe_publishable_key = sanitize_text_field($_POST['stripe_publishable_key']);
      $stripe_secret_key = sanitize_text_field($_POST['stripe_secret_key']);
      $buy_lead_page = sanitize_text_field($_POST['buy_lead_page']);
      $_leadbuyerdashboard_page = sanitize_text_field($_POST['_leadbuyerdashboard_page']);
      $_leaddisplayall_page = sanitize_text_field($_POST['_leaddisplayall_page']);
      $max_lead_purchase = sanitize_text_field($_POST['max_lead_purchase']);
      $daily_limit_annual = sanitize_text_field($_POST['daily_limit_annual']);
      $monthly_limit_annual = sanitize_text_field($_POST['monthly_limit_annual']);
      $yearly_limit_annual = sanitize_text_field($_POST['yearly_limit_annual']);
      $daily_limit_monthly = sanitize_text_field($_POST['daily_limit_monthly']);
      $monthly_limit_monthly = sanitize_text_field($_POST['monthly_limit_monthly']);
      $yearly_limit_monthly = sanitize_text_field($_POST['yearly_limit_monthly']);

      if (isset($_POST['admin_lead_field_display']) && ($_POST['admin_lead_field_display'])) {
        $admin_lead_field_display = array_map('sanitize_text_field', $_POST['admin_lead_field_display']);
      } else {
        $admin_lead_field_display = "";
      }
      if (isset($_POST['lead_field_display']) && ($_POST['lead_field_display'])) {
        $lead_field_display = array_map('sanitize_text_field', $_POST['lead_field_display']);
      } else {
        $lead_field_display = "";
      }
      if (isset($_POST['cat_lead_field_display']) && ($_POST['cat_lead_field_display'])) {
        $cat_lead_field_display = array_map('sanitize_text_field', $_POST['cat_lead_field_display']);
      } else {
        $cat_lead_field_display = "";
      }
      if (isset($_POST['group_lead_field_display']) && ($_POST['group_lead_field_display'])) {
        $group_lead_field_display = array_map('sanitize_text_field', $_POST['group_lead_field_display']);
      } else {
        $group_lead_field_display = "";
      }
      if (isset($_POST['quality_lead_field_display']) && ($_POST['quality_lead_field_display'])) {
        $quality_lead_field_display = array_map('sanitize_text_field', $_POST['quality_lead_field_display']);
      } else {
        $quality_lead_field_display = "";
      }
      if (isset($_POST['multiple_lead'])) {
        $multiple_lead = sanitize_text_field($_POST['multiple_lead']);
      }
      $_leaddetail_page = sanitize_text_field($_POST['_leaddetail_page']);


      update_option('lead_publish', $lead_publish);
      update_option('lead_currency', $lead_currency);
      update_option('paypal_mode', $paypal_mode);
      update_option('paypal_api_username', $paypal_api_username);
      update_option('paypal_api_password', $paypal_api_password);
      update_option('paypal_api_signature', $paypal_api_signature);
      update_option('stripe_mode', $stripe_mode);
      update_option('stripe_publishable_key', $stripe_publishable_key);
      update_option('stripe_secret_key', $stripe_secret_key);
      update_option('buy_lead_page', $buy_lead_page);
      update_option('_leadbuyerdashboard_page', $_leadbuyerdashboard_page);
      update_option('_leaddisplayall_page', $_leaddisplayall_page);
      update_option('multiple_lead_show', $multiple_lead);
      update_option('max_lead_purchase', $max_lead_purchase);
      update_option('_leaddetail_page', $_leaddetail_page);
      update_option('admin_lead_field_display', $admin_lead_field_display);
      update_option('lead_field_display', $lead_field_display);
      update_option('cat_lead_field_display', $cat_lead_field_display);
      update_option('group_lead_field_display', $group_lead_field_display);
      update_option('quality_lead_field_display', $quality_lead_field_display);
      update_option('daily_limit_annual', $daily_limit_annual);
      update_option('monthly_limit_annual', $monthly_limit_annual);
      update_option('yearly_limit_annual', $yearly_limit_annual);
      update_option('daily_limit_monthly', $daily_limit_monthly);
      update_option('monthly_limit_monthly', $monthly_limit_monthly);
      update_option('yearly_limit_monthly', $yearly_limit_monthly);

      GHAX_Traits::leadtrail_success('Settings Update');
    }
  }

  private function create_group()
  {
    global $wpdb;
    $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';

    if (isset($_POST['create_group'])) {
      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltcgroup') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }

      $group_name = sanitize_text_field($_POST['group_name']);
      $group_form = array_map('sanitize_text_field', array_values($_POST['group_form']));
      $group_price = sanitize_text_field($_POST['group_price']);
      $group_form_str = implode(',', $group_form);
      /* $wpdb->insert( 'wp_ghaxlt_lead_groups', array('name'=>$group_name, 'price' =>$group_price,
      'created_date'=>date('Y-m-d H:i:s'),'forms'=>implode(',',$group_form))); */

      $wpdb->insert($tblgrps, array(
        'name' => $group_name, 'price' => $group_price,
        'created_date' => date('Y-m-d H:i:s'), 'forms' => $group_form_str
      ));

      GHAX_Traits::leadtrail_success('Group created successfully.');
    }
  }

  private function create_quality()
  {
    global $wpdb;
    $tblcats = $wpdb->prefix . 'ghaxlt_lead_qualities';

    if (isset($_POST['create_quality'])) {
      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltcquality') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }

      $quality_name = sanitize_text_field($_POST['quality_name']);
      $quality_price = sanitize_text_field($_POST['quality_price']);
      $wpdb->insert($tblcats, array(
        'name' => $quality_name, 'price' => $quality_price,
        'created_date' => date('Y-m-d H:i:s')
      ));

      GHAX_Traits::leadtrail_success('Quality created successfully.');
    }
  }

  private function create_category()
  {
    global $wpdb;
    $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';

    if (isset($_POST['create_category'])) {
      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltccat') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }

      $category_name = sanitize_text_field($_POST['category_name']);
      $category_type = sanitize_text_field($_POST['category_type']);
      $category_price = sanitize_text_field($_POST['category_price']);
      // $wpdb->query("alter table $tblcats change column category_price  price float(10,2) DEFAULT 0");
      $wpdb->insert($tblcats, array(
        'name' => $category_name, 'type' => $category_type,
        'created_date' => date('Y-m-d H:i:s'), 'price' => $category_price
      ));

      GHAX_Traits::leadtrail_success('Category created successfully.');
    }
  }

  private function edit_lead()
  {
    global $wpdb;
    $tbllds = $wpdb->prefix . 'ghaxlt_leads';

    if (isset($_POST['update_lead_data'])) {
      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltulead') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }

      $lead_status = sanitize_text_field($_POST['lead_status']);
      $lead_category = sanitize_text_field($_POST['lead_category']);
      $lead_group = sanitize_text_field($_POST['lead_group']);
      $lead_quality = sanitize_text_field($_POST['lead_quality']);


      $lead_quantity = sanitize_text_field($_POST['lead_quantity']);
      $discount_quantity = sanitize_text_field($_POST['discount_quantity']);
      $lead_discount = sanitize_text_field($_POST['lead_discount']);


      $lead_publish = sanitize_text_field($_POST['lead_publish']);
      if ($lead_publish == 'yes') {
        $publish = 1;
      } else {
        $publish = 0;
      }

      $wpdb->update($tbllds, array('status' => $lead_status, 'category' => $lead_category, 'group' => $lead_group, 'quality' => $lead_quality, 'lead_quantity' => $lead_quantity, 'discount_quantity' => $discount_quantity, 'lead_discount' => $lead_discount, 'publish' => intval($publish)), array('id' => (int) $_GET['id']));

      GHAX_Traits::leadtrail_success('Lead data updated successfully.');
    }
  }

  private function edit_group()
  {
    global $wpdb;
    if (isset($_POST['edit_group'])) {
      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltugroup') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }
      $group_name = sanitize_text_field($_POST['group_name']);
      $group_price = sanitize_text_field($_POST['group_price']);
      $group_form = array_map('sanitize_text_field', array_values($_POST['group_form']));

      $group_form_str = implode(',', $group_form);
      $tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';

      /* $wpdb->update( 'wp_ghaxlt_lead_groups', array('name'=>$group_name, 'price' =>$group_price,'forms'=>implode(',',$group_form)),array('id'=>$_GET['id'])); */
      $wpdb->update($tblgrps, array('name' => $group_name, 'price' => $group_price, 'forms' => $group_form_str), array('id' => (int) $_POST['id']));

      GHAX_Traits::leadtrail_success('Group updated successfully.');
    }
  }

  private function edit_quality()
  {
    global $wpdb;
    $tblcats = $wpdb->prefix . 'ghaxlt_lead_qualities';

    if (isset($_POST['edit_quality'])) {
      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltuquality') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }
      $quality_name = sanitize_text_field($_POST['quality_name']);
      $quality_price = sanitize_text_field($_POST['quality_price']);

      $wpdb->update($tblcats, array('name' => $quality_name, 'price' => $quality_price), array('id' => (int) $_GET['id']));

      GHAX_Traits::leadtrail_success('Quality updated successfully.');
    }
  }

  private function edit_category()
  {
    global $wpdb;
    $tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';

    if (isset($_POST['edit_category'])) {
      if (!wp_verify_nonce($_POST['leadtrail_nc'], 'ltucat') || !current_user_can('manage_options')) {
        exit('Unauthorized Request');
      }
      $category_name = sanitize_text_field($_POST['category_name']);
      $category_type = sanitize_text_field($_POST['category_type']);
      $category_price = sanitize_text_field($_POST['category_price']);

      $wpdb->update($tblcats, array('name' => $category_name, 'type' => $category_type, 'price' => $category_price), array('id' => (int) $_GET['id']));

      GHAX_Traits::leadtrail_success('Category updated successfully.');
    }
  }
}
