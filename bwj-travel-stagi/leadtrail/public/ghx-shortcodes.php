<?php

require_once GHAX_LEADTRAIL_ABSPATH . 'includes/function/functions.php';

class GHAX_Shortcode_Manager
{
  private $payment_mode;
  private $tbllds, $tblcats, $tblgrps, $tblqlty, $tblpymts;

  public function __construct()
  {
    global $wpdb;

    $this->payment_mode = (strtoupper(get_option('paypal_mode')) == 'LIVE' && strtoupper(get_option('stipe_mode')) == 'LIVE') ? 'live' : 'sandbox';

    $this->tbllds = $wpdb->prefix . 'ghaxlt_leads';
    $this->tblcats = $wpdb->prefix . 'ghaxlt_lead_cats';
    $this->tblgrps = $wpdb->prefix . 'ghaxlt_lead_groups';
    $this->tblqlty = $wpdb->prefix . 'ghaxlt_lead_qualities';
    $this->tblpymts = $wpdb->prefix . 'ghaxlt_leads_payments';

    add_shortcode('display-all-leads', [$this, 'display_all_leads_list']);
    add_shortcode('display-category-leads', [$this, 'display_category_leads_list']);
    add_shortcode('display-quality-leads', [$this, 'display_quality_leads_list']);
    add_shortcode('display-group-leads', [$this, 'display_group_leads_list']);

    add_shortcode('buy-lead', [$this, 'buy_GHAXlt_lead']);
    add_shortcode('buyer-dashboard', [$this, 'display_buyer_dashboard']);

    add_shortcode('lead-detail', [$this, 'display_lead_details']);
  }

