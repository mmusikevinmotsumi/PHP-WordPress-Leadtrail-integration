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

    $id = array((int) $_POST['id']);
    if ($leadcart) {
      if (count($leadcart) >= $max_lead_purchase) {
        echo "You can purchase maximum " . (int) $max_lead_purchase . " leads at a time";
        die();
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
