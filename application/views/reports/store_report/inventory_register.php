<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>       
                            <div class="col-md-6 float-right">  
                                <div class="input-group">
                                    <div class="input-group-append" style="width:50%;">
                                        <select id="item_id" class="form-control select2" >
                                            <option value="">Select Item</option>
                                            <?=getItemListOption($itemList,$item_id)?>
                                        </select>
                                    </div>
                                    <div class="input-group-append" style="width:30%;">
                                        <select id="location_id" class="form-control select2" >
                                            <option value="">Select Location</option>
                                            <?=getLocationListOption($locationList,$location_id)?>
                                        </select>
                                    </div>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success refreshReportData loadData" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error item_name"></div>
                                <div class="error location"></div>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="9">Inventory Register</th>
                                    </tr>
									<tr>
										<th class="text-center">#</th>
										<th class="text-left">Item Code</th>
										<th class="text-left">Item Name</th>
										<th class="text-left">Location</th>
										<th class="text-left">Serial No.</th>
										<th class="text-right">Balance Qty.</th>
										<th class="text-right">Gross Weight</th>
										<th class="text-right">Net Weight</th>
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
    setTimeout(function(){ $(".loadData").trigger('click'); },500);
    
    $(document).on('click','.loadData',function(e){
		var item_id = $('#item_id').val();
		var location_id = $('#location_id').val();
        $.ajax({
            url: base_url + controller + '/getInventoryRegister',
            data: {item_id:item_id,location_id:location_id},
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#reportTable").DataTable().clear().destroy();
                $("#tbodyData").html(data.tbody);
                reportTable();
            }
        });
    });   
});
</script>