  public function display_all_leads_list()
  {
    ob_start();
    global $wpdb;

    $buy_lead_page = get_option('buy_lead_page');
    $buyer_dashboard_page = get_permalink(get_option('_leadbuyerdashboard_page'));

    $user = wp_get_current_user();
    if (!in_array('ghaxlt_buyer', (array) $user->roles) && !in_array('administrator', (array) $user->roles) && !in_array('ghaxlt_annual_buyer', (array) $user->roles) && !in_array('ghaxlt_monthly_buyer', (array) $user->roles)) {

      echo '<p>You are not allowed to view this content.</p>';
    } else {

      $price_result = $wpdb->get_results("select MIN(COALESCE(grps.price,0)+COALESCE(qlty.price,0)+COALESCE(cats.price,0)) as min_price,MAX(COALESCE(grps.price,0)+COALESCE(qlty.price,0)+COALESCE(cats.price,0)) as max_price from {$wpdb->prefix}ghaxlt_leads as gaxlead  left join  {$this->tblgrps} as grps on gaxlead.group=grps.id left join  {$this->tblqlty} as qlty on gaxlead.quality=qlty.id left join {$this->tblcats} as cats on gaxlead.category=cats.id WHERE gaxlead.publish=1");

      $results = $wpdb->get_results("select gaxlead.*,coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),1) as new_lead_quantity,cat.name as cat_name,grps.name as group_name,qlty.name as quality_name,COALESCE(grps.price,0)+COALESCE(qlty.price,0)+COALESCE(cat.price,0) as totalprice,(SELECT count(*) as count FROM {$this->tblpymts} WHERE lead_id=gaxlead.id and transaction_type='{$this->payment_mode}' order by id desc) as buylead from {$wpdb->prefix}ghaxlt_leads as gaxlead left join  {$this->tblcats} as cat on gaxlead.category=cat.id left join  {$this->tblgrps} as grps on gaxlead.group=grps.id left join  {$this->tblqlty} as qlty on gaxlead.quality=qlty.id WHERE gaxlead.publish=1 having coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),0)>=buylead ORDER BY id DESC");

      // print_r($results);
      // exit();
      //$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ghaxlt_leads WHERE `publish`=1  order by id desc");

      if (count($results) > 0) {
?>

        <div class="container">
          <div class="leadssrt">
            <h2>Displaying Leads by All</h2>
            <?php $lead_field_display = get_option('lead_field_display');
            if ($lead_field_display) {
            } else {
              $lead_field_display = array();
            }  ?>
            <?php
            $style = 'style="display:none"'; ?>
            <?php 
                $multiple_lead = get_option('multiple_lead_show', false);
                                       
                if ($multiple_lead) { ?>
                  <div class="lead-main-wrap" style="display:none;">
                    <div class="top-hdr-info">
                      <p style="font-weight:bold;;">You are requesting access to the following lead:</p>
                      <ul>                        
                      </ul>
                    </div>
                    <div class="float-end text-end mt-4 mb-4">
                      <div class="form-holder">
                        <div class="cart-btn-top">
                          <a class="buyleadbtn confirmaddtocart"  href="javascript:void(0)">Confirm</a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>
            <table id="leadstbl" style="width:100%;" class="table table-bordered">
              <thead>
                <th <?php echo (in_array('email', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Email</th>
                <th <?php echo (in_array('full_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Name</th>
                <th <?php echo (in_array('from_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Form Name</th>
                <th <?php echo (in_array('purchased_count', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Purchase Count</th>
                <th <?php echo (in_array('additional_info', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Additional Info</th>
                <th <?php echo (in_array('category', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Category</th>
                <th <?php echo (in_array('group', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Group</th>
                <th <?php echo (in_array('status', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Status</th>
                <th <?php echo (in_array('quality', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Quality</th>
                <th <?php echo (in_array('country', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Country</th>
                <th <?php echo (in_array('state', $lead_field_display)) ? '' : 'style="display:none"'; ?>>State</th>
                <th <?php echo (in_array('city', $lead_field_display)) ? '' : 'style="display:none"'; ?>>City</th>
                <th <?php echo (in_array('zipcode', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Zipcode</th>
                <th <?php echo (in_array('price', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Price</th>
                <th style="display:none"></th>
                <th <?php echo (in_array('published', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Published</th>
                <th <?php echo (in_array('created', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Created On</th>
                <th>Access Lead</th>
              </thead>
              <tbody>
                <?php
                foreach ($results as $result) {

                  if ($result->buylead >= $result->new_lead_quantity) {
                    continue;
                  }
                  // echo $result -> id;
                  $myarr = json_decode($result->data, true);
                  $myemail = "N/A";
                  $full_name = "N/A";
                  $city = "N/A";
                  $state = "N/A";
                  $zipcode = "N/A";
                  $country = "N/A";
                  foreach ($myarr as $key => $value) {

                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                      $myemail = $value;
                    }
                    if ($key == 'Name') {
                      $full_name = $value;
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
                    if ($key == 'Additional Info') {
                      $additional_info = $value;
                    }
                  }
                  $price = $result->totalprice;
                ?>

                  <tr id="delete_<?php echo esc_attr($result->id); ?>" name="<?php echo $full_name;?>">
                    <td <?php echo (in_array('email', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ghax_obfuscate_email($myemail); ?></td>
                    <td <?php echo (in_array('full_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($full_name); ?></td>
                    <td <?php echo (in_array('from_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->form_name) ? esc_html($result->form_name) : 'N/A'; ?></td>
                    <td <?php echo (in_array('purchased_count', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($result->buylead . '/' . $result->new_lead_quantity); ?></td>
                    <td <?php echo (in_array('additional_info', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($additional_info); ?></td>
                    <td <?php echo (in_array('category', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->cat_name) ? esc_html($result->cat_name) : 'N/A'; ?></td>
                    <td <?php echo (in_array('group', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->group_name) ? $result->group_name : 'N/A'; ?></td>
                    <td <?php echo (in_array('status', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->status) ? esc_html($result->status) : 'N/A'; ?></td>
                    <td <?php echo (in_array('quality', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->quality_name) ? esc_html($result->quality_name) : 'N/A'; ?></td>
                    <td <?php echo (in_array('country', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($country); ?></td>
                    <td <?php echo (in_array('state', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($state); ?></td>
                    <td <?php echo (in_array('city', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($city); ?></td>
                    <td <?php echo (in_array('zipcode', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($zipcode); ?></td>
                    <td <?php echo (in_array('price', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                      <?php
                      // if ($price) {
                      //   if ($result->discount_quantity) {
                      //     if ($result->lead_discount) {
                      //       if ($result->buylead >= $result->discount_quantity) {
                      //         $discount_multi = floor($result->buylead / $result->discount_quantity);
                      //         echo "<del>" . esc_html(get_option('lead_currency') . $price) . "</del> ";
                      //         $price = $price - ((($result->lead_discount * $price) / 100) * $discount_multi);
                      //         if ($price <= 0) {
                      //           $price = 0;
                      //         }
                      //       }
                      //     }
                      //   }
                      //   echo esc_html(get_option('lead_currency') . $price);
                      // } else {
                      //   echo 'N/A';
                      // } 
                      ?>
                    </td>
                    <td style="display:none">
                      <?php 
                      // if ($price && $price >= 0) {
                      //   echo $price;
                      // } else {
                      //   echo 0;
                      // } 
                      ?>
                    </td>
                    <td <?php echo (in_array('published', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                      <?php if ($result->publish == 1) {
                        echo "Yes";
                      } else {
                        echo "No";
                      } ?>
                    </td>
                    <td <?php echo (in_array('created', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?></td>
                    <?php
                    $user_id = get_current_user_id();
                    $leadcart = get_user_meta($user_id, 'leadcart', true);
                    if ($multiple_lead) {
                      if ($leadcart) {
                        if (in_array($result->id, $leadcart)) { ?>
                          <td>
                            <!-- <a class="added buyleadbtn" href="javascript:void(0)">Confirm</a>  -->
                            <a class="added remove_cart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Remove</a>
                          </td>
                        <?php
                        } else { ?>
                          <td><a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Get Access</a> </td>
                        <?php
                        } ?>
                      <?php } else { ?>
                        <td><a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Get Access</a> </td>
                      <?php
                      }
                    } else { ?>
                      <td><a class="directbuy buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Get Access</a></td>
                    <?php } ?>

                  </tr>
                <?php
                }
                ?>
              <tbody>
            </table>
          </div>
        </div>
        <script>
          jQuery("a.added").parents("tr").addClass("added");

          jQuery(".lead-main-wrap .top-hdr-info ul li").remove();

          if (jQuery("a.added").length>0){
            jQuery(".lead-main-wrap").show();
          }
          else{
            jQuery(".lead-main-wrap").hide();
          }
          jQuery("a.added").each(function(){
            jQuery(".lead-main-wrap .top-hdr-info ul").append("<li>" + jQuery(this).parents("tr").attr("name") + "</li>")
          })
        </script>
        <script>
          function sortTableById() {
              var table, rows, switching, i, x, y, shouldSwitch;
              table = document.getElementById("leadstbl");
              switching = true;

              // Loop to keep switching until no switching has been done
              while (switching) {
                  switching = false;
                  rows = table.rows;

                  // Loop through all table rows (except the headers)
                  for (i = 1; i < (rows.length - 1); i++) {
                      shouldSwitch = false;
                      
                      // Get the two elements to compare
                      x = rows[i].getAttribute("id");
                      y = rows[i + 1].getAttribute("id");

                      // Compare the two rows
                      if (parseInt(x.split("_")[1]) > parseInt(y.split("_")[1])) {
                          shouldSwitch = true;
                          break;
                      }
                  }
                  if (shouldSwitch) {
                      // If a switch is needed, perform the switch and mark that a switch has been done
                      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                      switching = true;
                  }
              }
          }

          // Call the sorting function after the page has loaded
          window.onload = function() {
              sortTableById();
          };
        </script>
      <?php
      } else {
        echo '<p>No leads found.</p>';
      }
    }
    return ob_get_clean();
  }

  function display_category_leads_list($atts)
  {

    ob_start();
    global $wpdb;

    $buy_lead_page = get_option('buy_lead_page');
    $user = wp_get_current_user();
    if (!in_array('ghaxlt_buyer', (array) $user->roles) && !in_array('administrator', (array) $user->roles) && !in_array('ghaxlt_annual_buyer', (array) $user->roles) && !in_array('ghaxlt_monthly_buyer', (array) $user->roles)) {
      echo '<p>You are not allowed to view this content.</p>';
    } else {
      $q1 = "SELECT * FROM {$wpdb->prefix}ghaxlt_lead_cats WHERE id=" . $atts['category'];
      $r1 = $wpdb->get_row($q1);


      $price_result = $wpdb->get_results("select MIN(COALESCE(grps.price,0)+COALESCE(qlty.price,0)) as min_price,MAX(COALESCE(grps.price,0)+COALESCE(qlty.price,0)) as max_price from {$wpdb->prefix}ghaxlt_leads as gaxlead  left join  {$this->tblgrps} as grps on gaxlead.group=grps.id left join  {$this->tblqlty} as qlty on gaxlead.quality=qlty.id WHERE gaxlead.publish=1 AND gaxlead.category='" . $atts['category'] . "'");

      $results = $wpdb->get_results("select gaxlead.*,coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),1) as new_lead_quantity,cat.name as cat_name,grps.name as group_name,qlty.name as quality_name,COALESCE(grps.price,0)+COALESCE(qlty.price,0) as totalprice,(SELECT count(*) as count FROM {$this->tblpymts} WHERE lead_id=gaxlead.id and transaction_type='{$this->payment_mode}' order by id desc) as buylead from {$wpdb->prefix}ghaxlt_leads as gaxlead left join  {$this->tblcats} as cat on gaxlead.category=cat.id left join  {$this->tblgrps} as grps on gaxlead.group=grps.id left join  {$this->tblqlty} as qlty on gaxlead.quality=qlty.id WHERE gaxlead.publish=1 AND gaxlead.category='" . $atts['category'] . "' having coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),0)>=buylead");

      //print_r($results);
      //$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ghaxlt_leads WHERE `publish`=1  order by id desc");

      if (count($results) > 0) {
      ?>

        <div class="leadssrt">
          <h2>Displaying Leads by Category - <?php echo esc_html(ucfirst($r1->name)); ?></h2>
          <div class="filter-holder">
            <div class="filter-left-wrap">
              <div class="row">
                <?php

                $countries = file_get_contents(GHAX_LEADTRAIL_ABSPATH . '/includes/classes/json/countries.json');
                $array_countries = json_decode($countries, true);
                $option = "<option value=''>Please select the country</option>";
                foreach ($array_countries as $array_country) {
                  $option .= "<option value='" . esc_attr($array_country['iso2']) . "'>" . esc_html($array_country['name']) . "</option>";
                }  ?>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>Country:</label>
                    <select name="country" id="country" data-column="7" onchange="leadsearch_attribute()" class="custom-input"><?php echo $option; ?></select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder select-state-text">
                    <label>State:</label>
                    <input type="text" name="state" id="state" data-column="8" onkeyup="leadsearch_attribute()" placeholder="State" class="custom-input">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>City:</label>
                    <input type="text" name="city" id="city" data-column="9" onkeyup="leadsearch_attribute()" placeholder="City" class="custom-input">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>Enter Zip</label>
                    <input type="text" name="zipcode" id="zipcode" data-column="10" onkeyup="leadsearch_attribute()" class="custom-input" placeholder="zip">
                  </div>
                </div>

                <?php
                if (count($price_result) > 0) { ?>
                  <div class="col-md-4 label-wrap">
                    <div class="form-holder">
                      <div class="label">
                        <label>Price:</label>
                      </div>
                      <div slider id="slider-distance">
                        <div>
                          <div inverse-left style="width:70%;"></div>
                          <div inverse-right style="width:70%;"></div>
                          <div range style="left:0%;right:0%;"></div>
                          <span thumb style="left:0%;"></span>
                          <span thumb style="left:100%;"></span>
                          <div sign style="left:0%;">
                            <span id="value"><?php echo 0; ?></span>
                          </div>
                          <div sign style="left:100%;">
                            <span id="value"><?php echo $price_result[0]->max_price; ?></span>
                          </div>
                        </div>
                        <input type="range" tabindex="0" onchange="leadsearch_attribute()" id="attribute_min_price" value="0" max="<?php echo (int) $price_result[0]->max_price; ?>" min="0" step="1" oninput="
                                      this.value=Math.min(this.value,this.parentNode.childNodes[5].value-1);
                                      var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                      var children = this.parentNode.childNodes[1].childNodes;
                                      children[1].style.width=value+'%';
                                      children[5].style.left=value+'%';
                                      children[7].style.left=value+'%';children[11].style.left=value+'%';
                                      children[11].childNodes[1].innerHTML=this.value;" />

                        <input type="range" tabindex="0" onchange="leadsearch_attribute()" id="attribute_max_price" value="<?php echo (int) $price_result[0]->max_price; ?>" max="<?php echo $price_result[0]->max_price; ?>" min="0" step="1" oninput="
                                      this.value=Math.max(this.value,this.parentNode.childNodes[3].value-(-1));
                                      var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                      var children = this.parentNode.childNodes[1].childNodes;
                                      children[3].style.width=(100-value)+'%';
                                      children[5].style.right=(100-value)+'%';
                                      children[9].style.left=value+'%';children[13].style.left=value+'%';
                                      children[13].childNodes[1].innerHTML=this.value;" />
                      </div>
                    </div>
                  </div>
                <?php } ?>
                <?php $multiple_lead = get_option('multiple_lead_show', false);
                if ($multiple_lead) { ?>
                  <div class="col-md-4">
                    <div class="form-holder">
                      <div class="cart-btn-top">
                        <a class="buyleadbtn" href="<?php echo get_permalink($buy_lead_page); ?>">View Cart</a>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>

          <?php $lead_field_display = get_option('cat_lead_field_display');
          if ($lead_field_display) {
          } else {
            $lead_field_display = array();
          }  ?>
          <?php
          $style = 'style="display:none"'; ?>
          <table id="leadstbl" style="width:100%;" class="table table-bordered">
            <thead>
              <th <?php echo (in_array('email', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Email</th>
              <th <?php echo (in_array('from_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Form Name</th>
              <th <?php echo (in_array('purchased_count', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Purchase Count</th>
              <th <?php echo (in_array('group', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Group</th>
              <th <?php echo (in_array('status', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Status</th>
              <th <?php echo (in_array('quality', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Quality</th>
              <th <?php echo (in_array('country', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Country</th>
              <th <?php echo (in_array('state', $lead_field_display)) ? '' : 'style="display:none"'; ?>>State</th>
              <th <?php echo (in_array('city', $lead_field_display)) ? '' : 'style="display:none"'; ?>>City</th>
              <th <?php echo (in_array('zipcode', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Zipcode</th>
              <th <?php echo (in_array('price', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Price</th>
              <th style="display:none"></th>
              <th <?php echo (in_array('published', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Published</th>
              <th <?php echo (in_array('created', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Created On</th>
              <th>Purchase</th>
            </thead>
            <tbody>
              <?php
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
                  <td <?php echo (in_array('email', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ghax_obfuscate_email($myemail); ?></td>
                  <td <?php echo (in_array('from_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->form_name) ? esc_html($result->form_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('purchased_count', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($result->buylead . '/' . $result->new_lead_quantity); ?></td>

                  <td <?php echo (in_array('group', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->group_name) ? $result->group_name : 'N/A'; ?></td>
                  <td <?php echo (in_array('status', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->status) ? esc_html($result->status) : 'N/A'; ?></td>
                  <td <?php echo (in_array('quality', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->quality_name) ? esc_html($result->quality_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('country', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($country); ?></td>
                  <td <?php echo (in_array('state', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($state); ?></td>
                  <td <?php echo (in_array('city', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($city); ?></td>
                  <td <?php echo (in_array('zipcode', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($zipcode); ?></td>
                  <td <?php echo (in_array('price', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php
                    if ($price) {
                      if ($result->discount_quantity) {
                        if ($result->lead_discount) {
                          if ($result->buylead >= $result->discount_quantity) {
                            $discount_multi = floor($result->buylead / $result->discount_quantity);
                            echo "<del>" . esc_html(get_option('lead_currency') . $price) . "</del> ";
                            $price = $price - ((($result->lead_discount * $price) / 100) * $discount_multi);
                            if ($price <= 0) {
                              $price = 0;
                            }
                          }
                        }
                      }
                      echo esc_html(get_option('lead_currency') . $price);
                    } else {
                      echo 'N/A';
                    } ?>
                  </td>
                  <td style="display:none"><?php if ($price && $price >= 0) {
                                              echo $price;
                                            } else {
                                              echo 0;
                                            } ?></td>
                  <td <?php echo (in_array('published', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php if ($result->publish == 1) {
                      echo "Yes";
                    } else {
                      echo "No";
                    } ?>
                  </td>
                  <td <?php echo (in_array('created', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?>
                  </td>
                  <?php
                  $user_id = get_current_user_id();
                  $leadcart = get_user_meta($user_id, 'leadcart', true);
                  if ($multiple_lead) {
                    if ($leadcart) {
                      if (in_array($result->id, $leadcart)) { ?>
                        <td><a class="added buyleadbtn" href="javascript:void(0)">Added</a> <a class="remove_cart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Remove</a></td>
                      <?php
                      } else { ?>
                        <td><a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Add to Cart</a> </td>
                      <?php
                      } ?>
                    <?php } else { ?>
                      <td><a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Add to Cart</a> </td>
                    <?php
                    }
                  } else { ?>
                    <td><a class="directbuy buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Buy Lead</a></td>
                  <?php } ?>

                </tr>
              <?php
              }
              ?>
            <tbody>
          </table>
        </div>
      <?php
      } else {
        echo '<p>No leads found.</p>';
      }
    }
    return ob_get_clean();
  }

  function display_quality_leads_list($atts)
  {
    ob_start();
    global $wpdb;

    $buy_lead_page = get_option('buy_lead_page');
    $user = wp_get_current_user();
    if (!in_array('ghaxlt_buyer', (array) $user->roles) && !in_array('administrator', (array) $user->roles) && !in_array('ghaxlt_annual_buyer', (array) $user->roles) && !in_array('ghaxlt_monthly_buyer', (array) $user->roles)) {
      echo '<p>You are not allowed to view this content.</p>';
    } else {

      //$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ghaxlt_leads WHERE `publish`=1 AND `status` = 'open' AND `quality`='".$atts['quality']."'");
      $q1 = "SELECT * FROM {$this->tblqlty} WHERE id=" . $atts['quality'];
      $r1 = $wpdb->get_row($q1);

      $price_result = $wpdb->get_results("select MIN(COALESCE(grps.price,0)+COALESCE(qlty.price,0)) as min_price,MAX(COALESCE(grps.price,0)+COALESCE(qlty.price,0)) as max_price from {$wpdb->prefix}ghaxlt_leads as gaxlead  left join  {$this->tblgrps} as grps on gaxlead.group=grps.id left join  {$this->tblqlty} as qlty on gaxlead.quality=qlty.id WHERE gaxlead.publish=1 AND gaxlead.quality='" . $atts['quality'] . "'");

      $results = $wpdb->get_results("select gaxlead.*,coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),1) as new_lead_quantity,cat.name as cat_name,grps.name as group_name,qlty.name as quality_name,COALESCE(grps.price,0)+COALESCE(qlty.price,0) as totalprice,(SELECT count(*) as count FROM {$this->tblpymts} WHERE lead_id=gaxlead.id and transaction_type='{$this->payment_mode}' order by id desc) as buylead from {$wpdb->prefix}ghaxlt_leads as gaxlead left join  {$this->tblcats} as cat on gaxlead.category=cat.id left join  {$this->tblgrps} as grps on gaxlead.group=grps.id left join  {$this->tblqlty} as qlty on gaxlead.quality=qlty.id WHERE gaxlead.publish=1 AND gaxlead.quality='" . $atts['quality'] . "' having coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),0)>=buylead");

      //print_r($results);
      //$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ghaxlt_leads WHERE `publish`=1  order by id desc");

      if (count($results) > 0) {
      ?>

        <div class="leadssrt">
          <h2>Displaying Leads by Quality - <?php echo esc_html(ucfirst($r1->name)); ?></h2>
          <div class="filter-holder">
            <div class="filter-left-wrap">
              <div class="row">
                <?php

                $countries = file_get_contents(GHAX_LEADTRAIL_ABSPATH . '/includes/classes/json/countries.json');
                $array_countries = json_decode($countries, true);
                $option = "<option value=''>Please select the country</option>";
                foreach ($array_countries as $array_country) {
                  $option .= "<option value='" . esc_attr($array_country['iso2']) . "'>" . esc_html($array_country['name']) . "</option>";
                }  ?>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>Country:</label>
                    <select name="country" id="country" data-column="7" onchange="leadsearch_attribute()" class="custom-input"><?php echo $option; ?></select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder select-state-text">
                    <label>State:</label>
                    <input type="text" name="state" id="state" data-column="8" onkeyup="leadsearch_attribute()" placeholder="State" class="custom-input">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>City:</label>
                    <input type="text" name="city" id="city" data-column="9" onkeyup="leadsearch_attribute()" placeholder="City" class="custom-input">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>Enter Zip</label>
                    <input type="text" name="zipcode" id="zipcode" data-column="10" onkeyup="leadsearch_attribute()" class="custom-input" placeholder="zip">
                  </div>
                </div>

                <?php
                if (count($price_result) > 0) { ?>
                  <div class="col-md-4 label-wrap">
                    <div class="form-holder">
                      <div class="label">
                        <label>Price:</label>
                      </div>
                      <div slider id="slider-distance">
                        <div>
                          <div inverse-left style="width:70%;"></div>
                          <div inverse-right style="width:70%;"></div>
                          <div range style="left:0%;right:0%;"></div>
                          <span thumb style="left:0%;"></span>
                          <span thumb style="left:100%;"></span>
                          <div sign style="left:0%;">
                            <span id="value"><?php echo 0; ?></span>
                          </div>
                          <div sign style="left:100%;">
                            <span id="value"><?php echo $price_result[0]->max_price; ?></span>
                          </div>
                        </div>
                        <input type="range" tabindex="0" onchange="leadsearch_attribute()" id="attribute_min_price" value="0" max="<?php echo (int) $price_result[0]->max_price; ?>" min="0" step="1" oninput="
                                      this.value=Math.min(this.value,this.parentNode.childNodes[5].value-1);
                                      var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                      var children = this.parentNode.childNodes[1].childNodes;
                                      children[1].style.width=value+'%';
                                      children[5].style.left=value+'%';
                                      children[7].style.left=value+'%';children[11].style.left=value+'%';
                                      children[11].childNodes[1].innerHTML=this.value;" />

                        <input type="range" tabindex="0" onchange="leadsearch_attribute()" id="attribute_max_price" value="<?php echo (int) $price_result[0]->max_price; ?>" max="<?php echo $price_result[0]->max_price; ?>" min="0" step="1" oninput="
                                      this.value=Math.max(this.value,this.parentNode.childNodes[3].value-(-1));
                                      var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                      var children = this.parentNode.childNodes[1].childNodes;
                                      children[3].style.width=(100-value)+'%';
                                      children[5].style.right=(100-value)+'%';
                                      children[9].style.left=value+'%';children[13].style.left=value+'%';
                                      children[13].childNodes[1].innerHTML=this.value;" />
                      </div>
                    </div>
                  </div>
                <?php } ?>
                <?php $multiple_lead = get_option('multiple_lead_show', false);
                if ($multiple_lead) { ?>
                  <div class="col-md-4">
                    <div class="form-holder">
                      <div class="cart-btn-top">
                        <a class="buyleadbtn" href="<?php echo get_permalink($buy_lead_page); ?>">View Cart</a>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>

          <?php $lead_field_display = get_option('quality_lead_field_display');
          if ($lead_field_display) {
          } else {
            $lead_field_display = array();
          }  ?>
          <?php
          $style = 'style="display:none"'; ?>
          <table id="leadstbl" style="width:100%;" class="table table-bordered">
            <thead>
              <th <?php echo (in_array('email', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Email</th>
              <th <?php echo (in_array('from_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Form Name</th>
              <th <?php echo (in_array('purchased_count', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Purchase Count</th>
              <th <?php echo (in_array('category', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Category</th>
              <th <?php echo (in_array('group', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Group</th>
              <th <?php echo (in_array('status', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Status</th>
              <th <?php echo (in_array('country', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Country</th>
              <th <?php echo (in_array('state', $lead_field_display)) ? '' : 'style="display:none"'; ?>>State</th>
              <th <?php echo (in_array('city', $lead_field_display)) ? '' : 'style="display:none"'; ?>>City</th>
              <th <?php echo (in_array('zipcode', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Zipcode</th>
              <th <?php echo (in_array('price', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Price</th>
              <th style="display:none"></th>
              <th <?php echo (in_array('published', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Published</th>
              <th <?php echo (in_array('created', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Created On</th>
              <th>Purchase</th>
            </thead>
            <tbody>
              <?php
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
                  <td <?php echo (in_array('email', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ghax_obfuscate_email($myemail); ?></td>
                  <td <?php echo (in_array('from_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->form_name) ? esc_html($result->form_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('purchased_count', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($result->buylead . '/' . $result->new_lead_quantity); ?></td>
                  <td <?php echo (in_array('category', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->cat_name) ? esc_html($result->cat_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('group', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->group_name) ? $result->group_name : 'N/A'; ?></td>
                  <td <?php echo (in_array('status', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->status) ? esc_html($result->status) : 'N/A'; ?></td>

                  <td <?php echo (in_array('country', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($country); ?></td>
                  <td <?php echo (in_array('state', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($state); ?></td>
                  <td <?php echo (in_array('city', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($city); ?></td>
                  <td <?php echo (in_array('zipcode', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($zipcode); ?></td>
                  <td <?php echo (in_array('price', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php
                    if ($price) {
                      if ($result->discount_quantity) {
                        if ($result->lead_discount) {
                          if ($result->buylead >= $result->discount_quantity) {
                            $discount_multi = floor($result->buylead / $result->discount_quantity);
                            echo "<del>" . esc_html(get_option('lead_currency') . $price) . "</del> ";
                            $price = $price - ((($result->lead_discount * $price) / 100) * $discount_multi);
                            if ($price <= 0) {
                              $price = 0;
                            }
                          }
                        }
                      }
                      echo esc_html(get_option('lead_currency') . $price);
                    } else {
                      echo 'N/A';
                    } ?>
                  </td>
                  <td style="display:none">
                    <?php if ($price && $price >= 0) {
                      echo $price;
                    } else {
                      echo 0;
                    } ?>
                  </td>
                  <td <?php echo (in_array('published', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php if ($result->publish == 1) {
                      echo "Yes";
                    } else {
                      echo "No";
                    } ?>
                  </td>
                  <td <?php echo (in_array('created', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?>
                  </td>
                  <?php
                  $user_id = get_current_user_id();
                  $leadcart = get_user_meta($user_id, 'leadcart', true);
                  if ($multiple_lead) {
                    if ($leadcart) {
                      if (in_array($result->id, $leadcart)) { ?>
                        <td><a class="added buyleadbtn" href="javascript:void(0)">Added</a> <a class="remove_cart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Remove</a></td>
                      <?php
                      } else { ?>
                        <td><a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Add to Cart</a> </td>
                      <?php
                      } ?>
                    <?php } else { ?>
                      <td><a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Add to Cart</a> </td>
                    <?php
                    }
                  } else { ?>
                    <td><a class="directbuy buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Buy Lead</a></td>
                  <?php } ?>

                </tr>
              <?php
              }
              ?>
            <tbody>
          </table>
        </div>
      <?php
      } else {
        echo '<p>No leads found.</p>';
      }
    }
    return ob_get_clean();
  }

  function display_group_leads_list($atts)
  {
    ob_start();
    global $wpdb;

    $buy_lead_page = get_option('buy_lead_page');
    $user = wp_get_current_user();
    if (!in_array('ghaxlt_buyer', (array) $user->roles) && !in_array('administrator', (array) $user->roles) && !in_array('ghaxlt_annual_buyer', (array) $user->roles) && !in_array('ghaxlt_monthly_buyer', (array) $user->roles)) {

      echo '<p>You are not allowed to view this content.</p>';
    } else {

      $q1 = "SELECT * FROM {$wpdb->prefix}ghaxlt_lead_groups WHERE id=" . $atts['group'];
      $r1 = $wpdb->get_row($q1);

      $price_result = $wpdb->get_results("select MIN(COALESCE(grps.price,0)+COALESCE(qlty.price,0)) as min_price,MAX(COALESCE(grps.price,0)+COALESCE(qlty.price,0)) as max_price from {$wpdb->prefix}ghaxlt_leads as gaxlead  left join  {$this->tblgrps} as grps on gaxlead.group=grps.id left join  {$this->tblqlty} as qlty on gaxlead.quality=qlty.id WHERE gaxlead.publish=1 AND gaxlead.group='" . $atts['group'] . "'");

      $results = $wpdb->get_results("select gaxlead.*,coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),1) as new_lead_quantity,cat.name as cat_name,grps.name as group_name,qlty.name as quality_name,COALESCE(grps.price,0)+COALESCE(qlty.price,0) as totalprice,(SELECT count(*) as count FROM {$this->tblpymts} WHERE lead_id=gaxlead.id  and transaction_type='{$this->payment_mode}' order by id desc) as buylead from {$wpdb->prefix}ghaxlt_leads as gaxlead left join  {$this->tblcats} as cat on gaxlead.category=cat.id left join  {$this->tblgrps} as grps on gaxlead.group=grps.id left join  {$this->tblqlty} as qlty on gaxlead.quality=qlty.id WHERE gaxlead.publish=1 AND gaxlead.group='" . $atts['group'] . "' having coalesce(nullif(rtrim(ltrim(gaxlead.lead_quantity)),''),0)>=buylead");

      //print_r($results);
      //$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ghaxlt_leads WHERE `publish`=1  order by id desc");

      if (count($results) > 0) {
      ?>

        <div class="leadssrt">
          <h2>Displaying Leads by Group - <?php echo esc_html(ucfirst($r1->name)); ?></h2>
          <div class="filter-holder">
            <div class="filter-left-wrap">
              <div class="row">
                <?php

                $countries = file_get_contents(GHAX_LEADTRAIL_ABSPATH . '/includes/classes/json/countries.json');
                $array_countries = json_decode($countries, true);
                $option = "<option value=''>Please select the country</option>";
                foreach ($array_countries as $array_country) {
                  $option .= "<option value='" . esc_attr(esc_attr($array_country['iso2'])) . "'>" . esc_html($array_country['name']) . "</option>";
                }  ?>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>Country:</label>
                    <select name="country" id="country" data-column="7" onchange="leadsearch_attribute()" class="custom-input"><?php echo $option; ?></select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder select-state-text">
                    <label>State:</label>
                    <input type="text" name="state" id="state" data-column="8" onkeyup="leadsearch_attribute()" placeholder="State" class="custom-input">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>City:</label>
                    <input type="text" name="city" id="city" data-column="9" onkeyup="leadsearch_attribute()" placeholder="City" class="custom-input">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-holder">
                    <label>Enter Zip</label>
                    <input type="text" name="zipcode" id="zipcode" data-column="10" onkeyup="leadsearch_attribute()" class="custom-input" placeholder="zip">
                  </div>
                </div>

                <?php
                if (count($price_result) > 0) { ?>
                  <div class="col-md-4 label-wrap">
                    <div class="form-holder">
                      <div class="label">
                        <label>Price:</label>
                      </div>
                      <div slider id="slider-distance">
                        <div>
                          <div inverse-left style="width:70%;"></div>
                          <div inverse-right style="width:70%;"></div>
                          <div range style="left:0%;right:0%;"></div>
                          <span thumb style="left:0%;"></span>
                          <span thumb style="left:100%;"></span>
                          <div sign style="left:0%;">
                            <span id="value"><?php echo 0; ?></span>
                          </div>
                          <div sign style="left:100%;">
                            <span id="value"><?php echo $price_result[0]->max_price; ?></span>
                          </div>
                        </div>
                        <input type="range" tabindex="0" onchange="leadsearch_attribute()" id="attribute_min_price" value="0" max="<?php echo (int) $price_result[0]->max_price; ?>" min="0" step="1" oninput="
                                      this.value=Math.min(this.value,this.parentNode.childNodes[5].value-1);
                                      var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                      var children = this.parentNode.childNodes[1].childNodes;
                                      children[1].style.width=value+'%';
                                      children[5].style.left=value+'%';
                                      children[7].style.left=value+'%';children[11].style.left=value+'%';
                                      children[11].childNodes[1].innerHTML=this.value;" />

                        <input type="range" tabindex="0" onchange="leadsearch_attribute()" id="attribute_max_price" value="<?php echo (int) $price_result[0]->max_price; ?>" max="<?php echo $price_result[0]->max_price; ?>" min="0" step="1" oninput="
                                      this.value=Math.max(this.value,this.parentNode.childNodes[3].value-(-1));
                                      var value=(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.value)-(100/(parseInt(this.max)-parseInt(this.min)))*parseInt(this.min);
                                      var children = this.parentNode.childNodes[1].childNodes;
                                      children[3].style.width=(100-value)+'%';
                                      children[5].style.right=(100-value)+'%';
                                      children[9].style.left=value+'%';children[13].style.left=value+'%';
                                      children[13].childNodes[1].innerHTML=this.value;" />
                      </div>
                    </div>
                  </div>
                <?php } ?>
                <?php $multiple_lead = get_option('multiple_lead_show', false);
                if ($multiple_lead) { ?>
                  <div class="col-md-4">
                    <div class="form-holder">
                      <div class="cart-btn-top">
                        <a class="buyleadbtn" href="<?php echo get_permalink($buy_lead_page); ?>">View Cart</a>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>

          <?php $lead_field_display = get_option('group_lead_field_display');
          if ($lead_field_display) {
          } else {
            $lead_field_display = array();
          }  ?>
          <?php
          $style = 'style="display:none"'; ?>
          <table id="leadstbl" style="width:100%;" class="table table-bordered">
            <thead>
              <th <?php echo (in_array('email', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Email</th>
              <th <?php echo (in_array('from_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Form Name</th>
              <th <?php echo (in_array('purchased_count', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Purchase Count</th>
              <th <?php echo (in_array('category', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Category</th>
              <th <?php echo (in_array('status', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Status</th>
              <th <?php echo (in_array('quality', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Quality</th>
              <th <?php echo (in_array('country', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Country</th>
              <th <?php echo (in_array('state', $lead_field_display)) ? '' : 'style="display:none"'; ?>>State</th>
              <th <?php echo (in_array('city', $lead_field_display)) ? '' : 'style="display:none"'; ?>>City</th>
              <th <?php echo (in_array('zipcode', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Zipcode</th>
              <th <?php echo (in_array('price', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Price</th>
              <th style="display:none"></th>
              <th <?php echo (in_array('published', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Published</th>
              <th <?php echo (in_array('created', $lead_field_display)) ? '' : 'style="display:none"'; ?>>Created On</th>
              <th>Purchase</th>
            </thead>
            <tbody>
              <?php
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
                  <td <?php echo (in_array('email', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ghax_obfuscate_email($myemail); ?></td>
                  <td <?php echo (in_array('from_name', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->form_name) ? esc_html($result->form_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('purchased_count', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($result->buylead . '/' . $result->new_lead_quantity); ?></td>
                  <td <?php echo (in_array('category', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->cat_name) ? esc_html($result->cat_name) : 'N/A'; ?></td>

                  <td <?php echo (in_array('status', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->status) ? esc_html($result->status) : 'N/A'; ?></td>
                  <td <?php echo (in_array('quality', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo ($result->quality_name) ? esc_html($result->quality_name) : 'N/A'; ?></td>
                  <td <?php echo (in_array('country', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($country); ?></td>
                  <td <?php echo (in_array('state', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($state); ?></td>
                  <td <?php echo (in_array('city', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($city); ?></td>
                  <td <?php echo (in_array('zipcode', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo esc_html($zipcode); ?></td>
                  <td <?php echo (in_array('price', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php
                    if ($price) {
                      if ($result->discount_quantity) {
                        if ($result->lead_discount) {
                          if ($result->buylead >= $result->discount_quantity) {
                            $discount_multi = floor($result->buylead / $result->discount_quantity);
                            echo "<del>" . esc_html(get_option('lead_currency') . $price) . "</del> ";
                            $price = $price - ((($result->lead_discount * $price) / 100) * $discount_multi);
                            if ($price <= 0) {
                              $price = 0;
                            }
                          }
                        }
                      }
                      echo esc_html(get_option('lead_currency') . $price);
                    } else {
                      echo 'N/A';
                    } ?>
                  </td>
                  <td style="display:none">
                    <?php if ($price && $price >= 0) {
                      echo $price;
                    } else {
                      echo 0;
                    } ?>
                  </td>
                  <td <?php echo (in_array('published', $lead_field_display)) ? '' : 'style="display:none"'; ?>>
                    <?php if ($result->publish == 1) {
                      echo "Yes";
                    } else {
                      echo "No";
                    } ?>
                  </td>
                  <td <?php echo (in_array('created', $lead_field_display)) ? '' : 'style="display:none"'; ?>><?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?></td>
                  <?php
                  $user_id = get_current_user_id();
                  $leadcart = get_user_meta($user_id, 'leadcart', true);
                  if ($multiple_lead) {
                    if ($leadcart) {
                      if (in_array($result->id, $leadcart)) { ?>
                        <td><a class="added buyleadbtn" href="javascript:void(0)">Added</a> <a class="remove_cart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Remove</a></td>
                      <?php
                      } else { ?>
                        <td><a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Add to Cart</a> </td>
                      <?php
                      } ?>
                    <?php } else { ?>
                      <td><a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Add to Cart</a> </td>
                    <?php
                    }
                  } else { ?>
                    <td><a class="directbuy buyleadbtn" href="javascript:void(0)" data-id="<?php echo esc_attr($result->id); ?>">Buy Lead</a></td>
                  <?php } ?>

                </tr>
              <?php
              }
              ?>
            <tbody>
          </table>
        </div>
        <?php
      } else {
        echo '<p>No leads found.</p>';
      }
    }
    return ob_get_clean();
  }

  /**
   * Send HTTP POST Request
   *
   * @param	string	The API method name
   * @param	string	The POST Message fields in &name=value pair format
   * @return	array	Parsed HTTP Response body
   */
  function PPHttpPost($methodName_, $nvpStr_)
  {
    global $environment;
    global $wpdb;
    $curarr = array(
      '$' => 'USD',
      '£' => 'GBP',
      '€' => 'EUR',
      'CAD $' => 'CAD'
    );
    $lead_currency = get_option('lead_currency');
    $curr = $curarr[$lead_currency];
    if ($curr) {
    } else {
      $curr = "usd";
    }
    $environment = get_option('paypal_mode');
    // Set up your API credentials, PayPal end point, and API version.
    $API_UserName = urlencode(get_option('paypal_api_username'));
    //$API_UserName = urlencode('mail-facilitator_api1.unraveledmedia.com');
    $API_Password = urlencode(get_option('paypal_api_password'));
    //$API_Password = urlencode('6XFK5RN458TDJWDU');
    $API_Signature = urlencode(get_option('paypal_api_signature'));
    //$API_Signature = urlencode('AEnZGlfD4J9rfjld3msdCvUIRsLLAiXLSuC7Qn4DGpeVmAuwfpe22HCx'); 
    $API_Endpoint = "https://api-3t.paypal.com/nvp";
    if ("sandbox" === $environment || "beta-sandbox" === $environment) {
      $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
    }
    $version = urlencode('51.0');
    // Set the API operation, version, and API signature in the request.
    ///$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

    $nvpreq = array(
      'METHOD' => $methodName_,
      'VERSION' => $version,
      'PWD' => $API_Password,
      'USER' => $API_UserName,
      'SIGNATURE' => $API_Signature . $nvpStr_
    );

    $req = wp_remote_post($API_Endpoint, array(
      'body' => $nvpreq,
      'timeout' => 60,
      'sslverify' => false
    ));

    if (is_wp_error($req)) {
      $err = $req->get_error_message();

      exit("$methodName_ failed: " . $err);
    }

    $httpResponse = wp_remote_retrieve_body($req);

    // Extract the response details.
    $httpResponseAr = explode("&", $httpResponse);

    $httpParsedResponseAr = array();
    foreach ($httpResponseAr as $i => $value) {
      $tmpAr = explode("=", $value);
      if (sizeof($tmpAr) > 1) {
        $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
      }
    }

    if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
      exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
    }

    return $httpParsedResponseAr;
  }

  function buy_GHAXlt_lead()
  {
    ob_start();
    global $wpdb;

    $sold = array();
    $display_data = "";

    $user_id = get_current_user_id();
    $leadcart = get_user_meta($user_id, 'leadcart', true);
    $price = array();
    $lead_email = array();
    if ($leadcart) {
      foreach ($leadcart as $key => $value) {
        $qry = "SELECT* FROM " . $wpdb->prefix . "ghaxlt_leads WHERE id=" . $value;
        $row = $wpdb->get_row($qry);

        if ($row) {
          $data = $row->data;
          $person = json_decode($data);
          $lead_group = $row->group;
          $lead_quality = $row->quality;
          $lead_category = $row->category;
          $admin_note = $row->admin_note;
          $buybol = true;
          $buyresults = $wpdb->get_results("SELECT count(*) as count FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE `lead_id`='$value'");
          $lprice = 0;
          if ($lead_group) {
            $qry1 = "SELECT* FROM " . $wpdb->prefix . "ghaxlt_lead_groups WHERE id=" . $lead_group;
            $row1 = $wpdb->get_row($qry1);
            if (isset($row1->price) && $row1->price) {
              $lprice += $row1->price;
            }
          }
          if ($lead_quality) {
            $qryl = "SELECT* FROM " . $wpdb->prefix . "ghaxlt_lead_qualities WHERE id=" . $lead_quality;
            $rowl = $wpdb->get_row($qryl);
            if ($rowl->price) {
              $lprice += $rowl->price;
            }
          }
          if ($lead_category) {
            $qryl = "SELECT* FROM " . $wpdb->prefix . "ghaxlt_lead_cats WHERE id=" . $lead_category;
            $rowl = $wpdb->get_row($qryl);
            if ($rowl->price) {
              $lprice += $rowl->price;
            }
          }
          if ($lprice) {
            if ($row->discount_quantity) {
              if ($row->lead_discount) {
                if ($buyresults[0]->count >= $row->discount_quantity) {
                  $lprice = $lprice - (($row->lead_discount * $lprice) / 100);
                }
              }
            }
          } else {
            $lprice = 0;
          }
          $price[] = $lprice;
          $myemail = "";
          foreach ($person as $key1 => $value1) {
            if (filter_var($value1, FILTER_VALIDATE_EMAIL)) {
              $myemail = $value1;
            }
          }

          if ($row->lead_quantity) {
            if ($row->lead_quantity <= $buyresults[0]->count) {
              $buybol = false;
            }
          }
          if (!$buybol) {

            $sold[] = $myemail;
            unset($leadcart[$key]);
          } else {
            $lead_email[] = ghax_obfuscate_email($myemail);
          }
        }
      }
      if ($sold) {
        //echo '<p style="text-align:center;font-weight:bold;color:#f00;">Sorry!! '.implode(',', $sold).' lead is already sold.</p>';
      }
      if (empty($leadcart)) {
        return;
      }
      $lprice = array_sum($price);

      echo '<div class="lead-main-wrap"><div class="top-hdr-info"><p style="font-weight:bold;;">You are requesting access to the following lead:</p>';
      echo "<ul><li>" . implode("</li><li>", $lead_email) . "</li></ul></div>";
      $user = wp_get_current_user();
      $udata = $user->data;
      /*$qry = "SELECT * FROM ".$wpdb->prefix."ghaxlt_leads WHERE id=".$_GET['lead'];
		$data = $wpdb->get_row($qry);*/
      $curarr = array(
        '$' => 'usd',
        '£' => 'gbp',
        '€' => 'eur',
        'CAD $' => 'cad'
      );
      $lead_currency = get_option('lead_currency');
      $curr = $curarr[$lead_currency];
      if ($curr) {
      } else {
        $curr = "usd";
      }
      $paypal_s = false;
      $stripe_s = false;
      if (get_option('paypal_api_username') && get_option('paypal_api_password') && get_option('paypal_api_signature')) {
        $paypal_s = true;
      }
      if (get_option('stripe_publishable_key') && (get_option('stripe_secret_key'))) {
        require_once('stripe-php/init.php');
        $stripe = new \Stripe\StripeClient(
          get_option('stripe_secret_key')
        );
        $stripe_s = true;
      }
      // Set your secret key. Remember to switch to your live secret key in production.
      // See your keys here: https://dashboard.stripe.com/apikeys

      // Token is created using Stripe Checkout or Elements!
      // Get the payment token ID submitted by the form:
      if (isset($_POST['stripeToken'])) {
        $token = sanitize_text_field($_POST['stripeToken']);
        $display_data = "display:none";
        $stripe_first_name = sanitize_text_field($_POST['stripe_first_name']);
        $stripe_last_name = sanitize_text_field($_POST['stripe_last_name']);

        $stripe_email = sanitize_email($_POST['stripe_email']);
        $cardId = sanitize_text_field($_POST['cardId']);
        $lead_price = number_format((sanitize_text_field($_POST['lead_price']) * 100), 0, '', '');
        $currency = 'usd';

        $customer = $stripe->customers->create([
          'name'    => $stripe_first_name . ' ' . $stripe_last_name,
          'email'   => $stripe_email,
          // 'source' => $cardId,
          'address' => [
            'line1' => '510 Townsend St',
            'postal_code' => '98140',
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'US',
          ],
        ]);

        $charge = $stripe->paymentIntents->create(
          [
            'amount' => $lead_price,
            'currency' => $curr,
            'description' => 'Lead is purchased by ' . $stripe_first_name,
            'payment_method_types' => ['card'],
            'customer' => $customer->id,
          ]
        );

        $result = $stripe->paymentIntents->confirm($charge->id, ['payment_method' => 'pm_card_visa']);

        // if ($charge->status == 'succeeded') {
        if ($result) {
          echo '<p class="psuccess">Payment has processed successfully. Redirecting you to your dashboard...<p>';
          $j = 0;
          foreach ($leadcart as $key => $value) {
            $wpdb->insert(
              $wpdb->prefix . "ghaxlt_leads_payments",
              array(
                'user_id' => get_current_user_id(),
                'lead_id' => $value,
                'payment_by' => 'stripe',
                'amount' => $price[$j],
                'payment_id' => $charge['id'],
                'transaction_type' => $this->payment_mode,
              )
            );
            $wpdb->update($wpdb->prefix . "ghaxlt_leads", array('status' => 'sold'), array('id' => $value));
            $j++;
          }

          update_user_meta($user_id, 'leadcart', "");

          $red = get_permalink(get_option('_leadbuyerdashboard_page'));
          $author_obj = get_user_by('id', $user_id);

          $to = $author_obj->user_email;

          $subject = 'You purchased a new lead';

          $headers = "From: " . strip_tags(get_option('admin_email')) . "\r\n";
          $headers .= "Reply-To: " . strip_tags(get_option('admin_email')) . "\r\n";
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
          $message = '<html><body>';
          $message .= '<div class="emailcontainer" style="border:2px solid #74499E;">';
          $message .= '<div class="emailcontent" style="background:#fff;padding:2%;">';
          $message .= '<p>You purchased ' . $j . ' leads for ' . get_option('lead_currency') . '' . sanitize_text_field($_POST['lead_price']) . '. You can view your lead details on the <a href="' . $red . '">buyer dashboard</a> </p>';
          $message .= '</div>';
          $message .= '</div>';
          $message .= '</body></html>';

          wp_mail($to, $subject, $message, $headers);

          $to1 = get_option('admin_email');
          $subject1 = 'You sold a lead';
          $headers1 = "From: " . strip_tags($author_obj->user_email) . "\r\n";
          $headers1 .= "Reply-To: " . strip_tags($author_obj->user_email) . "\r\n";
          $headers1 .= "MIME-Version: 1.0\r\n";
          $headers1 .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
          $message1 = '<html><body>';
          $message1 .= '<div class="emailcontainer" style="border:2px solid #74499E;">';
          $message1 .= '<div class="emailcontent" style="background:#fff;padding:2%;">';
          $message1 .= '<p>' . $author_obj->user_login . ' purchased ' . $j . ' leads for ' . get_option('lead_currency') . '' . sanitize_text_field($_POST['lead_price']) . '. Go to your <a href="' . home_url('/wp-admin') . '">admin dashboard.</a> </p>';
          $message1 .= '</div>';
          $message1 .= '</div>';
          $message1 .= '</body></html>';

          wp_mail($to1, $subject1, $message1, $headers1);
        ?>
          <script>
            setTimeout(function() {
              window.location.href = '<?php echo $red; ?>'; // the redirect goes here

            }, 5000);
          </script>
        <?php

        } else {
          echo '<p class="perror">Payment is pending.</p>';
        }
      }

      /*** paypal code ***/
      /** DoDirectPayment NVP example; last modified 08MAY23.
       *
       *  Process a credit card payment. 
       */

      // or 'beta-sandbox' or 'live'

      if (isset($_POST['paypalpayment'])) {
        // Set request-specific fields.
        $display_data = "display:none";
        //$paymentType = urlencode($_POST['Authorization']);				// or 'Sale'
        $paymentType =  'Sale';
        $firstName = urlencode(sanitize_text_field($_POST['firstname']));
        $lastName = urlencode(sanitize_text_field($_POST['lastname']));
        $creditCardType = urlencode(sanitize_text_field($_POST['cardtype']));
        $creditCardNumber = urlencode(sanitize_text_field($_POST['cardnumber']));
        $expDateMonth = sanitize_text_field($_POST['cardmonth']);
        // Month must be padded with leading zero
        $padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));

        $expDateYear = urlencode(sanitize_text_field($_POST['cardyear']));
        $cvv2Number = urlencode(sanitize_text_field($_POST['cardcvv']));
        /*$address1 = urlencode($_POST['address']);
        $address2 = '';
        $city = urlencode($_POST['city']);
        $state = urlencode($_POST['state']);
        $zip = urlencode($_POST['zip']);
        $country = 'US';*/  // US or other valid country code
        $amount = sanitize_text_field($_POST['lead_price']);  //actual amount should be substituted here
        $currencyID = strtoupper($curr); // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

        // Add request-specific fields to the request string.
        /*$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
				"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
				"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";*/

        $nvpStr =  "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber" .
          "&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName" .
          "&CURRENCYCODE=$currencyID";

        // Execute the API operation; see the PPHttpPost function above.
        $httpParsedResponseAr = $this->PPHttpPost('DoDirectPayment', $nvpStr);


        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
          //exit('Direct Payment Completed Successfully: '.print_r($httpParsedResponseAr, true));

          echo '<p class="psuccess">Payment has processed successfully. Redirecting you to your dashboard...<p>';
          $j = 0;
          $environment = get_option('paypal_mode');
          foreach ($leadcart as $key => $value) {
            $wpdb->insert(
              $wpdb->prefix . "ghaxlt_leads_payments",
              array(
                'user_id' => get_current_user_id(),
                'lead_id' => $value,
                'payment_by' => 'paypal',
                'amount' => $price[$j],
                'payment_id' => $httpParsedResponseAr['TRANSACTIONID'],
                'transaction_type' => $this->payment_mode,
              )
            );
            $wpdb->update($wpdb->prefix . "ghaxlt_leads", array('status' => 'sold'), array('id' => $value));
            $j++;
          }
          update_user_meta($user_id, 'leadcart', "");

          $red = get_permalink(get_option('_leadbuyerdashboard_page'));
          $author_obj = get_user_by('id', $user_id);
          $to = $author_obj->user_email;
          $subject = 'You purchased a new lead';
          $headers = "From: " . strip_tags(get_option('admin_email')) . "\r\n";
          $headers .= "Reply-To: " . strip_tags(get_option('admin_email')) . "\r\n";
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
          $message = '<html><body>';
          $message .= '<div class="emailcontainer" style="border:2px solid #74499E;">';
          $message .= '<div class="emailcontent" style="background:#fff;padding:2%;">';
          $message .= '<p>You purchased ' . $j . ' leads for ' . get_option('lead_currency') . '' . $amount . '. You can view your lead details on the <a href="' . $red . '">buyer dashboard</a> </p>';
          $message .= '</div>';
          $message .= '</div>';
          $message .= '</body></html>';

          wp_mail($to, $subject, $message, $headers);

          $to1 = get_option('admin_email');
          $subject1 = 'You sold a lead';
          $headers1 = "From: " . strip_tags($author_obj->user_email) . "\r\n";
          $headers1 .= "Reply-To: " . strip_tags($author_obj->user_email) . "\r\n";
          $headers1 .= "MIME-Version: 1.0\r\n";
          $headers1 .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
          $message1 = '<html><body>';
          $message1 .= '<div class="emailcontainer" style="border:2px solid #74499E;">';
          $message1 .= '<div class="emailcontent" style="background:#fff;padding:2%;">';
          $message1 .= '<p>' . $author_obj->user_login . ' purchased ' . $j . ' leads for ' . get_option('lead_currency') . '' . $amount . '. Go to your <a href="' . home_url('/wp-admin') . '">admin dashboard</a>. </p>';
          $message1 .= '</div>';
          $message1 .= '</div>';
          $message1 .= '</body></html>';

          wp_mail($to1, $subject1, $message1, $headers1);
        ?>
          <script>
            setTimeout(function() {
              window.location.href = '<?php echo $red; ?>'; // the redirect goes here

            }, 5000);
          </script>
      <?php
        } else {
          echo '<p class="perror">Somthing went wrong. Please try after sometime.<p>';
          exit();
        }
      }

      ?>
      <!-- <div class="price">Price :<br>
        <span>
          <?php if ($lprice) {

            echo get_option('lead_currency') . $lprice;
          } else {

            echo get_option('lead_currency') . '0.00';
          } ?>
        </span>
      </div> -->

      <div class="payment_options_container" style="<?php echo $display_data; ?>">
        <?php if (($paypal_s) && ($stripe_s)) { ?>
          <h4 class="paymentttl">Select a payment option:</h4>
        <?php } ?>

        <?php
        if ($paypal_s) { ?>
          <a class="paymentoptt" href="javascript:void(0);" id="paypalopt">Paypal</a>
        <?php
        }
        if ($stripe_s) { ?>
          <a class="paymentoptt" href="javascript:void(0);" id="stripeout">Stripe</a>
        <?php } ?>
      </div>

      <div class="payment_options_form">
        <?php
        if ($paypal_s) { ?>
          <div class="ghax_paypal_form" <?php if (($paypal_s) && ($stripe_s)) { ?> style="display:none" <?php } ?>>

            <form name="payform" action="" method="post">
              <input type="hidden" name="lead_price" value="<?php if ($lprice) {
                                                              echo $lprice;
                                                            } else {
                                                              echo '10.00';
                                                            } ?>">
              <div class="maindiv">
                <h5>Payment Details</h5>
                <div class="form-group">
                  <label>First Name</label>
                  <input name="firstname" type="text" value="" placeholder="Enter First Name" required>
                </div>
                <div class="form-group">
                  <label>Last Name</label>
                  <input name="lastname" type="text" value="" placeholder="Enter Last Name" required>
                </div>
                <div class="form-group">
                  <label>Email Address</label>
                  <input name="email" type="text" value="" placeholder="Please enter Email" required>
                </div>
                <h5>Credit card information</h5>
                <div class="form-group">
                  <label>Card Type</label>
                  <select name="cardtype">
                    <option value="visa" selected="selected">Visa</option>
                    <option value="MasterCard">Master Card</option>
                    <option value="AmericanExpress">American Express</option>
                  </select>
                </div>
                <div class="form-group" required>
                  <label>Card Number</label>
                  <input name="cardnumber" type="text" value="" placeholder="Enter Your Card Number">
                </div>
                <div class="form-group">
                  <label>Expiry Date</label>
                  <input name="cardmonth" class="sel" type="text" style="width:40px;" value="" required placeholder="mm" />&nbsp;
                  <input type="text" name="cardyear" class="sel" style=" width:98px;" value="" required placeholder="yyyy">
                </div>
                <div class="form-group">
                  <label>CVV</label>
                  <input name="cardcvv" type="text" value="" placeholder="***" required>
                </div>

                <input type="submit" class="paypalbtn" value="Submit Payment" name="paypalpayment">

                <div class="clear"></div>
              </div>
            </form>

          </div>
        <?php }
        if (get_option('stripe_publishable_key') && (get_option('stripe_secret_key'))) { ?>
          <div class="ghax_stripe_form" <?php if (($paypal_s) && ($stripe_s)) { ?> style="display:none" <?php } ?>>

            <form action="" method="post" id="payment-form">
              <div class="form-row">
                <h5>Personal Details</h5>
                <div class="form-group">
                  <label>First Name</label>
                  <input type="text" name="stripe_first_name" value="" id="stripe_first_name" placeholder="Enter First Name" required>
                </div>
                <div class="form-group">
                  <label>Last Name</label>
                  <input type="text" name="stripe_last_name" value="" id="stripe_last_name" placeholder="Enter Last Name" required>
                </div>
                <div class="form-group">
                  <label>Email Address</label>
                  <input type="email" name="stripe_email" value="<?php echo $udata->user_email; ?>" id="stripe_email" placeholder="Enter Email Address" required>
                </div>
                <h5>Credit card information</h5>
                <div id="card-element">
                  <!-- A Stripe Element will be inserted here. -->
                </div>

                <!-- Used to display Element errors. -->
                <div id="card-errors" role="alert"></div>
              </div>

              <input type="hidden" name="lead_price" value="<?php if ($lprice) {
                                                              echo $lprice;
                                                            } else {
                                                              echo '10.00';
                                                            } ?>">
              <button class="stripebtn">Submit Payment</button>
            </form>
          </div>
        <?php } ?>
      </div>
      </div>
      <script src="https://js.stripe.com/v3/"></script>
      <script>
        var stripe = Stripe('<?php echo get_option('stripe_publishable_key'); ?>');

        var elements = stripe.elements();
        // Custom styling can be passed to options when creating an Element.
        var style = {
          base: {
            color: '#000',
            backgroundColor: '#e5e5e5',
            lineHeight: '45px',
            padding: '10px',
            iconColor: '#734A9D',
            fontSize: '16px',
            fontFamily: '"Open Sans", sans-serif',
            fontSmoothing: 'antialiased',
            '::placeholder': {
              color: '#000',
            },
          },
          invalid: {
            color: '#e5424d',
            ':focus': {
              color: '#303238',
            },
          },
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {
          style: style
        });

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Create a token or display an error when the form is submitted.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
          event.preventDefault();

          stripe.createToken(card).then(function(result) {
            if (result.error) {
              // Inform the customer that there was an error.
              var errorElement = document.getElementById('card-errors');
              errorElement.textContent = result.error.message;
            } else {
              // Send the token to your server.
              // alert("Result::::", result);
              stripeTokenHandler(result.token);
            }
          });
        });

        function stripeTokenHandler(token) {
          // Insert the token ID into the form so it gets submitted to the server
          var form = document.getElementById('payment-form');
          var hiddenInput = document.createElement('input');
          hiddenInput.setAttribute('type', 'hidden');
          hiddenInput.setAttribute('name', 'stripeToken');
          hiddenInput.setAttribute('value', token.id);
          var cardInput = document.createElement('input');
          cardInput.setAttribute('type', 'hidden');
          cardInput.setAttribute('name', 'cardId');
          cardInput.setAttribute('value', token.card.id);
          // var cvvInput = document.createElement('input');
          // cvvInput.setAttribute('type', 'hidden');
          // cvvInput.setAttribute('name', 'stripeToken');
          // cvvInput.setAttribute('value', token.id);
          // var expmInput = document.createElement('input');
          // expmInput.setAttribute('type', 'hidden');
          // expmInput.setAttribute('name', 'stripeToken');
          // expmInput.setAttribute('value', token.id);
          // var expyInput = document.createElement('input');
          // expyInput.setAttribute('type', 'hidden');
          // expyInput.setAttribute('name', 'stripeToken');
          // expyInput.setAttribute('value', token.id);

          form.appendChild(cardInput);
          form.appendChild(hiddenInput);

          // Submit the form
          form.submit();
        }
      </script>
      <script>
        jQuery(document).ready(function($) {
          $('#paypalopt').click(function() {
            $('.ghax_paypal_form').show();
            $('.ghax_stripe_form').hide();
          });

          $('#stripeout').click(function() {
            $('.ghax_paypal_form').hide();
            $('.ghax_stripe_form').show();
          });
        });
      </script>
    <?php
    } else {
      echo "No Lead is added into cart";
    }
    return ob_get_clean();
  }

  function display_buyer_dashboard()
  {
    ob_start();
    global $wpdb;

    $user = wp_get_current_user();
    $current_user_id = get_current_user_id();
    // print_r($current_user_id);
    // die;
    // $wpdb->query("alter table {$wpdb->prefix}ghaxlt_leads alter column lead_quantity set DEFAULT 1");

    if (!in_array('ghaxlt_buyer', (array) $user->roles) && !in_array('administrator', (array) $user->roles) && !in_array('ghaxlt_annual_buyer', (array) $user->roles) && !in_array('ghaxlt_monthly_buyer', (array) $user->roles)) {

      echo '<p>You do not have access for this page.</p>';
      exit();
    } else {

      $receive_lead_notifications = get_user_meta($current_user_id, 'receive_lead_notifications', true);
      if (isset($_POST['save_notify_settings'])) {
        $receive_lead_notifications = sanitize_text_field($_POST['receive_lead_notifications']);
        update_user_meta($current_user_id, 'receive_lead_notifications', $receive_lead_notifications);
      }

    ?>

      <div class="leadssrt">
        <ul class="nav nav-tabs">
          <li class="active nav-item"><a class="nav-link active" data-bs-toggle="tab" data-bs-target="#purchased_leads" href="#purchased_leads">My Assigned Leads</a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" data-bs-target="#leads_notifications" href="#leads_notifications">Notifications</a></li>
          <!--<li><a data-toggle="tab" href="#menu2">Menu 2</a></li>-->
        </ul>

        <div class="tab-content">
          <div id="purchased_leads" class="tab-pane fade show active">
            <div class="leads_container">


              <table id="buyer-leadstbl" class="display mdl-data-table">
                <thead>
                  <tr>
                    <!-- <th>Email</th>
                    <th>Category</th>
                    <th>Group</th>
                    <th>Quality</th>
                    <th>Details</th> -->
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Category</th>
                    <th>Created On</th>
                    <th>Details</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  $qry1 = "SELECT * FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE user_id=" . $current_user_id;
                  $results1 = $wpdb->get_results($qry1);
                  if (count($results1) > 0) {
                    $leadsarr = array();
                    foreach ($results1 as $result1) {
                      $leadsarr[] = $result1->lead_id;
                    }
                    $leadsstr = implode(',', $leadsarr);
                    $qry2 =  "SELECT * FROM {$wpdb->prefix}ghaxlt_leads WHERE status = 'sold' AND id IN (" . $leadsstr . ")";
                    $results = $wpdb->get_results($qry2);
                    if (count($results) > 0) {
                      foreach ($results as $result) {
                        $myarr = json_decode($result->data, true);
                        $myemail = '';
                        foreach ($myarr as $key => $value) {
                          if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $myemail = $value;
                          }
                        }
                        $myname = '';
                        foreach ($myarr as $key => $value) {
                          if ($key == 'Name') {
                            $myname = $value;
                          }
                        }
                        $myPhoneNumber = '';
                        foreach ($myarr as $key => $value) {
                          if ($key == 'Phone Number') {
                            $myPhoneNumber = $value;
                          }
                        }
                        $ccat = 'N/A';
                        if ($result->category) {
                          $cqry = "SELECT * FROM {$wpdb->prefix}ghaxlt_lead_cats WHERE id=" . $result->category;
                          $cres = $wpdb->get_row($cqry);
                          if ($cres) {
                            $ccat = $cres->name;
                          }
                        }

                        // $ggrp = 'N/A';
                        // if ($result->group) {
                        //   $gqry = "SELECT * FROM {$wpdb->prefix}ghaxlt_lead_groups WHERE id=" . $result->group;
                        //   $gres = $wpdb->get_row($gqry);
                        //   if ($gres) {
                        //     $ggrp = $gres->name;
                        //   }
                        // }

                  ?>
                        <tr>
                          <td><?php echo $myname; ?></td>
                          <td><?php echo $myPhoneNumber; ?></td>
                          <!--<td><?php //echo $result->tag; 
                                  ?></td>-->
                          <td><?php echo $ccat; ?></td>
                          <!--<td><?php //echo $result->quality; 
                                  ?></td>-->
                          <td><?php echo date('m-d-Y h:i:s A', strtotime($result->created_date)); ?></td>
                          <td><a href="<?php echo get_permalink(get_option('_leaddetail_page')); ?>?lead=<?php echo esc_attr($result->id); ?>" class="detailleadbtn">View Details</a></td>

                        </tr>
                      <?php
                      }
                    } else {
                      ?>
                      <tr>
                        <td colspan=5>No Leads Found</td>
                      </tr>
                    <?php
                    }
                  } else {
                    ?>
                    <tr>
                      <td colspan=5>No Leads Found</td>
                    </tr>
                  <?php
                  }
                  ?>

                </tbody>
                <tfoot>
                  <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Category</th>
                    <th>Created On</th>
                    <th>Details</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <script>
              jQuery(document).ready(function($) {
                $('#buyer-leadstbl').DataTable({
                  columnDefs: [

                    {
                      orderable: false,
                      targets: [3]
                    },
                  ],
                  searchPlaceholder: "Search"
                });
              });
            </script>
          </div>


          <div id="leads_notifications" class="tab-pane fade">
            <form method="post" action="">
              <div class="form-group">
                <h5>Get notified for new leads created</h5>
                <div class="form-control1">
                  <select name="receive_lead_notifications">
                    <option value="Yes" <?php if ($receive_lead_notifications == 'Yes') {
                                          echo 'selected';
                                        } ?>>Yes</option>
                    <option value="No" <?php if ($receive_lead_notifications == 'No') {
                                          echo 'selected';
                                        } ?>>No</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <input type="submit" class="btn btn-primary" name="save_notify_settings" value="Save">
              </div>
            </form>
          </div>
          <!--<div id="menu2" class="tab-pane fade">
			<h3>Menu 2</h3>
			<p>Some content in menu 2.</p>
		  </div>-->
        </div>
      </div>
    <?php
    }
    return ob_get_clean();
  }

  function display_lead_details()
  {
    ob_start();
    global $wpdb;

    $user = wp_get_current_user();
    $current_user_id = get_current_user_id();

    if (!in_array('ghaxlt_buyer', (array) $user->roles) && !in_array('administrator', (array) $user->roles) && !in_array('ghaxlt_annual_buyer', (array) $user->roles) && !in_array('ghaxlt_monthly_buyer', (array) $user->roles)) {
      echo '<p>You do not have access for this page.</p>';
      exit();
    }

    if (!isset($_GET['lead'])) {
      return "Lead ID Missing";
    }

    $id = intval($_GET['lead']);

    $qry1 = sprintf("SELECT * FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE lead_id='%d' and user_id='%d'", $id, $current_user_id);
    $results1 = $wpdb->get_row($qry1);

    if ($results1 === NULL) {
      return '<p>You do not have access to this lead detail.</p>';
    }

    ?>
    <div class="leadssrt">
      <div class="leaddet_container">
        <h2>Lead Details</h2>
        <?php
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ghaxlt_leads WHERE id=$id");
        if ($result) {
          $admin_note = $result->admin_note;
        ?>
          <table>
            <tr>
              <th>Id</th>
              <td><?php echo esc_html($result->id); ?></td>
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
          <div class="admin_note_class">
            <?php echo esc_html($admin_note); ?>
          </div>
          <?php
          $settings = array(
            'teeny' => true,
            'textarea_rows' => 10,
            'tabindex' => 1,
            'media_buttons' => false
          );
          wp_editor(__(get_option('buyer_note', $result->buyer_note)), 'buyer_note', $settings);
          ?>
          <input type="submit" name="submit" value="Save" class="button-back add_buyer_note" data-lead-id="<?php echo esc_attr($id); ?>">

          <h3 class="note-response" style="color: green;"></h3>
        <?php
        }
        ?>
      </div>
    </div>
    <script>
      jQuery(document).ready(function($) {
        jQuery(document).on('click', '.add_buyer_note', function() {
          var id = jQuery(this).attr('data-lead-id');
          var note = tinyMCE.get('buyer_note');
          // console.log("countWord:::", note.length)
          jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              "action": "add_buyer_note_action",
              "id": id,
              "table": "<?php echo "{$wpdb->prefix}ghaxlt_leads"; ?>",
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
    return ob_get_clean();
  }
}
