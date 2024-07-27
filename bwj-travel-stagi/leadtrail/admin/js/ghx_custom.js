(function ($) {
  //leads_display
  var sett;
  var table = $('#leadstbl').DataTable({
    autoWidth: false,
    stateSave: true,
    ordering: false,
    stateSaveCallback: function (settings, data) {
      localStorage.setItem(
        'DataTables_' + settings.sInstance,
        JSON.stringify(data)
      );
      localStorage.setItem(
        'searchData',
        JSON.stringify({
          zipcode: $('#zipcode').val(),
          country: $('#country').val(),
          state: $('#state').val(),
          city: $('#city').val(),
          email: $('#email').val(),
          pricemin: $('#pricemin').val(),
          pricemax: $('#pricemax').val(),
        })
      );
    },
    stateLoadCallback: function (settings) {
      const data = JSON.parse(localStorage.getItem('searchData'));
      $('#zipcode').val(data ? data.zipcode : '');
      $('#country').val(data ? data.country : '');
      $('#state').val(data ? data.state : '');
      $('#city').val(data ? data.city : '');
      $('#email').val(data ? data.email : '');
      sett = settings.sInstance;
      return JSON.parse(
        localStorage.getItem('DataTables_' + settings.sInstance)
      );
    },
    columnDefs: [
      {
        targets: ['_all'],
        className: 'mdc-data-table__cell',
      },
    ],
  });
  $('.custom-input').on('keyup change', function () {
    var column = $(this).attr('data-column');
    table.columns(column).search(this.value).draw();
  });

  $('#resetBtn').click(function () {
    localStorage.removeItem('searchData');
    localStorage.removeItem('DataTables_' + sett);
    location.reload();
  });

  $('#pricemin,#pricemax').on('change', function () {
    table.draw();
  });

  $('.show_data').click(function () {
    var myval = $(this).attr('data-value');
    Swal.fire({
      title: 'Lead Data',
      html: myval,
      showCloseButton: true,
      showCancelButton: true,
      focusConfirm: false,
      /*confirmButtonText:
            			'<i class="fa fa-thumbs-up"></i> Great!',
            		  confirmButtonAriaLabel: 'Thumbs up, great!',
            		  cancelButtonText:
            			'<i class="fa fa-thumbs-down"></i>',
            		  cancelButtonAriaLabel: 'Thumbs down'*/
    });
  });

  jQuery(document).on('click', '.cust_b_delete.tb-leadstbl', function () {
    Swal.fire({
      title: 'Are you sure?',
      text: '',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#DD6B55',
      confirmButtonText: 'Yes, Delete it!',
      cancelButtonText: 'Cancel',
      closeOnConfirm: false,
      closeOnCancel: false,
    }).then((result) => {
      if (result.isConfirmed) {
        var id = jQuery(this).attr('data-lead-id');
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'all_delete_action',
            id: id,
            table: 'leads',
          },
          success: function (data) {
            var rowID = '#delete_' + id;
            table.row(jQuery(rowID)).remove().draw(false);
          },
        });
      }
    });
  });

  //For both Group & quality table
  var grouptable = $('#leadsgrptbl').DataTable({
    autoWidth: false,
    stateSave: true,
    columnDefs: [
      {
        targets: ['_all'],
        className: 'mdc-data-table__cell',
      },
      {
        orderable: false,
        targets: [4],
      },
    ],
  });

  jQuery(document).on('click', '.cust_b_delete.tb-leadsgrptbl', function () {
    Swal.fire({
      title: 'Are you sure?',
      text: '',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#DD6B55',
      confirmButtonText: 'Yes, Delete it!',
      cancelButtonText: 'Cancel',
      closeOnConfirm: false,
      closeOnCancel: false,
    }).then((result) => {
      if (result.isConfirmed) {
        var id = jQuery(this).attr('data-group-id');
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'all_delete_action',
            id: id,
            table: 'groups',
          },
          success: function (data) {
            var rowID = '#delete_' + id;
            grouptable.row(jQuery(rowID)).remove().draw(false);
            //jQuery(rowID).remove();
          },
        });
      }
    });
  });

  //qualities
  jQuery(document).on('click', '.cust_b_delete.tb-qualities', function () {
    Swal.fire({
      title: 'Are you sure?',
      text: '',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#DD6B55',
      confirmButtonText: 'Yes, Delete it!',
      cancelButtonText: 'Cancel',
      closeOnConfirm: false,
      closeOnCancel: false,
    }).then((result) => {
      if (result.isConfirmed) {
        var id = jQuery(this).attr('data-quality-id');
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'all_delete_action',
            id: id,
            table: 'qualities',
          },
          success: function (data) {
            var rowID = '#delete_' + id;
            table1.row(jQuery(rowID)).remove().draw(false);
            //jQuery(rowID).remove();
          },
        });
      }
    });
  });

  //categories
  var catstable = $('#leadscattbl').DataTable({
    autoWidth: false,
    stateSave: true,
    columnDefs: [
      {
        targets: ['_all'],
        className: 'mdc-data-table__cell',
      },
      {
        orderable: false,
        targets: [5],
      },
    ],
  });

  jQuery(document).on('click', '.cust_b_delete.tb-leadscattbl', function () {
    Swal.fire({
      title: 'Are you sure?',
      text: '',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#DD6B55',
      confirmButtonText: 'Yes, Delete it!',
      cancelButtonText: 'Cancel',
      closeOnConfirm: false,
      closeOnCancel: false,
    }).then((result) => {
      if (result.isConfirmed) {
        var id = jQuery(this).attr('data-cat-id');
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'all_delete_action',
            id: id,
            table: 'cats',
          },
          success: function (data) {
            var rowID = '#delete_' + id;
            catstable.row(jQuery(rowID)).remove().draw(false);
            //jQuery(rowID).remove();
          },
        });
      }
    });
  });

  //Edit lead data
  jQuery('#lead_discount').on('keyup', function (e) {
    let get_price = document.getElementById('lead_discount').value;
    //
    if (get_price > 100) {
      jQuery('input[name=lead_discount').val(100);
    } else {
      let arr = get_price.split('.');
      if (arr[1]) {
        if (arr[1].length > 2) {
          var set_price = parseFloat(get_price).toFixed(2);
          jQuery('input[name=lead_discount').val(set_price);
        }
      }
      //
    }
  });
})(jQuery);

