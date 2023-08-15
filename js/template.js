$(function() {
  $('#side-menu').metisMenu();
});

// hide flash messages after some times
$(document).ready( function() {
  $('.dismissable').delay(10000).fadeOut();
});

// Printing function
$(document).ready(function() {
    $(".print_div").find("#print").on("click", function() {
        var dv_id = $(this).parents(".print_div").attr("id");
        $("#" + dv_id).print({
            //Use Global styles
            globalStyles: true,
            //Add link with attrbute media=print
            mediaPrint: false,
            iframe: true,
            //Don"t print this
            noPrintSelector: ".avoid"
        });
    });
});

/*!Datatables Initialization & remove search label
if (document.getElementById("table")) {
$(document).ready(function() {
	$("#table").dataTable({
		"oLanguage": { "sSearch": "" } ,
		"iDisplayLength": 25
	});
} );
}
*/
//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    })
})
// Popup windows 
function popupwindow(url, title, w, h) {
	var left = (screen.width/2)-(w/2);
	var top = (screen.height/2)-(h/2);
	return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}


// close windows after selecting date
$(".form-control").on("changeDate", function(e){
	$(this).datepicker("hide");
});


if ($("#date1").length){
    $("#date1").datepicker({
        format: "dd-mm-yyyy"
    });
}

if ($("#date2").length){
    $("#date2").datepicker({
        format: "dd-mm-yyyy"
    });
}
if ($("#date3").length){
    $("#date3").datepicker({
        format: "dd-mm-yyyy"
    });
}

//turn off autocomplete for date fields
$('#date1').attr('autocomplete','off');
$('#date2').attr('autocomplete','off');
$('#date3').attr('autocomplete','off');

//It restrict the non-numbers
var specialKeys = new Array();
specialKeys.push(8,46); //Backspace
function IsNumeric(e) {
    var keyCode = e.which ? e.which : e.keyCode;
    //console.log( keyCode );
    var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
    return ret;
}

function validateUHID(event) {
    var key = window.event ? event.keyCode : event.which;
    if (event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 13) {
        return true;
    } else if ( key < 48 || key > 57 ) {
        return false;
    }else {
    	return true;
    }
}

// Ajax Query
 function Ajax_Query(str,loadArea,DataName) {
    var xmlhttp = null;
  if (str=="") {
    document.getElementById(loadArea).innerHTML="";
    return;
  } 
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById(loadArea).innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET","ajax-query.php?"+DataName+"="+str,true);
  xmlhttp.send();
}

//// Chosen Select(plugin) declared for select menu
if ($(".chosen-select").length){
$(".chosen-select").chosen({
    disable_search_threshold: 10,
    no_results_text: "Oops, nothing found!",
    width: "100%"
});
}
//to check all checkboxes
$(document).on("change","#check_all",function(){
	$("input[class=case]:checkbox").prop("checked", $(this).is(":checked"));
}); 

//autocomplete script
$(document).on('focus','.autocomplete_customer',function(){
	type = $(this).data('type');
	
	//if(type =='CustomerCode' )autoTypeNo=0;
	if(type =='CustomerName' )autoTypeNo=1; 
    if(type =='PatientName' )autoTypeNo=1; 

    /*if(location.host!="localhost" && location.host!="192.168.1.2"){ // in production
		
			let text = location.host;
			let position = text.search("amarhms");
			if(position>1){
				var full_path = location.protocol + "//" + location.host+"/arch/";
			}else{		
				var full_path = location.protocol + "//" + location.host+"/";
			}	
	}else{ 
		var full_path = location.protocol + "//" + location.host+"/ahms/";
	}*/
	var full_path = location.protocol + "//" + location.host+"/ahms/";
	
    url = full_path+"/ajax.php";	
	
	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : url,
				dataType: "json",
				method: 'post',
				data: {
				   name_startsWith: request.term,
				   type: type
				},
				 success: function( data ) {
					 response( $.map( data, function( item ) {
					 	var code = item.split("|");
						return {
							label: code[autoTypeNo],
							value: code[autoTypeNo],
							data : item
						}
					}));
				}
			});
		},
		autoFocus: true,	      	
		minLength: 3,
		select: function( event, ui ) {
			var names = ui.item.data.split("|");		
			$('#customer_id').val(names[0]);
			$('#customer_name').val(names[1]);
		}		      	
	});
});

