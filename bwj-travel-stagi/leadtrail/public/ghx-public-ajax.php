<?php

class GHAX_Public_Ajax
{

  public function __construct()
  {
    add_action('wp_ajax_nopriv_lead_add_to_cart', [$this, 'lead_add_to_cart']);
    add_action('wp_ajax_lead_add_to_cart', [$this, 'lead_add_to_cart']);

    add_action('wp_ajax_nopriv_directleadtobuy', [$this, 'directleadtobuy']);
    add_action('wp_ajax_directleadtobuy', [$this, 'directleadtobuy']);

    add_action('wp_ajax_nopriv_lead_remove_cart', [$this,  'lead_remove_cart']);
    add_action('wp_ajax_lead_remove_cart', [$this, 'lead_remove_cart']);
  }

  function lead_add_to_cart()
  {
    if (!wp_verify_nonce($_POST['nc'], 'ltfrontend')) {
      exit('Unauthorized Request');
    }

    $user_id = get_current_user_id();
    $leadcart = get_user_meta($user_id, 'leadcart', true);
    $max_lead_purchase = get_option('max_lead_purchase');

    global $wpdb;

    $current_date = current_time('Y-m-d');
    $current_month = current_time('Y-m');
    $current_year = current_time('Y');

    $user_info = get_userdata($user_id);

    if ($user_info) {
      $user_roles = $user_info->roles;
      // echo 'User roles: ' . implode(', ', $user_roles);
      if (strpos(implode(', ', $user_roles), 'ghaxlt_annual_buyer') !== false){
        $daily_limit = get_option('daily_limit_annual');
        $monthly_limit = get_option('monthly_limit_annual');
        $yearly_limit = get_option('yearly_limit_annual');
      }
      if (strpos(implode(', ', $user_roles), 'ghaxlt_monthly_buyer') !== false){
        $daily_limit = get_option('daily_limit_monthly');
        $monthly_limit = get_option('monthly_limit_monthly');
        $yearly_limit = get_option('yearly_limit_monthly');
      }
      
      
    } else {
      echo 'User not found.';
    }


    $query = $wpdb->prepare(
        "SELECT COUNT(*) FROM `wp_ghaxlt_leads_payments` WHERE `user_id` = %d AND DATE(`created_date`) = %s",
        $user_id, $current_date
    );
    
    $daily_count = $wpdb->get_var($query);
    $monthly_count = $wpdb->get_var($query);
    $yearly_count = $wpdb->get_var($query);


    $id = array((int) $_POST['id']);
    if ($leadcart) {
      if (count($leadcart) >= $max_lead_purchase) {
        echo "You can purchase maximum " . (int) $max_lead_purchase . " leads at a time";
        die();
      }
      else if (count($leadcart) + $daily_count >= $daily_limit) {
        echo "Your current membership allows you to access " . (int) $daily_limit ." leads per day.";
        die();
      }
      else if (count($leadcart) + $monthly_count >= $monthly_limit) {
        echo "Your current membership allows you to access " . (int) $monthly_limit ." leads per month.";
        die();
      }
      else if (count($leadcart) + $yearly_count >= $yearly_limit) {
        echo "Your current membership allows you to access " . (int) $yearly_limit ." leads per year.";
        die();
      }
      else {

      }

      if (in_array((int) $_POST['id'], $leadcart)) {
        $leadcart1 = $leadcart;
      } else {
        if ($leadcart) {
          $leadcart1 = array_merge($leadcart, $id);
        } else {
          $leadcart1 = $id;
        }
      }
    } else {
      $leadcart1 = $id;
    }

    update_user_meta($user_id, 'leadcart', $leadcart1);
    die();
  }

  function directleadtobuy()
  {
    if (!wp_verify_nonce($_POST['nc'], 'ltfrontend')) {
      exit('Unauthorized Request');
    }
    $user_id = get_current_user_id();
    $leadcart = get_user_meta($user_id, 'leadcart', true);
    $id = array((int) $_POST['id']);

    update_user_meta($user_id, 'leadcart', $id);
    die();
  }

  function lead_remove_cart()
  {
    if (!wp_verify_nonce($_POST['nc'], 'ltfrontend')) {
      exit('Unauthorized Request');
    }
    $user_id = get_current_user_id();
    $leadcart = get_user_meta($user_id, 'leadcart', true);
    $del_val = (int) $_POST['id'];
    $id = array((int) $_POST['id']);
    if ($leadcart) {

      $leadcart1 = array_filter($leadcart, function ($e) use ($del_val) {

        return ($e !== $del_val);
      });
      update_user_meta($user_id, 'leadcart', $leadcart1);
    }
    die();
  }
}
