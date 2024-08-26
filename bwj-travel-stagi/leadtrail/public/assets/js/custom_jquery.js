function leadsearch() {
  var country,
    state,
    zipcode,
    city,
    filterState,
    filterCountry,
    filterZipcode,
    min,
    max,
    filterCity,
    rows,
    i,
    country = document.getElementById('country');
  state = document.getElementById('state');
  city = document.getElementById('city');
  zipcode = document.getElementById('zipcode');
  min = 0;
  max = 0;
  if (document.getElementById('min_price')) {
    min = document.getElementById('min_price').value;
    max = document.getElementById('max_price').value;
  }

  filterCountry = country.value.toUpperCase();
  filterState = state.value.toUpperCase();
  filterCity = city.value.toUpperCase();
  filterZipcode = zipcode.value.toUpperCase();

  rows = document.querySelector('#leadstbl tbody').rows;
  for (i = 0; i < rows.length; i++) {
    var tdCountry = rows[i].cells[7].textContent.toUpperCase();
    var tdState = rows[i].cells[8].textContent.toUpperCase();
    var tdCity = rows[i].cells[9].textContent.toUpperCase();
    var tdzipcode = rows[i].cells[10].textContent.toUpperCase();
    var price = parseInt(rows[i].cells[12].textContent.toUpperCase());
    //alert(price);
    if (
      tdCountry.indexOf(filterCountry) > -1 &&
      tdState.indexOf(filterState) > -1 &&
      tdCity.indexOf(filterCity) > -1 &&
      tdzipcode.indexOf(filterZipcode) > -1 &&
      ((isNaN(min) && isNaN(max)) ||
        (isNaN(min) && price <= max) ||
        (min <= price && isNaN(max)) ||
        (min <= price && price <= max))
    ) {
      rows[i].style.display = '';
    } else {
      rows[i].style.display = 'none';
    }
  }
}
jQuery(document).ready(function ($) {
  $('#leadstbl').DataTable({
    ordering: false,
    columnDefs: [
      {
        targets: ['_all'],
        className: 'mdc-data-table__cell',
      },
    ],
    searchPlaceholder: 'Search',
  });

  $(document).on('click', '.leadaddtocart', function (event) {
    var id = $(this).attr('data-id');
    var $thisbutton = $(this);
    if ($("#leadstbl").attr("cart-count") == '0'){
      $.ajax({
        type: 'post',
        url: ajax_script.ajaxurl,
        data: { action: 'lead_add_to_cart', id: id, nc: ajax_script.nc },
        beforeSend: function (response) {
          $thisbutton.removeClass('added').addClass('loading');
          $(".buyleadbtn").css("pointer-events","none");
          $("#loading-animation").show();
        },
        complete: function (response) {
          $thisbutton.addClass('added').removeClass('loading');
          $("#loading-animation").hide();
        },
        success: function (response) {
          if (response) {
            alert(response);
            $(".buyleadbtn").css("pointer-events", "auto");
          } else {
            console.log("leadaddtocart: success");
            // $(".buyleadbtn").css("pointer-events", "auto");
            $thisbutton
              .parent()
              .html(
                '<a class="added remove_cart buyleadbtn" href="javascript:void(0)" data-id="' +
                  id +
                  '">Remove</a>'
              );
            $(this).parents("tr").addClass("added");
            const currentDateTime = new Date();
            const milliseconds = currentDateTime.getTime();
            window.location.href = window.location.href+"?"+milliseconds;
              }
        },
      });
    }
    else{
      alert('You are only allowed to redeem 1 lead at a time');
    }
    
    
    event.preventDefault();
  });

  $(document).on('click', '.remove_cart', function (event) {
    var id = $(this).attr('data-id');
    var $thisbutton = $(this);
    $.ajax({
      type: 'post',
      url: ajax_script.ajaxurl,
      data: { action: 'lead_remove_cart', id: id, nc: ajax_script.nc },
      beforeSend: function (response) {
        $thisbutton.addClass('loading');
        $(".buyleadbtn").css("pointer-events","none");
        $("#loading-animation").show();
      },
      complete: function (response) {
        $thisbutton.removeClass('loading');
        $("#loading-animation").show();
      },
      success: function (response) {
        console.log("remove_cart: success");
        // $(".buyleadbtn").css("pointer-events", "auto");
        $thisbutton
          .parent()
          .html(
            '<a class="leadaddtocart buyleadbtn" href="javascript:void(0)" data-id="' +
              id +
              '">Get Access</a> '
          );
        $(this).parents("tr").removeClass("added");
        const currentDateTime = new Date();
        const milliseconds = currentDateTime.getTime();
        window.location.href = window.location.href+"?"+milliseconds;
      },
    });
    
    event.preventDefault();
  });

  $(document).on('click', '.confirmaddtocart', function (event) {
    event.preventDefault();
    console.log("Run JavaScript - confirmaddtocart");
    var $thisbutton = $(this);
    if (($thisbutton.hasClass("daily-count-0") && $thisbutton.hasClass("annual_buyer")) || $thisbutton.hasClass("administrator")){
      if(!$thisbutton.hasClass("cart-count-0") ){
        console.log("Cart is not empty.");
        $.ajax({
          type: 'post',
          url: ajax_script.ajaxurl,
          data: { action: 'confirm_add_to_cart', nc: ajax_script.nc },
          beforeSend: function (response) {
            $thisbutton.addClass('loading');
            $(".buyleadbtn").css("pointer-events","none");
            $("#loading-animation").show();
          },
          complete: function (response) {
            $thisbutton.removeClass('loading');
            $("#loading-animation").hide();
          },
          success: function (response) {
            // $(".buyleadbtn").css("pointer-events", "auto");
            if (response.data.redirect_url) {
              const currentDateTime = new Date();
              const milliseconds = currentDateTime.getTime();
              window.location.href = response.data.redirect_url+"?"+milliseconds;
            }
          },
          error: function() {
            alert('There was an error processing your request. Please try again later.');
          }
        });
      }
      else{
        alert("Please select 1 lead and try again.")
      }
    }
    else{
      $("#lead-purchase-checkout a").click();
    }
  });

  $('.directbuy').click(function (event) {
    var id = $(this).attr('data-id');
    var $thisbutton = $(this);
    $.ajax({
      type: 'post',
      url: ajax_script.ajaxurl,
      data: { action: 'directleadtobuy', id: id, nc: ajax_script.nc },
      success: function (response) {
        window.location.href = ajax_script.redirecturl;
      },
    });
    event.preventDefault();
  });

  $('.clear-cart').click(function (event) {
    event.preventDefault();
    var id = $(this).parent('li').attr('data-id');
    if (jQuery(".remove_cart[data-id='" + id + "']").length){
      jQuery(".remove_cart[data-id='" + id + "']").click();
    }
    else{
      $.ajax({
        type: 'post',
        url: ajax_script.ajaxurl,
        data: { action: 'lead_remove_cart', id: id, nc: ajax_script.nc },
        beforeSend: function (response) {
          $(".buyleadbtn").css("pointer-events","none")
        },
        complete: function (response) {
        },
        success: function (response) {
          console.log("remove_cart: success: ", id);
          $(".buyleadbtn").css("pointer-events", "auto");
          const currentDateTime = new Date();
          const milliseconds = currentDateTime.getTime();
          window.location.href = window.location.href+"?"+milliseconds;
        },
      });
      
    }
  });
});

