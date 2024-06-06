<?php

/** @wordpress-plugin
 * Author:            GHAX
 * Author URI:        https://leadtrail.io/
 */

class leadtrail_Activator
{
  /* Activate Class */
  public static function activate_leadtrail()
  {

    global $wpdb;


    /* Leads information Table */

    $sqlQuery = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "ghaxlt_leads` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`data` longtext DEFAULT NULL,
					`submitted_by` varchar(255) DEFAULT NULL,
					`form_name` varchar(255) DEFAULT NULL,
					`created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					`category` int(11) DEFAULT NULL,
					`tags` varchar(255) DEFAULT NULL,
					`group` int(11) DEFAULT NULL,
					`status` varchar(255) DEFAULT NULL,
					`publish` varchar(255) DEFAULT NULL,
					`quality` int(11) DEFAULT NULL,
					`lead_quantity` int(11) DEFAULT NULL,
					`discount_quantity` int(11) DEFAULT NULL,
					`lead_discount` float(10,2) DEFAULT NULL,
          `admin_note` varchar(255) DEFAULT NULL,
          `buyer_note` varchar(255) DEFAULT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    $wpdb->query($sqlQuery);

    $row = $wpdb->get_results("SELECT `DATA_TYPE` FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $wpdb->prefix . "ghaxlt_leads' AND column_name = 'category'");
    if ($row && $row[0]->DATA_TYPE != 'int') {
      $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "ghaxlt_leads` MODIFY `category` int(11) DEFAULT NULL;");
    }

    $row = $wpdb->get_results("SELECT `DATA_TYPE` FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $wpdb->prefix . "ghaxlt_leads' AND column_name = 'quality'");
    if ($row && $row[0]->DATA_TYPE != 'int') {
      $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "ghaxlt_leads` MODIFY `quality` int(11) DEFAULT NULL;");
    }