// Search Patients
//autocomplete script
$(document).on('focus','.autocomplete_Patient',function(){
  type = $(this).data('type');
  
  //if(type =='CustomerCode' )autoTypeNo=0;
  if(type =='patient_name' )autoTypeNo=1; 
    
	/*
	if(location.host!="localhost" && location.host!="192.168.1.2"){ // in production
		
			let text = location.host;
			let position = text.search("amarhms");
			if(position>1){
				var full_path = location.protocol + "//" + location.host+"/arch/";
			}else{		
				var full_path = location.protocol + "//" + location.host+"/";
			}	
	}else{ 
		var full_path = location.protocol + "//" + location.host+"/ahms/";
	}
    */
	var full_path = location.protocol + "//" + location.host+"/ahms/";
    
    url = full_path+"/ajax.php";  
  
  $(this).autocomplete({
    source: function( request, response ) {
      $.ajax({
        url : url,
        dataType: "json",
        method: 'post',
        data: {
           name_startsWith: request.term,
           type: type
        },
         success: function( data ) {
           response( $.map( data, function( item ) {
            var code = item.split("|");
            return {
              label: code[autoTypeNo],
              value: code[autoTypeNo],
              data : item
            }
          }));
        }
      });
    },
    autoFocus: true,          
    minLength: 4,
    select: function( event, ui ) {
      var names = ui.item.data.split("|");    
      $('#patient_id').val(names[0]);
      $('#patientsName').val(names[1]);
    }           
  });
});

/////// Customer Search using select2

if( $('#contact_ajax_search').length )// use this if you are using id to check
{
    $(document).ready(function(){
        search_Type =  $('#searchType').val();
        
        if(search_Type=='contact'){
            placeholderText = 'Enter Party Name/Mobile/ID.';
        }else if(search_Type=='pc'){
            placeholderText = 'Enter PC/Referred Name/Mobile/ID.';
        }else if(search_Type=='employee'){
            placeholderText = 'Enter Employee Name/Mobile/ID.';
        }else if(search_Type=='employeeCustomerID'){
            placeholderText = 'Enter Employee Name/Mobile/ID.';
        }
        else if(search_Type=='ReferredDoctor'){
            placeholderText = 'Enter Doctor Name/Mobile/ID.';
        }
        
        /*
	if(location.host!="localhost" && location.host!="192.168.1.2"){ // in production
		
			let text = location.host;
			let position = text.search("amarhms");
			if(position>1){
				var full_path = location.protocol + "//" + location.host+"/arch/";
			}else{		
				var full_path = location.protocol + "//" + location.host+"/";
			}	
	}else{ 
		var full_path = location.protocol + "//" + location.host+"/ahms/";
	}
    */
	var full_path = location.protocol + "//" + location.host+"/ahms/";
        
        url = full_path+"/ajax-select2.php";

     $("#contact_ajax_search").select2({
      minimumInputLength: 3,
      allowClear: true,
      placeholder: {
        id: -1,
        text: placeholderText,
      },
      ajax: { 
       url: url,
       type: "post",
       dataType: "json",
       delay: 250,        
       data: function (params) {
        return {
          searchType : search_Type,
          searchCustomer: params.term // search term
        };
       },
       processResults: function (response) {
         return {
            results: response
         };
       },
       cache: true
      }
     });
    });            
}

if( $('#contact_ajax_search2').length )// use this if you are using id to check
{
    $(document).ready(function(){
        search_Type2 =  $('#searchType2').val();
        
        if(search_Type2=='contact'){
            placeholderText = 'Enter Party Name/Mobile/ID.';
        }else if(search_Type2=='pc'){
            placeholderText = 'Enter PC/Referred Name/Mobile/ID.';
        }else if(search_Type2=='employee'){
            placeholderText = 'Enter Employee Name/Mobile/ID.';
        }else if(search_Type2=='ReferredDoctor'){
            placeholderText = 'Enter Doctor Name/Mobile/ID.';
        }
        
        /*
	if(location.host!="localhost" && location.host!="192.168.1.2"){ // in production
		
			let text = location.host;
			let position = text.search("amarhms");
			if(position>1){
				var full_path = location.protocol + "//" + location.host+"/arch/";
			}else{		
				var full_path = location.protocol + "//" + location.host+"/";
			}	
	}else{ 
		var full_path = location.protocol + "//" + location.host+"/ahms/";
	}
    */
	var full_path = location.protocol + "//" + location.host+"/ahms/";
        
        url = full_path+"/ajax-select2.php";

     $("#contact_ajax_search2").select2({
      minimumInputLength: 3,
      allowClear: true,
      placeholder: {
        id: -1,
        text: placeholderText,
      },
      ajax: { 
       url: url,
       type: "post",
       dataType: "json",
       delay: 250,        
       data: function (params) {
        return {
          searchType : search_Type2,
          searchCustomer: params.term // search term
        };
       },
       processResults: function (response) {
         return {
            results: response
         };
       },
       cache: true
      }
     });
    });            
}