function leadsearch_attribute() {
  var country,
    state,
    zipcode,
    city,
    filterState,
    filterCountry,
    filterZipcode,
    min,
    max,
    filterCity,
    rows,
    i,
    country = document.getElementById('country');
  state = document.getElementById('state');
  city = document.getElementById('city');
  zipcode = document.getElementById('zipcode');
  min = 0;
  max = 0;
  if (document.getElementById('attribute_min_price')) {
    min = document.getElementById('attribute_min_price').value;
    max = document.getElementById('attribute_max_price').value;
  }

  filterCountry = country.value.toUpperCase();
  filterState = state.value.toUpperCase();
  filterCity = city.value.toUpperCase();
  filterZipcode = zipcode.value.toUpperCase();

  rows = document.querySelector('#leadstbl tbody').rows;
  for (i = 0; i < rows.length; i++) {
    var tdCountry = rows[i].cells[6].textContent.toUpperCase();
    var tdState = rows[i].cells[7].textContent.toUpperCase();
    var tdCity = rows[i].cells[8].textContent.toUpperCase();
    var tdzipcode = rows[i].cells[9].textContent.toUpperCase();
    var price = parseInt(rows[i].cells[11].textContent.toUpperCase());
    //alert(price);
    if (
      tdCountry.indexOf(filterCountry) > -1 &&
      tdState.indexOf(filterState) > -1 &&
      tdCity.indexOf(filterCity) > -1 &&
      tdzipcode.indexOf(filterZipcode) > -1 &&
      ((isNaN(min) && isNaN(max)) ||
        (isNaN(min) && price <= max) ||
        (min <= price && isNaN(max)) ||
        (min <= price && price <= max))
    ) {
      rows[i].style.display = '';
    } else {
      rows[i].style.display = 'none';
    }
  }
}

var lead_state = [];
if (document.getElementById('country')) {
  document.getElementById('country').addEventListener('change', (event) => {
    lead_state = [];
    var country_id = event.target.value;
    if (states.length > 0) {
      for (var i = 0; i < states.length; i++) {
        if (states[i].country_code == country_id) {
          lead_state.push(states[i].name);
        } else {
        }
      }
    }
    autocomplete(document.getElementById('state'), lead_state);
  });
}
