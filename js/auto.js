	      
//adds extra table rows
var i=$('table tr').length;
$(".addmore").on('click',function(){
	html = '<tr>';
	html += '<td><input class="case" type="checkbox"/></td>';
	html += '<td><input type="text" data-type="productCode" name="itemNo[]" id="itemNo_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';
	html += '<td><input type="text" data-type="productName" name="itemName[]" id="itemName_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';
	html += '<td><input type="text" name="price[]" id="price_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>';	
	html += '<td><input type="text" name="price[]" id="price_'+i+'" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>';	
	html += '<td><input type="text" name="due[]" id="due_'+i+'" class="form-control TotalDues" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>';
	html += '<td><input type="number" name="paid[]" id="paid_'+i+'" class="form-control changesNo TotalPaidPrice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"></td>';
	html += '</tr>';
	$('table').append(html);
	i++;
});

//to check all checkboxes
$(document).on('change','#check_all',function(){
	$('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
});

//deletes the selected table rows
$(".delete").on('click', function() {
	$('.case:checkbox:checked').parents("tr").remove();
	$('#check_all').prop("checked", false); 
	calculateTotal();
});

//autocomplete script
$(document).on('focus','.autocomplete_txt',function(){
	
	type = $(this).data('type');

   var memID = document.getElementById("membershipID");
   var pc_cid = document.getElementById("contact_ajax_search3");
   var mateIDS = document.getElementById("mateIDS");
  
   var rowNumbers = $('#table3 tr').length; 

    if(rowNumbers){    	
		rowNumbers = 50 + rowNumbers;	
	}else{
		rowNumbers = 50;
	}


      if(memID){
      	var membershipID = memID.value;
      }else{
      	var membershipID = 0;
      }

       if(pc_cid){
      	var pc_CID = pc_cid.value;
      }else{
      	var pc_CID = 0;
      }  

        if(mateIDS){
      	var mateIDS = mateIDS.value;
      }else{
      	var mateIDS = 0;
      }  



	if(type =='productCode' )autoTypeNo=0;
	if(type =='productName' )autoTypeNo=1; 
    
    if(type =='InvestCode' )autoTypeNo=0;
	if(type =='InvestName' )autoTypeNo=1; 	
    
    if(type =='ConsultCode' )autoTypeNo=0;
	if(type =='ConsultName' )autoTypeNo=1; 
    
    if(type =='IPDCode' )autoTypeNo=0;
	if(type =='IPDname' )autoTypeNo=1;

	if(type =='IPDAutoCode' )autoTypeNo=0;
	if(type =='IPDAutoname' )autoTypeNo=1;		
	
	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : 'ajax.php',
				dataType: "json",
				method: 'post',
				data: {
				   name_startsWith: request.term,
				   type: type,
				   memId:membershipID,
				   pcCId:pc_CID,
				   rowNumbers:rowNumbers,
				   mateIDS:mateIDS				  
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
		minLength: 2,
		select: function( event, ui ) {
			var names = ui.item.data.split("|");						
			id_arr = $(this).attr('id');
	  		id = id_arr.split("_");
			$('#itemNo_'+id[1]).val(names[0]);
			$('#itemName_'+id[1]).val(names[1]);
			$('#qty_'+id[1]).val(1);
            $('#referral_'+id[1]).val(names[3]);
            $('#net_price_'+id[1]).val(names[2]);
            $('#sell_'+id[1]).val(names[3]);            
            $('#discount_'+id[1]).val(names[4]);
            $('#discount2_'+id[1]).val(names[5]);
			$('#price_'+id[1]).val(names[2]);
			$('#ItemPrice_'+id[1]).val(names[2]);
			/*$('#paid_'+id[1]).val( 1*names[2] );*/
			$('#paid_'+id[1]).val(names[6]);
			$('#maxdiscount_'+id[1]).val(names[7]);
			$('#billPrice_'+id[1]).val(names[10]);
			/* var fdata=accessoriesInfo+names[8];*/ 
			/*$('#mateRialInfo').html(fdata);*/
			$(".mateRialInfo").append(names[8]);			
			$("#mateIDS").val(names[9]);
			calculateTotal();
		}		      	
	});
});

