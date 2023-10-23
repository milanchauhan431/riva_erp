var itemCount = 0;
$(document).ready(function(){
	$(".ledgerColumn").hide();
	$(".summary_desc").attr('style','width: 60%;');
	$("#invItemLink").hide();

	$(document).on('click','.getInvoiceItem',function(){
		var ref_id = $('#ref_id').val();
		var party_name = $('#party_id :selected').text();
		$('.doc_no').html("");

		if (ref_id != "" || ref_id != 0) {
			$.ajax({
				url: base_url + 'salesInvoice/getPartyInvoiceItems',
				type: 'post',
				data: { id : ref_id },
				success: function (response) {
					$("#modal-xl").modal();
					$('#modal-xl .modal-body').html('');
					$('#modal-xl .modal-title').html("Carete Invoice [ Party Name : "+party_name+" ]");
					$('#modal-xl .modal-body').html(response);
					$('#modal-xl .modal-body form').attr('id',"createCreditNoteForm");
					$('#modal-xl .modal-footer .btn-save').html('<i class="fa fa-check"></i> Create Invoice');
					$("#modal-xl .modal-footer .btn-save").attr('onclick',"createInvoice();");
				}
			});
		} else {
			$('.doc_no').html("Inv. No. is required.");
		}	
	});

    $(document).on("change",'#order_type',function(){
        var order_type = $(this).val();
		$.ajax({ 
            type: "post",   
            url: base_url + controller + '/getCreditNoteTypes',   
            data: {order_type:order_type},
			dataType: 'json',
        }).done(function(response){
			$("#inv_type").val("");
			$("#inv_type").val(response.inv_type);
			$("#sp_acc_id").html("");
			$("#sp_acc_id").html(response.accountOptions);
			$("#sp_acc_id").select2();

			gstin();
        });

        $.ajax({ 
            type: "post",   
            url: base_url + controller + '/getAccountSummaryHtml',   
            data: {order_type:order_type}
        }).done(function(response){
            $("#taxSummaryHtml").html("");
            $("#taxSummaryHtml").html(response);
            $("#taxSummaryHtml .select2").select2();

            var gst_type = $("#gst_type").val();
            if (gst_type == 1) {
                $(".cgstCol").show(); $(".sgstCol").show(); $(".igstCol").hide();
                $(".amountCol").hide(); $(".netAmtCol").show();
            } else if (gst_type == 2) {
                $(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").show();
                $(".amountCol").hide(); $(".netAmtCol").show();
            } else {
                $(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").hide();
                $(".amountCol").show(); $(".netAmtCol").hide();
            }
            claculateColumn();

			$(".ledgerColumn").hide();
			$(".summary_desc").attr('style','width: 60%;');
        });

		

        if(order_type == "Sales Return"){
            $('#itemForm #stock_eff').val("1");
        }else{
            $('#itemForm #stock_eff').val("0");
        }

		
    });

    $(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
		
        $("#itemForm .error").html();

        if($('#order_type').val() == "Sales Return"){
            $('#itemForm #stock_eff').val("1");
        }else{
            $('#itemForm #stock_eff').val("0");
        }

		var party_id = $('#party_id').val();
		$(".party_id").html("");
		$("#itemForm #row_index").val("");
		if(party_id){
			setPlaceHolder();
			$("#itemModel").modal();
			$("#itemModel .btn-close").show();
			$("#itemModel .btn-save").show();	
            $("#itemForm .select2").select2();
			setTimeout(function(){ $("#itemForm #item_id").focus(); },500);
		}else{ 
            $(".party_id").html("Party name is required."); $("#itemModel").modal('hide'); 
        }
	});

    $(document).on('click', '.saveItem', function () {
        
		var fd = $('#itemForm').serializeArray();
		var formData = {};
		$.each(fd, function (i, v) {
			formData[v.name] = v.value;
		});
        $("#itemForm .error").html("");

        if (formData.item_id == "") {
			$(".item_id").html("Item Name is required.");
		}
        if (formData.qty == "" || parseFloat(formData.qty) == 0) {
            $(".qty").html("Qty is required.");
        }
        if (formData.price == "" || parseFloat(formData.price) == 0) {
            $(".price").html("Price is required.");
        }

		if(formData.gross_weight == "" || parseInt(formData.gross_weight) == 0){
			$(".gross_weight").html("Gross Weight is required.");
		}

		if(formData.net_weight == "" || parseInt(formData.net_weight) == 0){
			$(".net_weight").html("Net Weight is required.");
		}

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
            formData.disc_per = (parseFloat(formData.disc_per) > 0)?formData.disc_per:0;
            var amount = 0; var taxable_amount = 0; var disc_amt = 0; var igst_amt = 0;
            var gst_amount = 0; var cgst_amt = 0; var sgst_amt = 0; var net_amount = 0; 
            var gst_per = 0; var cgst_per = 0; var sgst_per = 0; var igst_per = 0;
			var mackingChargeAmt = 0;var mcDiscAmt = 0;var otherChargeAmt = 0; 
			var varietyChargeAmt = 0; var diamondAmount = 0; var gold_platinum_price = 0;

			amount = parseFloat(parseFloat(formData.net_weight) * parseFloat(formData.price)).toFixed(2);		

			mackingChargeAmt = parseFloat((parseFloat(amount) * parseFloat(formData.making_per))/100).toFixed(2);
			if (formData.making_disc_per != "" && formData.making_disc_per != "0") {
				mcDiscAmt = parseFloat((parseFloat(mackingChargeAmt) * parseFloat(formData.making_disc_per)) / 100).toFixed(2);
			}

			otherChargeAmt = parseFloat(formData.other_charge).toFixed(2);
			varietyChargeAmt = parseFloat(formData.vrc_charge).toFixed(2);
			diamondAmount = parseFloat(formData.diamond_amount).toFixed(2);

			if(parseFloat(formData.gold_platinum_price) > 0){
				gold_platinum_price = parseFloat(formData.gold_platinum_price).toFixed(2);
			}		

			taxable_amount = parseFloat(parseFloat(amount) + (parseFloat(mackingChargeAmt) - parseFloat(mcDiscAmt)) + parseFloat(otherChargeAmt) + parseFloat(varietyChargeAmt) + parseFloat(diamondAmount) + parseFloat(gold_platinum_price)).toFixed(2);

			if(formData.disc_amount != "" && parseFloat(formData.disc_amount) > 0){
				taxable_amount = parseFloat(parseFloat(taxable_amount) - parseFloat(formData.disc_amount)).toFixed(2);
			}
			
			formData.making_charge = mackingChargeAmt;
			formData.making_charge_dicount = mcDiscAmt;
			formData.other_charge = otherChargeAmt;
			formData.vrc_charge = varietyChargeAmt;
			formData.diamond_amount = diamondAmount;

            formData.gst_per = igst_per = parseFloat(formData.gst_per).toFixed(0);
            formData.gst_amount = igst_amt = parseFloat((igst_per * taxable_amount) / 100).toFixed(2);

            cgst_per = parseFloat(parseFloat(igst_per) / 2).toFixed(2);
            sgst_per = parseFloat(parseFloat(igst_per) / 2).toFixed(2);

            cgst_amt = parseFloat((cgst_per * taxable_amount) / 100).toFixed(2);
            sgst_amt = parseFloat((sgst_per * taxable_amount) / 100).toFixed(2);

            net_amount = parseFloat(parseFloat(taxable_amount) + parseFloat(igst_amt)).toFixed(2);

            formData.qty = parseFloat(formData.qty).toFixed(2);
            formData.cgst_per = cgst_per;
            formData.cgst_amount = cgst_amt;
            formData.sgst_per = sgst_per;
            formData.sgst_amount = sgst_amt;
            formData.igst_per = igst_per;
            formData.igst_amount = igst_amt;
            formData.disc_amount = disc_amt;
            formData.amount = amount;
            formData.taxable_amount = taxable_amount;
            formData.net_amount = net_amount;

            AddRow(formData);
            $('#itemForm')[0].reset();
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
			if($('#order_type').val() == "Sales Return"){
				$('#itemForm #stock_eff').val("1");
			}else{
				$('#itemForm #stock_eff').val("0");
			}
            $("#itemForm .select2").select2();
            if ($(this).data('fn') == "save") {
                $("#item_idc").focus();
            } else if ($(this).data('fn') == "save_close") {
                $("#itemModel").modal('hide');
            }

        }
	});

    $(document).on('click', '.btn-close', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('')
		$('#itemForm #row_index').val("");
		$("#itemForm .error").html("");
        $("#itemForm .select2").select2();
	});   

	$(document).on('change','#unit_id',function(){
		$("#unit_name").val("");
		if($(this).val()){ $("#unit_name").val($("#unit_id :selected").data('unit')); }
	});

	$(document).on('change','#hsn_code',function(){
		$("#gst_per").val(($("#hsn_code :selected").data('gst_per') || 0));
		$("#gst_per").select2();
	});

	$('#doc_no').typeahead({
		source: function(query, result){
			$.ajax({
				url:base_url + controller + '/getPartyInvoiceList',
				method:"POST",
				global:false,
				data:{doc_no:query,party_id:$("#party_id :selected").val(),order_type:$("#order_type :selected").val()},
				dataType:"json",
				success:function(data){
					result($.map(data, function(row){return {name:row.trans_number,id:row.id,doc_date:row.trans_date,entry_type:row.entry_type};}));
					$("#saveCreditNote #doc_date").val("");
					$("#saveCreditNote #ref_id").val("");
					$("#saveCreditNote #from_entry_type").val("");
					$("#invItemLink").hide();
				}
			});
		},
		updater: function(item) {
            $("#saveCreditNote #doc_date").val(item.doc_date || "");
			$("#saveCreditNote #ref_id").val(item.id || "");
			$("#saveCreditNote #from_entry_type").val(item.entry_type || "");
			$("#invItemLink").show();
			return item;
        }
	});
});