    $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $wpdb->prefix . "ghaxlt_leads' AND column_name = 'lead_quantity'");
    if (empty($row)) {
      $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "ghaxlt_leads` ADD `lead_quantity` int(11) DEFAULT NULL;");
    }
    $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $wpdb->prefix . "ghaxlt_leads' AND column_name = 'discount_quantity'");
    if (empty($row)) {
      $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "ghaxlt_leads` ADD `discount_quantity` int(11) DEFAULT NULL;");
    }
    $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $wpdb->prefix . "ghaxlt_leads' AND column_name = 'lead_discount'");
    if (empty($row)) {
      $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "ghaxlt_leads` ADD `lead_discount` float(10,2) DEFAULT NULL;");
    }

    /*** creating leads groups table */

    $sqlQuery1 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "ghaxlt_lead_groups` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` longtext DEFAULT NULL,
					`image` varchar(255) DEFAULT NULL,
					`forms` longtext DEFAULT NULL,
					`price` float(10,2) DEFAULT 0,
					`created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    $wpdb->query($sqlQuery1);

    $row = $wpdb->get_results("SELECT `DATA_TYPE` FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . $wpdb->prefix . "ghaxlt_lead_groups' AND column_name = 'price'");
    if ($row && $row[0]->DATA_TYPE != 'float') {
      $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "ghaxlt_lead_groups` MODIFY `price` float(10,2) DEFAULT 0;");
    }



    /*** creating leads quality table */

    $sqlQuery4 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "ghaxlt_lead_qualities` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` longtext DEFAULT NULL,
					`price` float(10,2) DEFAULT 0,
					`created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    $wpdb->query($sqlQuery4);

    /*** creating leads category/tag table */

    $sqlQuery2 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "ghaxlt_lead_cats` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` longtext DEFAULT NULL,
					`image` varchar(255) DEFAULT NULL,
					`price` float(10,2) DEFAULT 0,
					`type` varchar(255) DEFAULT NULL,
					`created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    $wpdb->query($sqlQuery2);

    /**** create lead payments table ******/
    $sqlQuery3 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "ghaxlt_leads_payments` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`user_id` int(11) DEFAULT NULL,
					`lead_id` int(11) DEFAULT NULL,
					`payment_by` varchar(255) DEFAULT NULL,
					`amount` varchar(255) DEFAULT NULL,
					`payment_id` varchar(255) DEFAULT NULL,
					`transaction_type` varchar(50) DEFAULT 'sandbox',
					`created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    $wpdb->query($sqlQuery3);


    /* Adding lead detail page*/
    $leaddetail_not_exist = $leadpurchase_not_exist = $leadbuyerdashboard_not_exist = $leaddisplayall_not_exist = 0;

    /**************************************/

    $_leaddetail_page = get_option('_leaddetail_page');
    $_leadpurchase_page = get_option('buy_lead_page');
    $_leadbuyerdashboard_page = get_option('_leadbuyerdashboard_page');
    $_leaddisplayall_page = get_option('_leaddisplayall_page');


    /**************************************/


    if (!empty($_leaddetail_page)) {
      if (FALSE === get_post_status($_leaddetail_page)) {
        $leaddetail_not_exist = 1;
      }
    } else {
      $leaddetail_not_exist = 1;
    }
    /**************************************/

    if (!empty($_leadpurchase_page)) {
      if (FALSE === get_post_status($_leadpurchase_page)) {
        $leadpurchase_not_exist = 1;
      } else {
      }
    } else {
      $leadpurchase_not_exist = 1;
    }

    /**************************************/

    if (!empty($_leadbuyerdashboard_page)) {
      if (FALSE === get_post_status($_leadbuyerdashboard_page)) {
        $leadbuyerdashboard_not_exist = 1;
      }
    } else {
      $leadbuyerdashboard_not_exist = 1;
    }

    /**************************************/

    if (!empty($_leaddisplayall_page)) {
      if (FALSE === get_post_status($_leaddisplayall_page)) {
        $leaddisplayall_not_exist = 1;
      }
    } else {
      $leaddisplayall_not_exist = 1;
    }




    /**************************************/
    if ($leaddetail_not_exist == 1) {
      $page['post_type']    = 'page';
      $page['post_content'] = '[lead-detail]';
      $page['post_parent']  = 0;
      $page['post_author']  = 1;
      $page['post_status']  = 'publish';
      $page['post_title']   = 'Lead Detail';
      $leaddetail_pageid = wp_insert_post($page);
      update_option('_leaddetail_page', $leaddetail_pageid);
    }

    /**************************************/

    if ($leadpurchase_not_exist == 1) {
      $ppage['post_type']    = 'page';
      $ppage['post_content'] = '[buy-lead]';
      $ppage['post_parent']  = 0;
      $ppage['post_author']  = 1;
      $ppage['post_status']  = 'publish';
      $ppage['post_title']   = 'Lead Purchase';
      $leadpurchase_pageid = wp_insert_post($ppage);
      update_option('buy_lead_page', $leadpurchase_pageid);
    }

    /**************************************/

    if ($leadbuyerdashboard_not_exist == 1) {
      $bppage['post_type']    = 'page';
      $bppage['post_content'] = '[buyer-dashboard]';
      $bppage['post_parent']  = 0;
      $bppage['post_author']  = 1;
      $bppage['post_status']  = 'publish';
      $bppage['post_title']   = 'Buyer Dashboard';
      $leadbuyerdashboard_pageid = wp_insert_post($bppage);
      update_option('_leadbuyerdashboard_page', $leadbuyerdashboard_pageid);
    }

    /**************************************/

    if ($leaddisplayall_not_exist == 1) {
      $ldpage['post_type']    = 'page';
      $ldpage['post_content'] = '[display-all-leads]';
      $ldpage['post_parent']  = 0;
      $ldpage['post_author']  = 1;
      $ldpage['post_status']  = 'publish';
      $ldpage['post_title']   = 'Display All Leads';
      $leaddisplatall_pageid = wp_insert_post($ldpage);
      update_option('_leaddisplayall_page', $leaddisplatall_pageid);
    }
  }
}