//only pharma new purchases && new po use for barcode started here
//autocomplete script
$(document).on('focus','.autobarcode_txt_po',function(){
	
	type = $(this).data('type');
	if(type =='productCodes' )autoTypeNo=0;
	if(type =='productNames' )autoTypeNo=1;     		

	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : 'ajax.php',
				dataType: "json",
				method: 'post',
				data: {
				   name_startsWith: request.term,
				   type: type
				},
				 success: function( data ) {
					 response( $.map( data, function( item ) {
					 	
					var names = item.split("|");
                     
					 	itemID=names[0];
				  		itemName=names[1];
				  		item_quantity=1;
				  		referral=names[3];				  	
				  		net_price=names[2];
				  		discount=names[4];
				  		discount2=names[5];
				  		price=names[2];				  		
				  		paid=names[6];	
				  		maxdiscount=names[7];
				  		amount=item_quantity*net_price;
				  		var i=$('#table-auto tr').length;				  					  		
                        $('#itemNameSearch').val("");
                        $('#itemIdNo').val("");
                     
                       
                        var pos_table='';
                        pos_table +='<tr>';   
                        pos_table +='<td class="avoid"><div class="checkbox-wrapper"><label><input class="case" type="checkbox" name="itemID['+i+'][checkbox]" value="'+itemID+'"  tabindex="-1"><span class="checkmark"></span></label></div></td>';
                        pos_table +='<td>'+i+'</td> <input type="hidden" data-type="productCode" name="itemID['+i+'][medID]" id="itemNo_'+i+'" value="'+itemID+'" class="form-control autocomplete_txt" autocomplete="off" />';
                        pos_table +='<td class="text-left"><input type="text" data-type="productName" id="itemName_'+itemName+'" value="'+itemName+'" class="form-control autocomplete_txt item-name-width" autocomplete="off" /></td>';
						pos_table += '<td><input type="text" name="itemID['+i+'][price]" value="'+price+'" id="price_'+i+'" onkeypress="return IsNumeric(event);" class="form-control Price amount-width" tabindex="-1"/></td>';
						pos_table +='<input type="hidden" name="itemID['+i+'][net_price]" value="'+net_price+'" id="net_price_'+i+'" onkeypress="return IsNumeric(event);" class="form-control" />';
						pos_table +='<td><input type="text" name="itemID['+i+'][qty]" id="qty_'+i+'" class="form-control qty quantity-width" value="'+item_quantity+'" onkeypress="return IsNumeric(event);" /></td>';
						pos_table +='<td><input type="text" id="paid_'+i+'" value="" class="form-control TP_Payable amount-width" /></td>';
						pos_table +='<td><input type="text" id="vat_'+i+'" class="form-control vat quantity-width" value="0" onkeypress="return IsNumeric(event);" /></td>';
						pos_table +='<td><input type="text" name="itemID['+i+'][amount]" value="" id="amount_'+i+'" readonly=""  class="form-control TotalPayable amount-width" required="" tabindex="-1"/></td>';
						pos_table +='<td><i class="far fa-trash-alt delete"></i></td>';					
					    pos_table +='</tr>';
					
					$(".order_holder").append(pos_table);	
					}));
				}
			});
		}
		      	
	});
});


