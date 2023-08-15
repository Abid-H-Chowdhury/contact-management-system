//adds extra table rows
/*var i=$('#table-auto tr').length;
$(".addmore1").on('click',function(){
	html = '<tr>';
	html += '<td class="avoid"><input class="case" type="checkbox" tabindex="-1"/></td>';
	html += '<input type="hidden" data-type="productCode" name="itemID['+i+'][itemid]" id="itemNo_'+i+'" class="form-control autocomplete_txt" autocomplete="off"/>';                     
    html += '<td><input type="text" data-type="productName" id="itemName_'+i+'" class="form-control autocomplete_txt" autocomplete="off"></td>';
    html += '<td><input type="text" id="stock_'+i+'" class="form-control" readonly="" tabindex="-1"></td>';
    html += '<td><input type="termxt" name="itemID['+i+'][amount]" value="" id="price_'+i+'" onkeypress="return IsNumeric(event);" class="form-control Price" readonly="" tabindex="-1" ></td>';
    html += '<input type="hidden" name="itemID['+i+'][price]" value="" id="net_price_'+i+'" onkeypress="return IsNumeric(event);" class="form-control"  >';
    html += '<td><input type="text" name="itemID['+i+'][qty]" id="qty_'+i+'" class="form-control qty" value="1" onkeypress="return IsNumeric(event);"></td>';
    html += '<td><input type="text" name="itemID['+i+'][payable]" id="paid_'+i+'" class="form-control  TotalPayable" readonly="" tabindex="-1"></td>';
	html += '</tr>';
	$('#table-auto').append(html);
	i++;
});*/

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
	
	if(type =='productCode' )autoTypeNo=0;
	if(type =='productName' )autoTypeNo=1; 	
	if(type =='ProductWithStock' )autoTypeNo=1; 	
	
	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : 'ajax-ph-invoice.php',
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
		minLength: 2,
		select: function( event, ui ) {
			var names = ui.item.data.split("|");						
			id_arr = $(this).attr('id');
	  		id = id_arr.split("_");
			$('#itemNo_'+id[1]).val(names[0]);
			$('#itemName_'+id[1]).val(names[1]);
			$('#qty_'+id[1]).val(1);
            $('#net_price_'+id[1]).val(names[2]);
			$('#price_'+id[1]).val(names[2]);
            $('#stock_'+id[1]).val(names[3]);
			$('#paid_'+id[1]).val( 1*names[2] );
			calculateTotal();
		}		      	
	});
});


//test
//autocomplete script for barcode
$(document).on('focus','.autocompletes_txt',function(){
	type = $(this).data('type');
	
	if(type =='productCodes' )autoTypeNo=0;
	if(type =='productNames' )autoTypeNo=1; 	
	
	$(this).autocomplete({
		source: function( request, response ) {
			$.ajax({
				url : 'ajax-ph-invoice.php',
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
				  		price=names[2];
				  		stock=names[3];
				  		paid=1*names[2];				  		
				  		var i=$('#table-auto tr').length;				  					  		
                        $('#itemNameSearch').val("");
                        $('#itemIdNo').val("");
                     
                       
                        var pos_table='';
                        pos_table +='<tr>';   
                        pos_table +='<td class="avoid"><div class="checkbox-wrapper"><label><input class="case" type="checkbox" tabindex="-1" /><span class="checkmark"></span></label></div></td><input type="hidden" data-type="productCode" name="itemID['+i+'][itemid]" id="itemNo_'+i+'" value="'+itemID+'" class="form-control autocomplete_txt" autocomplete="off" />';
                        pos_table +='<td class="text-left"><input type="text" data-type="productName" name="itemName[]" id="itemName_'+i+'" value="'+itemName+'" class="form-control autocomplete_txt item-name-width"  autocomplete="off"/></td>';
						pos_table +='<td><input type="text" id="stock_'+i+'" value="'+stock+'" class="form-control note-width" readonly="" tabindex="-1" /></td>';
						pos_table +='<td><input type="text" name="itemID['+i+'][amount]" id="price_'+i+'" value="'+net_price+'" onkeypress="return IsNumeric(event);" class="form-control Price amount-width"/></td>';
						pos_table +='<input type="hidden" name="itemID['+i+'][price]" value="'+net_price+'" id="net_price_'+i+'" onkeypress="return IsNumeric(event);" class="form-control" readonly="" tabindex="-1" />';
						pos_table +='<td><input type="text" name="itemID['+i+'][qty]" id="qty_'+i+'" class="form-control qty quantity-width" value="'+item_quantity+'" onkeypress="return IsNumeric(event);" /></td>';
					    pos_table +='<td><input type="text" name="itemID['+i+'][payable]" id="paid_'+i+'" value="'+paid+'" class="form-control  TotalPayable total-amount-width" readonly="" tabindex="-1"/></td>';
					    pos_table +='<td><i class="far fa-trash-alt delete"></i></td>';					
					    pos_table +='</tr>';
				
                     console.log(pos_table);					
					$(".order_holder").append(pos_table);	
					}));
				}
			});
		}		      	
	});
});

//test

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