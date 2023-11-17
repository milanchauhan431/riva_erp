<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>       
                            <div class="col-md-3">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>               
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th rowspan="2" class="text-center">#</th>
										<th rowspan="2" class="text-left">Item Code</th>
										<th rowspan="2" class="text-left">Item Description</th>
										<th colspan="3" class="text-center">Opening</th>
										<th colspan="3" class="text-center">IN</th>
										<th colspan="3" class="text-center">OUT</th>
										<th colspan="3" class="text-center">Closing</th>
									</tr>
                                    <tr>
                                        <th class="text-right">Qty.</th>
										<th class="text-right">G.W.</th>
										<th class="text-right">N.W.</th>

                                        <th class="text-right">Qty.</th>
										<th class="text-right">G.W.</th>
										<th class="text-right">N.W.</th>

                                        <th class="text-right">Qty.</th>
										<th class="text-right">G.W.</th>
										<th class="text-right">N.W.</th>

                                        <th class="text-right">Qty.</th>
										<th class="text-right">G.W.</th>
										<th class="text-right">N.W.</th>
                                    </tr>
								</thead>
								<tbody id="tbodyData"></tbody>
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
$(document).ready(function(){
	reportTable();
    setTimeout(function(){$(".loadData").trigger('click');},500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
        if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
        if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid){
            $.ajax({
                url: base_url + controller + '/getItemHistoryData',
                data: {from_date:from_date,to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });   
});
</script>