$(document).on('focus','.autobarcode_txt_purchase',function(){
	
	type = $(this).data('type');
	if(type =='productCodes' )autoTypeNo=0;
	if(type =='productNames' )autoTypeNo=1;     		

	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : 'ajax.php',
				dataType: "json",
				method: 'post',
				data: {
				   name_startsWith: request.term,
				   type: type
				},
				 success: function( data ) {
					 response( $.map( data, function( item ) {
					 	
						var names = item.split("|");                     
					 	itemID=names[0];
				  		itemName=names[1];
				  		item_quantity=1;
				  		net_price=names[2];
				  		sell_price=names[3];			  		
				  		price=names[2];					  		
				  		amount=item_quantity*net_price;
				  		var i=$('#table-auto tr').length;				  					  		
                        $('#itemNameSearch').val("");
                        $('#itemIdNo').val("");
                        //expiredate started
						var currentDate = new Date();
						var month = currentDate.getMonth()+1;
						if (month < 10) month = "0" + month;
						var dateOfMonth = currentDate.getDate();
						if (dateOfMonth < 10) dateOfMonth = "0" + dateOfMonth;
						var year = currentDate.getFullYear()+2;
						var expiredate = dateOfMonth + "-" + month + "-" + year;
              			  //expiredate ended

                        html=''; 
						html += '<tr>';       
						html += '<td class="avoid"><div class="checkbox-wrapper"><label><input class="case" type="checkbox" name="itemID[' + i + '][checkbox]" value="'+itemID+'"/><span class="checkmark"></span></label></div></td>';
						html += '<td class="avoid">' + i + '</td>';
						html += '<input type="hidden" data-type="productCode" name="itemID[' + i + '][medID]" id="itemNo_' + i + '" value="'+itemID+'" class="form-control autocomplete_txt" autocomplete="off"/>';
						html += '<td class="text-left"><input type="text" data-type="productName" id="itemName_' + i + '" value="'+itemName+'" class="form-control autocomplete_txt" autocomplete="off"></td>';
						html += '<td class="text-left"><input type="text" name="itemID[' + i + '][expire]" value="'+ expiredate +'" class="form-control changesNo expire" tabindex="-1"></td>';
						html += '<td><input type="text" name="itemID[' + i + '][batch]" class="form-control" tabindex="-1"></td>';
						html += '<td><input type="text" name="itemID[' + i + '][price]" value="'+price+'"  id="price_' + i + '" onkeypress="return IsNumeric(event);" class="form-control Price" ></td>';
						html += '<input type="hidden" name="itemID[' + i + '][net_price]" value="'+net_price+'" id="net_price_' + i + '" onkeypress="return IsNumeric(event);" class="form-control" >';
						html += '<td><input type="text" name="itemID[' + i + '][qty]" id="qty_' + i + '" class="form-control qty" value="'+item_quantity+'" onkeypress="return IsNumeric(event);"></td>';
						html += '<td><input type="text" id="paid_' + i + '" value="" class="form-control TP_Payable amount-width"></td>';
						html += '<td><input type="text" id="vat_' + i + '" class="form-control vat quantity-width" value="0" onkeypress="return IsNumeric(event);"/></td>';
						html += '<td><input type="text" name="itemID[' + i + '][amount]" id="amount_' + i + '" value="" class="form-control  TotalPayable" readonly="" tabindex="-1"></td>';
						html += '<td><input type="text" name="itemID[' + i + '][sell]" value="'+sell_price+'" id="sell_' + i + '" onkeypress="return IsNumeric(event);" class="form-control" ></td>';
						html += '<td><i class="far fa-trash-alt delete"></i></td>';
						html += '</tr>';
					
					$(".order_holder").append(html);	
					}));
				}
			});
		}
		      	
	});
});
//END only pharma new purchases && new po use for barcode END here




//find due item by item
$(document).on('change keyup blur','.changesNo',function(){
	id_arr = $(this).attr('id');
	id = id_arr.split("_");
	paid = $('#paid_'+id[1]).val();
	price = $('#price_'+id[1]).val();
	if( paid!='' && price !='' ) $('#due_'+id[1]).val( (parseFloat(price)-parseFloat(paid)).toFixed(2) );	
	calculateTotal();
});

//total price calculation 
function calculateTotal(){
	DueTotal = 0 ; total = 0;  paidAmount=0;
	
	$('.TotalDues').each(function(){
		if($(this).val() != '' )DueTotal += parseFloat( $(this).val() );
	});
	$('#DueTotal').val( DueTotal.toFixed(2) );
	
	$('.TotalPaidPrice').each(function(){
		if($(this).val() != '' )paidAmount += parseFloat( $(this).val() );
	});
	$('#amountPaid').val( paidAmount.toFixed(2) );		
}

//It restrict the non-numbers
var specialKeys = new Array();
specialKeys.push(8,46); //Backspace
function IsNumeric(e) {
    var keyCode = e.which ? e.which : e.keyCode;
    console.log( keyCode );
    var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
    return ret;
}

function IsMatch(str,sn) { 
      var due =  $('#due_'+sn).val();
     if(due >= 0.00){
        $("#paid_"+sn).val(str);
    }else{    
        alert("Enter Correct Value.Due amount can't be negative.!!!");       
        $("#paid_"+sn).val("");      
    }    
}

//datepicker
$(function () {
    $('#invoiceDate').datepicker({});
});


//ajax autocomplete script
$(document).on('focus','.autocomplete_agent',function(){
	type = $(this).data('type');
	
	//if(type =='CustomerCode' )autoTypeNo=0;
	if(type =='AgentName' )autoTypeNo=1; 	
	
	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : 'ajax.php',
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
			$('#agent_id').val(names[0]);
			$('#agent_name').val(names[1]);
            $('#agent_tid').val(names[2]);
		}		      	
	});
});