function createInvoice(){	
	$(".orderItem:checked").map(function() {
		row = $(this).data('row');
		row.qty = row.pending_qty;
		row.gst_per = parseFloat(row.gst_per);
		row.org_price = row.price;
		
		AddRow(row);
	}).get();

	$("#modal-xl").modal('hide');
	$('#modal-xl .modal-body').html('');
}

function AddRow(data) {
    var tblName = "creditNoteItems";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

	//Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}
	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

    var idInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][id]", value: data.id });
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_id]", class:"item_id", value: data.item_id });
	var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_name]", value: data.item_name });
    var formEnteryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][from_entry_type]", value: data.from_entry_type });
	var refIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][ref_id]", value: data.ref_id });
    var itemCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_code]", value: data.item_code });
    var itemtypeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_type]", value: data.item_type });
	var stockEffInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][stock_eff]", value: data.stock_eff });
    var pormInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][p_or_m]", value: 1 });

	var stockTransIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][masterData][i_col_2]", value: data.stock_trans_id });
	var locationInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][masterData][i_col_1]", value: data.location_id });
	var uniqueIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][masterData][t_col_1]", value: data.unique_id });
	var standardQtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][masterData][d_col_1]", value: data.standard_qty });
	var purityInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][masterData][d_col_2]", value: data.purity });
	var stockCategoryInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][masterData][t_col_2]", value: data.stock_category });

    cell = $(row.insertCell(-1));
    cell.html(data.item_name + ((parseFloat(data.gold_platinum_price) > 0)?"<br><small>Gold Amount : "+data.gold_platinum_price + "</small>":""));
    cell.append(idInput);
    cell.append(itemIdInput);
    cell.append(itemNameInput);
    cell.append(formEnteryTypeInput);
    cell.append(refIdInput);
    cell.append(itemCodeInput);
    cell.append(itemtypeInput);
	cell.append(stockEffInput);
    cell.append(pormInput);

	cell.append(stockTransIdInput);
    cell.append(locationInput);
    cell.append(uniqueIdInput);
    cell.append(standardQtyInput);
    cell.append(purityInput);
	cell.append(stockCategoryInput);

    var hsnCodeInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][hsn_code]", value: data.hsn_code });
	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][qty]", class:"item_qty", value: data.qty });
    var psInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][packing_qty]", value: data.packing_qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(psInput);
	cell.append(qtyErrorDiv);

	var grossWeightInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gross_weight]", value: data.gross_weight });
	cell = $(row.insertCell(-1));
	cell.html(data.gross_weight);
	cell.append(grossWeightInput);

	var netWeightInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_weight]", value: data.net_weight });
	cell = $(row.insertCell(-1));
	cell.html(data.net_weight);
	cell.append(netWeightInput);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_id]", value: data.unit_id });
	var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][unit_name]", value: data.unit_name });
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	cell.append(unitNameInput);

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][price]", value: data.price});
    var orgPriceInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][org_price]", value: data.org_price});
	var priceErrorDiv = $("<div></div>", { class: "error price" + itemCount });
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(orgPriceInput);
	cell.append(priceErrorDiv);

	var mcPerInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][making_per]", value: data.making_per });
	var mcDiscPerInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][making_disc_per]", value: data.making_disc_per });
	var makingChrageInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][making_charge]", value: data.making_charge });
	var makingChrageDiscountInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][making_charge_dicount]", value: data.making_charge_dicount });
	var otherChrageInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][other_charge]", value: data.other_charge });
	var vrChrageInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][vrc_charge]", value: data.vrc_charge });
	var diamondAmtInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][diamond_amount]", value: data.diamond_amount });
	var gpAmtInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][gold_platinum_price]", value: data.gold_platinum_price });
 	var gpWgInput = $("<input/>", { type: "hidden", name: "itemData[" + itemCount + "][gold_weight]", value: data.gold_weight });
	var tmcAmt = parseFloat(parseFloat(data.making_charge) - parseFloat(data.making_charge_dicount)).toFixed(2); 
	cell = $(row.insertCell(-1));
	cell.html(tmcAmt);
	cell.append(mcPerInput);
	cell.append(mcDiscPerInput);
	cell.append(makingChrageInput);
	cell.append(makingChrageDiscountInput);
	cell.append(otherChrageInput);
	cell.append(vrChrageInput);
	cell.append(diamondAmtInput);
	cell.append(gpAmtInput);
	cell.append(gpWgInput);

    var discPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_per]", value: data.disc_per});
	var discAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][disc_amount]", value: data.disc_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.disc_amount);
	cell.append(discPerInput);
	cell.append(discAmtInput);

    var cgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_per]", value: data.cgst_per });
	var cgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][cgst_amount]", class:'cgst_amount', value: data.cgst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.cgst_amount + '(' + data.cgst_per + '%)');
	cell.append(cgstPerInput);
	cell.append(cgstAmtInput);
	cell.attr("class", "cgstCol");

	var sgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_per]", value: data.sgst_per });
	var sgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][sgst_amount]", class:"sgst_amount", value: data.sgst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.sgst_amount + '(' + data.sgst_per + '%)');
	cell.append(sgstPerInput);
	cell.append(sgstAmtInput);
	cell.attr("class", "sgstCol");

	var gstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_per]", class:"gst_per", value: data.gst_per });
	var igstPerInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_per]", value: data.igst_per });
	var gstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][gst_amount]", class:"gst_amount", value: data.gst_amount });
	var igstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][igst_amount]", class:"igst_amount", value: data.igst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.igst_amount + '(' + data.igst_per + '%)');
	cell.append(gstPerInput);
	cell.append(igstPerInput);
	cell.append(gstAmtInput);
	cell.append(igstAmtInput);
	cell.attr("class", "igstCol");

    var amountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][amount]", class:"amount", value: data.amount });
    var taxableAmountInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][taxable_amount]", class:"taxable_amount", value: data.taxable_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.taxable_amount);
	cell.append(amountInput);
	cell.append(taxableAmountInput);
	cell.attr("class", "amountCol");

	var netAmtInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][net_amount]", value: data.net_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
	cell.append(netAmtInput);
	cell.attr("class", "netAmtCol");

    var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+itemCount+"][item_remark]", value: data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");

	var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-sm btn-outline-warning waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");

    var gst_type = $("#gst_type").val();
	if (gst_type == 1) {
		$(".cgstCol").show(); $(".sgstCol").show(); $(".igstCol").hide();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else if (gst_type == 2) {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").show();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").hide();
		$(".amountCol").show(); $(".netAmtCol").hide();
	}

    claculateColumn();
	itemCount++;
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
	//$("#itemModel .btn-close").hide();
	$("#itemModel .btn-save").hide();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});

	$("#itemForm .select2").select2();
	$("#itemForm #row_index").val(row_index);
}

