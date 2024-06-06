<div class="container-fluid section-top1">
  <div class="row logo">
    <div class="col-md-6 logo1"><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/leadtrail-logo.jpeg" /></div>
    <div class="col-md-6 logo2"><img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>includes/img/help.png"><a href="https://leadtrail.io/support" target="_blank">Help</a></div>
  </div>
</div>


<div class="wrap Dashboard_section">
  <?php global $wpdb; ?>

  <?php
  global $wpdb;
  $environment = (strtoupper(get_option('paypal_mode')) == 'LIVE' && strtoupper(get_option('stripe_mode')) == 'LIVE') ? 'live' : 'sandbox';
  $leads_qry = "SELECT * FROM " . $wpdb->prefix . "ghaxlt_leads  ld left join {$wpdb->prefix}ghaxlt_leads_payments lp ON lp.lead_id = ld.id WHERE lp.transaction_type like '%$environment%'";
  $leads_res = $wpdb->get_results($leads_qry);
  $total_leads_count = count($leads_res);

  $open_leads_qry = "SELECT * FROM " . $wpdb->prefix . "ghaxlt_leads ld left join {$wpdb->prefix}ghaxlt_leads_payments lp ON lp.lead_id = ld.id WHERE lp.transaction_type like '%$environment%' and ld.status='open'";
  $open_leads_res = $wpdb->get_results($open_leads_qry);
  $total_open_leads_count = count($open_leads_res);

  $sold_leads_qry = "SELECT * FROM " . $wpdb->prefix . "ghaxlt_leads ld left join {$wpdb->prefix}ghaxlt_leads_payments lp ON lp.lead_id = ld.id WHERE lp.transaction_type like '%$environment%' and ld.status='sold'";
  $sold_leads_res = $wpdb->get_results($sold_leads_qry);
  $total_sold_leads_count = count($sold_leads_res);

  $dead_leads_qry = "SELECT * FROM " . $wpdb->prefix . "ghaxlt_leads ld left join {$wpdb->prefix}ghaxlt_leads_payments lp ON lp.lead_id = ld.id WHERE lp.transaction_type like '%$environment%' and ld.status='dead'";
  $dead_leads_res = $wpdb->get_results($dead_leads_qry);
  $total_dead_leads_count = count($dead_leads_res);

  ?>

  <h1 class="anal-sec">Analytics Dashboard</h1>
  <!-- Page-body start -->
  <div class="page-body dashboard-sec">
    <div class="row">

      <!-- Material statustic card start -->
      <div class="col-xl-6 col-md-12 Dashboard_page">
        <div class="card mat-stat-card">
          <div class="card-block card-block-one">
            <div class="row align-items-center">
              <div class="col-sm-6 p-b-20 p-t-20">
                <div class="row align-items-center text-center">
                  <div class="col-4 p-r-0 border-sec">
                    <img src="<?php echo GHAX_LEADTRAIL_RELPATH; ?>admin/assets/ghax/1.png">
                  </div>
                  <div class="col-8 p-l-0">
                    <p class="text-muted m-b-0 <?php echo esc_attr($environment); ?>">Leads </p>
                    <h5><?php echo esc_html($total_leads_count); ?></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 p-b-20 p-t-20">
                <div class="row align-items-center text-center">
                  <div class="col-4 p-r-0 border-sec">
                    <img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/2.png">
                  </div>
                  <div class="col-8 p-l-0">
                    <p class="text-muted m-b-0">Open</p>
                    <h5><?php echo esc_html($total_open_leads_count); ?></h5>
                  </div>
                </div>
              </div>
            </div>
            <div class="row align-items-center">
              <div class="col-sm-6 p-b-20 p-t-20 ">
                <div class="row align-items-center text-center">
                  <div class="col-4 p-r-0 border-sec">
                    <img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/3.png">
                  </div>
                  <div class="col-8 p-l-0">
                    <p class="text-muted m-b-0">Sold</p>
                    <h5><?php echo esc_html($total_sold_leads_count); ?></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 p-b-20 p-t-20">
                <div class="row align-items-center text-center">
                  <div class="col-4 p-r-0 border-sec">
                    <img src="<?php echo GHAX_LEADTRAIL_RELPATH ?>admin/assets/ghax/4.png">
                  </div>
                  <div class="col-8 p-l-0">
                    <p class="text-muted m-b-0">Dead</p>
                    <h5><?php echo esc_html($total_dead_leads_count); ?></h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
      $aqry1 = "SELECT SUM(amount) TOTAL FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and MONTH(created_date) = " . Date('m') . " AND YEAR(created_date)=" . Date('Y') . " AND DAY(created_date)=" . Date('d');
      $ares1 = $wpdb->get_var($aqry1);

      $aqry2 = "SELECT SUM(amount) TOTAL FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and MONTH(created_date) = " . Date('m') . " AND YEAR(created_date)=" . Date('Y');
      $ares2 = $wpdb->get_var($aqry2);

      $aqry3 = "SELECT SUM(amount) TOTAL FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and YEAR(created_date)=" . Date('Y');
      $ares3 = $wpdb->get_var($aqry3);

      $aqry4 = "SELECT SUM(amount) TOTAL FROM {$wpdb->prefix}ghaxlt_leads_payments where transaction_type like '%$environment%'";
      $ares4 = $wpdb->get_var($aqry4);
      ?>
      <div class="col-xl-6 col-md-12 Dashboard_page2">
        <div class="card mat-stat-card right-sec">
          <div class="card-block">
            <h2 class="est-sec">Estimated Revenue</h2>
            <div class="row align-items-center ">
              <div class="col-3 ">
                <div class=" align-items-center text-center">

                  <p class="text-muted m-b-0">Today</p>
                  <h5><?php echo esc_html(get_option('lead_currency')) . " " . round($ares2, 2); ?></h5>
                </div>
              </div>

              <div class="col-sm-3 ">
                <div class="align-items-center text-center">

                  <p class="text-muted m-b-0">This Month</p>
                  <h5><?php echo esc_html(get_option('lead_currency')) . " " . round($ares2, 2); ?></h5>



                </div>
              </div>
              <div class="col-sm-3">
                <div class=" align-items-center text-center">

                  <p class="text-muted m-b-0">This Year </p>
                  <h5><?php echo esc_html(get_option('lead_currency')) . " " . round($ares3, 2); ?></h5>

                </div>
              </div>

              <div class="col-sm-3">
                <div class=" align-items-center text-center">
                  <p class="text-muted m-b-0">Total</p>
                  <h5><?php echo esc_html(get_option('lead_currency')) . " " . round($ares4, 2); ?></h5>



                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--<div class="col-xl-4 col-md-12">
					<div class="card mat-clr-stat-card text-white green ">
						<div class="card-block">
							<div class="row">
								<div class="col-3 text-center bg-c-green">
									<i class="fas fa-star mat-icon f-24"></i>
								</div>
								<div class="col-9 cst-cont">
									<h5>4000+</h5>
									<p class="m-b-0">Ratings Received</p>
								</div>
							</div>
						</div>
					</div>
					<div class="card mat-clr-stat-card text-white blue">
						<div class="card-block">
							<div class="row">
								<div class="col-3 text-center bg-c-blue">
									<i class="fas fa-trophy mat-icon f-24"></i>
								</div>
								<div class="col-9 cst-cont">
									<h5>17</h5>
									<p class="m-b-0">Achievements</p>
								</div>
							</div>
						</div>
					</div>
				</div>-->
      <!-- Material statustic card end -->
      <!-- order-visitor start -->


      <!-- order-visitor end -->

      <!--  sale analytics start -->



      <div class="col-xl-6 col-md-12">
        <div class="card sale-sec">
          <div class="card-header">
            <h5>Sales This Month</h5>
          </div>
          <div class="card-block">
            <!--<div id="morris-site-visit"></div>-->
            <canvas id="saleChart"></canvas>
          </div>
        </div>
      </div>

      <div class="col-md-12 col-lg-6">
        <div class="card sale-sec">
          <div class="card-header">
            <h5>Sales This Year</h5>
          </div>
          <div class="card-block">
            <canvas id="saleyearChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-md-12">
        <div class="card table-card">
          <div class="card-header">
            <h5>Leads Purchased</h5>
            <div class="card-header-right">
              <i class="fa fa-cog" aria-hidden="true"></i>
            </div>
          </div>
          <div class="card-block">
            <div class="table-responsive">
              <table class="table table-hover m-b-0 without-header <?php echo esc_attr($environment); ?>">
                <tbody>
                  <?php
                  $purchase_qry = "SELECT * FROM " . $wpdb->prefix . "ghaxlt_leads_payments where transaction_type LIKE '%$environment%' ORDER BY id desc LIMIT 5 ";
                  $purchase_results = $wpdb->get_results($purchase_qry);
                  if ($purchase_results) {
                    foreach ($purchase_results as $purchase_result) {
                      $user_info = get_userdata($purchase_result->user_id);

                      //$user_info = get_userdata(5);
                      if ($user_info) {
                  ?>
                        <tr>
                          <td>
                            <div class="d-inline-block align-middle">
                              <i class="far fa-user text-c-purple f-24 img-radius img-40 align-top m-r-15"></i>
                              <!--<img src="<?php //echo GHAX_LEADTRAIL_RELPATH; 
                                            ?>/admin/assets/images/avatar-4.jpg" alt="user image" class="img-radius img-40 align-top m-r-15">-->
                              <div class="d-inline-block">
                                <h6><?php echo esc_html($user_info->display_name); ?></h6>
                                <p class="m-b-0"><?php echo esc_html($user_info->user_email); ?></p>
                              </div>
                            </div>
                          </td>
                          <td class="text-right">
                            <h6 class="f-w-700"><?php echo esc_html(get_option('lead_currency')) . " " . esc_html($purchase_result->amount); ?>
                              <!--<i class="fas fa-level-down-alt text-c-red m-l-10"></i>-->
                            </h6>
                          </td>
                        </tr>
                    <?php
                      }
                    }
                  } else {
                    ?>
                    <tr>
                      <td colspan=2>No records found.</td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>
      <?php /*
				
				<div class="col-md-12 col-lg-6">
					<div class="card">
						<div class="card-header">
							<h5>Leads Revenue Year-Wise</h5>
						</div>
						<div class="card-block">
							<canvas id="salerevenueyearly"></canvas>
						</div>
					</div>
				</div> */ ?>
      <!--  sale analytics end -->


      <!--<div class="col-md-12 col-lg-6">
					<div class="card">
						<div class="card-header">
							<h5>Leads Growth</h5>
							<span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
						</div>
						<div class="card-block">
							<div id="line-example"></div>
						</div>
					</div>
				</div>-->

      <!--<div class="col-md-12 col-lg-6">
					<div class="card">
						<div class="card-header">
							<h5>Donut Chart</h5>
							<span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
						</div>
						<div class="card-block">
							<div id="donut-example"></div>
						</div>
					</div>
				</div>-->

    </div>

  </div>

  <?php
  $results1 = $wpdb->get_results("SELECT *,DATE(created_date) DateOnly,MONTH(created_date) as MonthOnly FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and MONTH(created_date)=" . Date('m') . " AND YEAR(created_date)=" . Date('Y') . " GROUP BY DateOnly");
  $results2 = $wpdb->get_results("SELECT *,DATE(created_date) DateOnly FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and YEAR(created_date)=" . Date('Y') . " GROUP BY DateOnly");
  $results3 = $wpdb->get_results("SELECT COUNT(*) count,YEAR(created_date) YearOnly FROM {$wpdb->prefix}ghaxlt_leads_payments where transaction_type like '%$environment%' GROUP BY YearOnly ORDER BY YearOnly ASC");

  $labels1 = array();
  $labels2 = array();
  $labels3 = array();
  $data1 = array();
  $data2 = array();
  $data3 = array();
  foreach ($results1 as $result1) {
    $timestamp1 = strtotime($result1->created_date);
    $new_date_format11 = date('Y-m-d', $timestamp1);
    $new_date_format12 = date('Y-m', $timestamp1);
    $new_date_format1 = date('j F Y', $timestamp1);
    $new_date_format2 = date('F Y', $timestamp1);
    $labels1[] = "'" . esc_html($new_date_format1) . "'";
    $labels2[] = "'" . esc_html($new_date_format2) . "'";
    $q1 = "SELECT COUNT(*) FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and created_date LIKE '%" . $new_date_format11 . "%'";
    $q2 = "SELECT COUNT(*) FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and created_date LIKE '%" . $new_date_format12 . "%'";
    $r1 = $wpdb->get_var($q1);
    $r2 = $wpdb->get_var($q2);
    $data1[] = $r1;
    $data2[] = $r2;
  }

  foreach ($results2 as $result2) {
    $timestamp1 = strtotime($result2->created_date);
    $new_date_format12 = date('Y-m', $timestamp1);
    $new_date_format2 = date('F Y', $timestamp1);
    $labels2[] = "'" . esc_html($new_date_format2) . "'";
    $q2 = "SELECT COUNT(*) FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and created_date LIKE '%" . $new_date_format12 . "%'";
    $r2 = $wpdb->get_var($q2);
    $data2[] = $r2;
  }

  foreach ($results3 as $result3) {
    $labels3[] = "'" . esc_html($result3->YearOnly) . "'";
    $q3 = "SELECT SUM(amount) FROM {$wpdb->prefix}ghaxlt_leads_payments WHERE transaction_type like '%$environment%' and YEAR(created_date) = " . $result3->YearOnly;
    $r3 = $wpdb->get_var($q3);
    $data3[] = $r3;
  }


  $totalsale1 = array_sum($data1);
  $totalsale2 = array_sum($data2);
  $label1str = implode(',', $labels1);
  $label1str1 = implode(',', $labels2);
  $label1str2 = implode(',', $labels3);
  $data1str = implode(',', $data1);
  $data1str1 = implode(',', $data2);
  $data1str2 = implode(',', $data3);
  ?>
  <script>
    var curr = '<?php echo esc_html(get_option('lead_currency')); ?>';

    var ctx = document.getElementById('saleChart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [<?php echo filter_var($label1str, FILTER_VALIDATE_REGEXP, array('options' => ['regexp' => "/\w,*/"])); ?>],
        datasets: [{
          label: 'Total Sales: <?php echo esc_js($totalsale1); ?>',
          data: [<?php echo filter_var($data1str, FILTER_VALIDATE_REGEXP, array('options' => ['regexp' => "/\w,*/"])); ?>],
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });



    var ctx1 = document.getElementById('saleyearChart').getContext('2d');
    var myChart1 = new Chart(ctx1, {
      type: 'line',
      data: {
        labels: [<?php echo filter_var($label1str1, FILTER_VALIDATE_REGEXP, array('options' => ['regexp' => "/\w,*/"])); ?>],
        datasets: [{
          label: 'Total Sales: <?php echo esc_js($totalsale1); ?>',
          data: [<?php echo filter_var($data1str1, FILTER_VALIDATE_REGEXP, array('options' => ['regexp' => "/\w,*/"])); ?>],
          borderColor: '#ffb88c',
          pointBackgroundColor: "#fff",
          pointBorderColor: "#ffb88c",
          pointHoverBackgroundColor: "#ffb88c",
          pointHoverBorderColor: "#fff",
          pointRadius: 4,
          pointHoverRadius: 4,
          fill: true
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });



    /*

    var ctx2 = document.getElementById('salerevenueyearly').getContext('2d');
    var myChart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: [<?php //echo $label1str2; 
                      ?>],
            datasets: [{
                label: 'Lead Revenue Year-wise IN '+curr,
                data: [<?php //echo $data1str2; 
                        ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    }); */
  </script>