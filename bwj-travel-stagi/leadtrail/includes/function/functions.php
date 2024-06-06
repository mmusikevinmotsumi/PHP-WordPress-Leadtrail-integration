<?php

/** @wordpress-plugin
 * Author:            cWebco WP Plugin Team
 * Author URI:        https://leadtrail.io/
 */
/* Function for session message */

if (!function_exists('set_error_message')) {
  function set_error_message($msg, $type)
  {
    @session_start();
    if (isset($_SESSION['error_msg'])) :
      unset($_SESSION['error_msg']);
    endif;

    $_SESSION['error_msg']['msg'] = $msg;
    $_SESSION['error_msg']['error'] = $type;
    return true;
  }
}

if (!function_exists('show_error_message')) {
  function show_error_message()
  {
    $msg = '';
    @session_start();

    if (isset($_SESSION['error_msg']) && isset($_SESSION['error_msg']['msg'])) :
      if ($_SESSION['error_msg']['error'] == '1') :
        $tp = 'message_error';
      else :
        $tp = 'message_success';
      endif;
      $msg .= '<div class="portlet light pro_mess"><div class="message center pmpro_message ' . $tp . '">';
      $msg .= sanitize_text_field($_SESSION['error_msg']['msg']);
      $msg .= '</div></div>';
      unset($_SESSION['error_msg']['msg']);
      unset($_SESSION['error_msg']['error']);
      unset($_SESSION['error_msg']);
    endif;

    return $msg;
  }
}

if (!function_exists('pr')) {
  function pr($post)
  {
    echo '<pre>';
    print_r($post);
    echo '</pre>';
  }
}

if (!function_exists('do_account_redirect')) {
  //Finishing setting templates 
  function do_account_redirect($url)
  {
    global $post, $wp_query;

    if (have_posts()) {
      include($url);
      die();
    } else {
      $wp_query->is_404 = true;
    }
  }
}

function ghax_obfuscate_email($email)
{
  $em   = explode("@", $email);
  $name = implode('@', array_slice($em, 0, count($em) - 1));
  $len  = floor(strlen($name) / 2);
  $endlen  = floor(strlen(end($em)));

  return substr($name, 0, $len) . str_repeat('*', $len) . "@" . str_repeat('*', $endlen);
}


function ghax_checkEmail($email)
{
  $find1 = strpos($email, '@');
  $find2 = strpos($email, '.');
  return ($find1 !== false && $find2 !== false && $find2 > $find1);
}

/* Function for Redirect */
if (!function_exists('foreceRedirect')) {
  function ghax_foreceRedirect($filename)
  {
    if (!headers_sent())
      header('Location: ' . esc_url($filename));
    else {
      echo '<script type="text/javascript">';
      echo 'window.location.href="' . esc_html($filename) . '";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url=' . esc_html($filename) . '" />';
      echo '</noscript>';
    }
  }
}

//Not used anywhere
// function verifyTransaction($data)
// {
//   global $paypalUrl;

//   $req = 'cmd=_notify-validate';
//   foreach ($data as $key => $value) {
//     $value = urlencode(stripslashes($value));
//     $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
//     $req .= "&$key=$value";
//   }

//   $ch = curl_init($paypalUrl);
//   curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//   curl_setopt($ch, CURLOPT_POST, 1);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//   curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
//   curl_setopt($ch, CURLOPT_SSLVERSION, 6);
//   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
//   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
//   curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
//   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
//   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
//   $res = curl_exec($ch);

//   if (!$res) {
//     $errno = curl_errno($ch);
//     $errstr = curl_error($ch);
//     curl_close($ch);
//     throw new Exception("cURL error: [$errno] $errstr");
//   }

//   $info = curl_getinfo($ch);

//   // Check the http response
//   $httpCode = $info['http_code'];
//   if ($httpCode != 200) {
//     throw new Exception("PayPal responded with http code $httpCode");
//   }

//   curl_close($ch);

//   return $res === 'VERIFIED';
// }

function ghax_checkTxnid($txnid)
{
  global $db;

  $txnid = $db->real_escape_string($txnid);
  $results = $db->query('SELECT * FROM `payments` WHERE txnid = \'' . $txnid . '\'');

  return !$results->num_rows;
}

/*add_action('init',send_email_notification_on_lead_creation);*/
function send_email_notification_on_lead_creation($lead)
{
  global $wpdb;
  $leaddetail_page = get_option('_leaddetail_page');
  $args = array(
    'role' => 'ghaxlt_buyer',
  );
  $buyers = get_users($args);

  foreach ($buyers as $buyer) {
    $uid = $buyer->data->ID;
    $receive_lead_notifications = get_user_meta($uid, 'receive_lead_notifications', true);
    if ($receive_lead_notifications == 'Yes') {
      $to = $buyer->data->user_email;

      $subject = 'New Lead Posted';

      $headers = "From: " . strip_tags(get_option('admin_email')) . "\r\n";
      $headers .= "Reply-To: " . strip_tags(get_option('admin_email')) . "\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
      $message = '<html><body>';
      $message .= '<div class="emailcontainer" style="border:2px solid #74499E;">';
      /* $message .= '<div class="emailheader" style="background:#74499E;color:#fff;padding:1%;">';
			$message .= '<h3 style="text-align:center;">LeadTrail - New Lead Created</h3>';
			$message .= '</div>'; */
      $message .= '<div class="emailcontent" style="background:#fff;padding:2%;">';
      /* $message .= '<p><strong>Dear '.$buyer->data->display_name.',</strong></p>'; */
      $message .= '<p>A new lead has been posted on ' . get_bloginfo("url") . '. <a target="_blank" style="text-decoration:none;color: #734A9D;font-weight:bold" href="' . get_permalink($leaddetail_page) . '/?lead=' . $lead . '">Click here</a> to view it.</p>';
      /* $message .= '<p>Thanks</p>';
			$message .= '<p><strong>Leadtrail Team</strong></p>'; */
      $message .= '</div>';
      $message .= '</div>';
      $message .= '</body></html>';

      wp_mail($to, $subject, $message, $headers);
    }
  }
}

function ghax_search($array, $key, $value)
{
  $results = array();
  if (is_array($array)) {
    if (isset($array[$key]) && $array[$key] == $value) {
      $results[] = $array;
    }
    foreach ($array as $subarray) {
      $results = array_merge(
        $results,
        ghax_search($subarray, $key, $value)
      );
    }
  }
  return $results;
}