function Remove(button) {
    var tableId = "creditNoteItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="15" align="center">No data available in table</td></tr>');
	}

	claculateColumn();
}

function resPartyDetail(response = ""){
    var html = '<option value="">Select GST No.</option>';
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);

        var gstDetails = response.data.gstDetails; var i = 1;
        $.each(gstDetails,function(index,row){  
			if(row.gstin !=""){
				html += '<option value="'+row.gstin+'" '+((i==1)?"selected":"")+'>'+row.gstin+'</option>';
				i++;
			}            
        });        
    }else{
        $("#party_name").val("");
    }
    //html += '<option value="URP">URP</option>';
    $("#gstin").html(html);$("#gstin").select2();gstin();
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
        $("#itemForm #unit_id").val(itemDetail.unit_id);$("#itemForm #unit_id").select2();
        $("#itemForm #unit_name").val(itemDetail.unit_name);
		$("#itemForm #disc_per").val(itemDetail.defualt_disc);
		$("#itemForm #price").val(itemDetail.price);
		$("#itemForm #org_price").val(itemDetail.price);
		$("#itemForm #packing_qty").val(itemDetail.packing_standard);
        $("#itemForm #hsn_code").val(itemDetail.hsn_code);$("#itemForm #hsn_code").select2();
        $("#itemForm #gst_per").val(parseFloat(itemDetail.gst_per).toFixed(0));$("#itemForm #gst_per").select2();
    }else{
        $("#itemForm #item_code").val("");
        $("#itemForm #item_name").val("");
        $("#itemForm #item_type").val("");
        $("#itemForm #unit_id").val("");$("#itemForm #unit_id").select2();
        $("#itemForm #unit_name").val("");
		$("#itemForm #disc_per").val("");
		$("#itemForm #price").val("");
		$("#itemForm #org_price").val("");
		$("#itemForm #packing_qty").val("");
        $("#itemForm #hsn_code").val("");$("#itemForm #hsn_code").select2();
        $("#itemForm #gst_per").val(0);$("#itemForm #gst_per").select2(); 
    }
}

function resCreditNote(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

        window.location = base_url + controller;
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }			
    }	
}