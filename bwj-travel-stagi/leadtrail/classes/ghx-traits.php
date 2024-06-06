<?php

class GHAX_Traits
{
  public static function leadtrail_error($msg = '')
  {
    $msg = esc_html($msg);
    update_option('leadtrail_error', $msg);

    wp_redirect(admin_url('admin.php?page=' . (string) $_GET['page'] . '&lterror=1'));
    exit();
  }

  public static function leadtrail_success($msg = '')
  {
    $msg = esc_html($msg);
    update_option('leadtrail_success', $msg);

    $page = esc_attr($_GET['page']);

    if ($page == 'edit_lead_data') {
      $page = 'leads';
    } else if ($page == 'create_group' || $page == 'edit_group') {
      $page = 'lead_groups';
    } else if ($page == 'create_quality' || $page == 'edit_quality') {
      $page = 'lead_qualities';
    } else if ($page == 'create_category' || $page == 'edit_category') {
      $page = 'lead_categories';
    }

    wp_redirect(admin_url('admin.php?page=' . $page . '&ltsuccess=1'));
    exit();
  }

  public static function leadtrail_activate_license($license)
  {
    $leadtrail_license_key = $license;

    $act = wp_remote_get(GHAX_LICENSE_PURCHASE_URL . '/?ltactivate=' . $leadtrail_license_key);
    $result = wp_remote_retrieve_body($act);
    $result = json_decode($result, true);

    if ($result['data']['status'] == '404') {
      SELF::leadtrail_error($result['message']);
    } else {
      if ($result['success'] == true) {
        update_option('leadtrail_license_key', $leadtrail_license_key);
        update_option('leadtrail_license_status', 'active');
        update_option('leadtrail_license_expiry_date', $result['data']['expiresAt']);
        wp_redirect(admin_url('admin.php?page=leadtrail'));
      }
    }
  }


  public static function ghax_csv_to_array($filename = '', $delimiter = ',')
  {
    if (!file_exists($filename) || !is_readable($filename))
      return FALSE;

    $header = [];
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        if (!$header)
          $header = $row;
        else
          $data[] = array_combine($header, $row);
      }
      fclose($handle);
    }
    return $data;
  }
}
