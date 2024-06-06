<?php
require_once GHAX_LEADTRAIL_ABSPATH . 'classes/ghx-traits.php';

class GHAX_Admin_Ajax
{

  public function __construct()
  {
    add_action('wp_ajax_add_admin_note_action', array(&$this, 'add_admin_note_action'));
    add_action('wp_ajax_add_buyer_note_action', array(&$this, 'add_buyer_note_action'));
    add_action('wp_ajax_all_delete_action', array(&$this, 'all_delete_action'));
  }

  function add_admin_note_action()
  {
    global $wpdb;
    $id = (int) $_POST['id'];
    $table = sanitize_text_field($_POST['table']);
    $note = sanitize_textarea_field($_POST['note']);
    // $wpdb->query("alter table $table add column buyer_note longtext DEFAULT NULL");
    $wpdb->update($table, array('admin_note' => $note), array('id' => $id));

    echo "<p>Note updated successfully.</p>";
    exit();
  }

  function add_buyer_note_action()
  {
    global $wpdb;
    $id = (int) $_POST['id'];
    $table = sanitize_text_field($_POST['table']);
    $note = sanitize_textarea_field($_POST['note']);
    // $wpdb->query("alter table $table add column buyer_note longtext DEFAULT NULL");
    $wpdb->update($table, array('buyer_note' => $note), array('id' => $id));

    echo "<p>Note updated successfully.</p>";
    exit();
  }

  function all_delete_action()
  {
    global $wpdb;
    $id = (int) $_POST['id'];
    $table = sanitize_text_field($_POST['table']);

    switch ($table) {
      case 'leads':
        $table = $wpdb->prefix . 'ghaxlt_leads';
        break;
      case 'groups';
        $table = $wpdb->prefix . 'ghaxlt_lead_groups';
        break;
      case 'qualities':
        $table = $wpdb->prefix . 'ghaxlt_lead_qualities';
        break;
      case 'cats':
        $table = $wpdb->prefix . 'ghaxlt_lead_cats';
        break;
        // case 'payments':
        //   $table = $wpdb->prefix . 'ghaxlt_leads_payments';
        //   break;
    }

    if ($table) {
      $wpdb->delete($table, array('id' => $id));
    }

    die('success');
  }
}
