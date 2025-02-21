<?php $this->load->view('includes/header'); ?>
<style>
.info-table tr th {
    background-color: #f9e9c9;
    border-color: #d7b56e;
}
.info-table tr td {
    border-color: #d7b56e;
}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title pageHeader text-left">EMI Statement</h4>
                            </div>       
						    <input type="hidden" id="ref_id" value="<?=(!empty($dataRow->id))?$dataRow->id:''?>" />
						    <input type="hidden" id="from_entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:''?>" />
                        </div>                                         
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered info-table">
                                <tr>
                                    <th>Customer Name</th>
                                    <td><?=$partyDetail->party_name?></td>
                                    <th>Contact No.</th>
                                    <td><?=$partyDetail->party_mobile?></td>
                                    <th>Membership No.</th>
                                    <td><?=$dataRow->trans_number?></td>
                                    <th>Membership Date</th>
                                    <td><?=formatDate($dataRow->trans_date)?></td>                                    
                                </tr>
                                <tr>                                    
                                    <th>Plan Name</th>
                                    <td><?=$dataRow->plan_name?></td>
                                    <th>No. of EMI</th>
                                    <td><?=floatval($dataRow->credit_period)?></td>
                                    <th>EMI Amount</th>
                                    <td><?=floatval($dataRow->total_amount)?></td>
                                    <th>Total Amount</th>
                                    <td><?=floatval($dataRow->net_amount)?></td>
                                </tr>
                                <tr>                                   
                                    <th>Received EMI</th>
                                    <td><?=floatval($dataRow->received_emi)?></td>
                                    <th>Received Amount</th>
                                    <td><?=floatval($dataRow->rop_amount)?></td>
                                    <th>Pending EMI</th>
                                    <td><?=floatval(($dataRow->credit_period - $dataRow->received_emi))?></td>
                                    <th>Pending Amount</th>
                                    <td><?=floatval(($dataRow->net_amount - $dataRow->rop_amount))?></td>
                                </tr>
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id='commanTableDetail' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Vou. No.</th>
										<th>Vou. Date</th>
										<th>Payment Mode</th>
										<th>Vou. Amount</th>
									</tr>
								</thead>
								<tbody id="tbodyData">
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>

var tableOption = {
    responsive: true,
    "scrollY": '52vh',
    "scrollX": true,
    deferRender: true,
    scroller: true,
    destroy: true,
    "autoWidth" : false,
    order: [],
    "columnDefs": [
        {type: 'natural',targets: 0},
        {orderable: false,targets: "_all"},
        {className: "text-center",targets: [0, 1]},
        {className: "text-center","targets": "_all"}
    ],
    pageLength: 25,
    language: {search: ""},
    lengthMenu: [
        [ 10, 20, 25, 50, 75, 100, 250,500 ],
        [ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
    ],
    dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [
        'pageLength', 
        'excel',
        { text: 'Pdf',action: function () {loadData('pdf'); } } , 
        {text: 'Refresh',action: function (){ loadData(); } }
    ],
    "fnInitComplete":function(){ $('.dataTables_scrollBody').perfectScrollbar(); },
    "fnDrawCallback": function() { $('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar(); }
};

$(document).ready(function(){
	loadData();
});

function loadData(pdf=""){
	$(".error").html("");
	var valid = 1;
	var ref_id = $('#ref_id').val();
	var from_entry_type = $('#from_entry_type').val();
    var postData = {ref_id:ref_id, from_entry_type:from_entry_type, pdf:pdf};
	if(valid){
        if(pdf == ""){
            $.ajax({
                url: base_url + controller + '/getEmiStatement',
                data: postData,
                type: "POST",
                dataType:'json',
                success:function(data){              
                    $("#commanTableDetail").DataTable().clear().destroy();
                    $("#tbodyData").html("");
                    $("#tbodyData").html(data.tbody);
                    reportTable('commanTableDetail',tableOption);

                }
            });
        }else{
			var url = base_url + controller + '/getEmiStatement/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			window.open(url);
		}
	}
}
</script>