if( $('#contact_ajax_search3').length )// use this if you are using id to check
{
    $(document).ready(function(){
        search_Type3 =  $('#searchType3').val();
        
        if(search_Type3=='contact'){
            placeholderText = 'Enter Party Name/Mobile/ID.';
        }else if(search_Type3=='pc'){
            placeholderText = 'Enter PC/Referred Name/Mobile/ID.';
        }else if(search_Type3=='employee'){
            placeholderText = 'Enter Employee Name/Mobile/ID.';
        }else if(search_Type3=='ReferredDoctor'){
            placeholderText = 'Enter Doctor Name/Mobile/ID.';
        }
        
        /*
    if(location.host!="localhost" && location.host!="192.168.1.2"){ // in production
        
            let text = location.host;
            let position = text.search("amarhms");
            if(position>1){
                var full_path = location.protocol + "//" + location.host+"/arch/";
            }else{      
                var full_path = location.protocol + "//" + location.host+"/";
            }   
    }else{ 
        var full_path = location.protocol + "//" + location.host+"/ahms/";
    }
    */
    var full_path = location.protocol + "//" + location.host+"/ahms/";
        
        url = full_path+"/ajax-select2.php";

     $("#contact_ajax_search3").select2({
      minimumInputLength: 3,
      allowClear: true,
      placeholder: {
        id: -1,
        text: placeholderText,
      },
      ajax: { 
       url: url,
       type: "post",
       dataType: "json",
       delay: 250,        
       data: function (params) {
        return {
          searchType : search_Type3,
          searchCustomer: params.term // search term
        };
       },
       processResults: function (response) {
         return {
            results: response
         };
       },
       cache: true
      }
     });
    });            
}

  document.addEventListener('keydown', function(event) {
    if(event.keyCode == 74 && event.ctrlKey){
        event.preventDefault();
    }
  });
// Patient Action Sidemenu Close Animation
// $("#actionPatientSidemenu .dropdown").hover(function() {
//   $(this).toggleClass("dropdown-close");
// });
// Patient Action Float Sidemenu Hover
$(function() {
    $("#floatPatientActionWrapper #actionPatientSidemenu .dropdown").hover(
      function(){ $(this).addClass('open') },
      function(){ $(this).removeClass('open') }
    );
  });

// Tooltip Initialization
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

/*! Back To Top */
$(window).scroll(function() {
  if ($(this).scrollTop() > 50 ) {
    $('.scrolltop:hidden').stop(true, true).fadeIn();
  } else {
    $('.scrolltop').stop(true, true).fadeOut();
  }
});
$('.scrolltop').on("click",function(){
    $('html,body').animate({ scrollTop: 0 }, 'slow', function () {
    });

  });

function close_window() {
    window.opener.location.reload(false);
    close();
};  

// Clear Input Field On Hover
Array.prototype.forEach.call(document.querySelectorAll('.form-group>[data-clear-input]'), function(el) {
  el.addEventListener('click', function(e) {
    e.target.previousElementSibling.value = '';
  });
});


//autocomplete script for health card member
$(document).on('focus','.autocomplete_Hcard_Patient',function(){
  type = $(this).data('type');

  if(type =='patient_name' )autoTypeNo=1;
      
    /*
	if(location.host!="localhost" && location.host!="192.168.1.2"){ // in production
		
			let text = location.host;
			let position = text.search("amarhms");
			if(position>1){
				var full_path = location.protocol + "//" + location.host+"/arch/";
			}else{		
				var full_path = location.protocol + "//" + location.host+"/";
			}	
	}else{ 
		var full_path = location.protocol + "//" + location.host+"/ahms/";
	}
    */
	var full_path = location.protocol + "//" + location.host+"/arch/";
    
    url = full_path+"/ajax.php";  
  
  $(this).autocomplete({
    source: function( request, response ) {
      $.ajax({
        url : url,
        dataType: "json",
        method: 'post',
        data: {
           name_startsWith: request.term,
           type: type
        },
         success: function( data ) {
           response( $.map( data, function( item ) {
            var code = item.split("|");
            return {
              label: code[autoTypeNo],
              value: code[autoTypeNo],
              data : item
            }
          }));
        }
      });
    },
    autoFocus: true,          
    minLength: 4,
    select: function( event, ui ) {
      var names = ui.item.data.split("|"); 
        id_arr = $(this).attr('id');
         id = id_arr.split("_");   
      $('#patientId_'+id[1]).val(names[0]);
      $('#patientsName_'+id[1]).val(names[1]);
    }           
  });
});

//autocomplete script for health card member End