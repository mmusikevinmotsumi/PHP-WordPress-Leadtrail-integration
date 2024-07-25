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

    add_action('wp_ajax_nopriv_confirm_add_to_cart', [$this,  'confirm_add_to_cart']);
    add_action('wp_ajax_confirm_add_to_cart', [$this, 'confirm_add_to_cart']);

  }
	
  function lead_add_to_cart()
  	{
    if (!wp_verify_nonce($_POST['nc'], 'ltfrontend')) {
      exit('Unauthorized Request');
    }

    $user_id = get_current_user_id();
    $leadcart = get_user_meta($user_id, 'leadcart', true);
    $max_lead_purchase = get_option('max_lead_purchase');
    $max_global_purchase = get_option('max_global_purchase');
    

// 	echo "User ID: ".$user_id."\n";
    global $wpdb;

    $current_date = current_time('Y-m-d');
    $current_month = current_time('Y-m');
    $current_year = current_time('Y');
// 	echo "Current Date: ".$current_date."\n";
// 	echo "Current Month: ".$current_month."\n";
// 	echo "Current Year: ".$current_year."\n";

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
      if (strpos(implode(', ', $user_roles), 'administrator') !== false){
        $daily_limit = 9999;
        $monthly_limit = 9999;
        $yearly_limit = 9999;
      }
      
      
    } else {
      echo 'User not found.';
    }


    // Daily count
    $daily_query = $wpdb->prepare(
      "SELECT * FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE `user_id` = %d AND DATE(`created_date`) = %s",
      $user_id, $current_date
    );
    $daily_count = count($wpdb->get_results($daily_query));

    // Daily total count
    $daily_total_query = $wpdb->prepare(
      "SELECT * FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE DATE(`created_date`) = %s",
      $current_date
    );
    $daily_total_count = count($wpdb->get_results($daily_total_query));
		  	  
// 	echo "Daily count: ".$daily_count."\n";
	  
    // Monthly count
    $monthly_query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE `user_id` = %d AND YEAR(`created_date`) = YEAR(%s) AND MONTH(`created_date`) = MONTH(%s)",
      $user_id, $current_date, $current_date
    );
	$monthly_count = count($wpdb->get_results($monthly_query));
	  
// 	echo "Monthly count: ".$monthly_count."\n";

    // Yearly count
    $yearly_query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE `user_id` = %d AND YEAR(`created_date`) = YEAR(%s)",
      $user_id, $current_date
    );
    $yearly_count = count($wpdb->get_results($yearly_query));

// 	echo "Yearly count: ".$yearly_count."\n";
	

    $id = array((int) $_POST['id']);
    if ($leadcart) {
// 		echo "Cart count: ".count($leadcart)."\n";
      if (count($leadcart) >= $max_lead_purchase) {
        echo "You may only request " . (int) $max_lead_purchase . " leads at a time";
        die();
      }
      else if (count($leadcart) + $daily_count > $daily_limit) {
        echo "Your membership allows access to a total of:\n" . (int) $daily_limit ." leads per day\n". (int) $monthly_limit ." leads per month\n". (int) $yearly_limit ." leads per year";
        die();
      }
      else if (count($leadcart) + $monthly_count > $monthly_limit) {
        echo "Your membership allows access to a total of:\n" . (int) $daily_limit ." leads per day\n". (int) $monthly_limit ." leads per month\n". (int) $yearly_limit ." leads per year";
        die();
      }
      else if (count($leadcart) + $yearly_count > $yearly_limit) {
        echo "Your membership allows access to a total of:\n" . (int) $daily_limit ." leads per day\n". (int) $monthly_limit ." leads per month\n". (int) $yearly_limit ." leads per year";
        die();
      }
      else {
          // $wpdb->insert(
          //   $wpdb->prefix . "ghaxlt_leads_payments",
          //   array(
          //     'user_id' => get_current_user_id(),
          //     'lead_id' => $_POST['id'],
          //     'payment_by' => 'N/A',
          //     'amount' => 0,
          //     'payment_id' => 'N/A',
          //     'transaction_type' => 'N/A',
          //   )
          // );
          // $wpdb->update($wpdb->prefix . "ghaxlt_leads", array('status' => 'sold'), array('id' => $_POST['id']));
          // update_user_meta($user_id, 'leadcart', "");
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
// 		echo "Cart count: 0";
      if ($daily_count >= $daily_limit) {
        echo "Your membership allows access to a total of:\n" . (int) $daily_limit ." leads per day\n". (int) $monthly_limit ." leads per month\n". (int) $yearly_limit ." leads per year";
        die();
      }
      else if ($monthly_count >= $monthly_limit) {
        echo "Your membership allows access to a total of:\n" . (int) $daily_limit ." leads per day\n". (int) $monthly_limit ." leads per month\n". (int) $yearly_limit ." leads per year";
        die();
      }
      else if ($yearly_count >= $yearly_limit) {
        echo "Your membership allows access to a total of:\n" . (int) $daily_limit ." leads per day\n". (int) $monthly_limit ." leads per month\n". (int) $yearly_limit ." leads per year";
        die();
      }
      else {
        $leadcart1 = $id;
        // $wpdb->insert(
        //   $wpdb->prefix . "ghaxlt_leads_payments",
        //   array(
        //     'user_id' => get_current_user_id(),
        //     'lead_id' => $_POST['id'],
        //     'payment_by' => 'N/A',
        //     'amount' => 0,
        //     'payment_id' => 'N/A',
        //     'transaction_type' => 'N/A',
        //   )
        // );
        // $wpdb->update($wpdb->prefix . "ghaxlt_leads", array('status' => 'sold'), array('id' => $_POST['id']));
        // update_user_meta($user_id, 'leadcart', "");
      }
    }

    update_user_meta($user_id, 'leadcart', $leadcart1);
    die();
  }
	
  function confirm_add_to_cart()
  {
    if (!wp_verify_nonce($_POST['nc'], 'ltfrontend')) {
      exit('Unauthorized Request');
    }
    $user_id = get_current_user_id();
    $leadcart = get_user_meta($user_id, 'leadcart', true);
    
    global $wpdb;
    
    if ($leadcart) {
      foreach ($leadcart as $key => $value) {
        $wpdb->insert(
          $wpdb->prefix . "ghaxlt_leads_payments",
          array(
            'user_id' => get_current_user_id(),
            'lead_id' => $value,
            'payment_by' => 'N/A',
            'amount' => 0,
            'payment_id' => 'N/A',
            'transaction_type' => 'sandbox',
          )
        );
        $wpdb->update($wpdb->prefix . "ghaxlt_leads", array('status' => 'sold'), array('id' => $value));
      }
      update_user_meta($user_id, 'leadcart', "");
      wp_send_json_success(array(
          'redirect_url' => get_permalink(get_option('_leadbuyerdashboard_page'))
      ));
    }
    else{
      wp_send_json_error('Please select leads from the table.');
    } 
    exit;
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

    // global $wpdb;

    // $result = $wpdb->delete($wpdb->prefix . "ghaxlt_leads_payments", array('lead_id' => $del_val), array('%d'));

    // if ($result !== false) {
    //     echo "Row with ID ".$del_val." successfully deleted.";
    //     $wpdb->update($wpdb->prefix . "ghaxlt_leads", array('status' => 'open'), array('id' => $del_val));
    // } else {
    //     echo "Failed to delete row with ID ".$del_val;
    // }

    die();
  }
}
