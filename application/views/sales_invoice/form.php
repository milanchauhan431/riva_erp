<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Sales Invoice</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveSalesInvoice" data-res_function="resSaveInvoice" enctype="multipart/form-data">
                            <div class="col-md-12">
                                <div class="row">

                                    <div class="hiddenInput">
                                        <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                        <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">
                                        <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:((!empty($from_entry_type))?$from_entry_type:"")?>">
                                        <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:((!empty($ref_id))?$ref_id:"")?>">

                                        

                                        <input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>">
                                        <input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:""?>">
                                        <input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:""?>">

                                        <input type="hidden" name="tax_class" id="tax_class" value="<?=(!empty($dataRow->tax_class))?$dataRow->tax_class:""?>">
                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label for="trans_number">Inv. No.</label>
                                        <div class="input-group">
                                            <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:((!empty($trans_prefix))?$trans_prefix:"")?>">
                                            <input type="text" name="trans_no" id="trans_no" class="form-control numericOnly" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:((!empty($trans_no))?$trans_no:"")?>">
                                        </div>
                                        <input type="hidden" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:((!empty($trans_number))?$trans_number:"")?>" readonly>
                                        <div class="error trans_number"></div>
                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label for="trans_date">Inv. Date</label>
                                        <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
                                    </div>

                                    <div class="col-md-5 form-group">
                                        <label for="party_id">Customer Name</label>
                                        <div class="float-right">	
                                            
											<span class="dropdown float-right m-r-5">
												<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

												<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
													<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
													
													<a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty" data-controller="parties" data-postdata='{"party_category" : 1 }' data-res_function="resPartyMaster" data-js_store_fn="customStore" data-form_title="Add Customer">+ Customer</a>
													
												</div>
											</span>

                                            <span class="dropdown float-right m-r-10">
                                                <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Create Invoice</a>

                                                <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
													<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>

                                                    <a class="dropdown-item getPendingQuotation" href="javascript:void(0)">+ Sales Quotation</a>

                                                    <a class="dropdown-item getPendingOrders" href="javascript:void(0)">+ Sales Order</a>

                                                    <a class="dropdown-item getPendingPI" href="javascript:void(0)">+ Proforma Invoice</a>

                                                    <a class="dropdown-item getPendingChallan" href="javascript:void(0)">+ Delivery Challan</a>
                                                </div>
                                            </span>
										</div>
                                        <select name="party_id" id="party_id" class="form-control select2 partyDetails partyOptions req" data-res_function="resPartyDetail" data-party_category="1">
											<option value="">Select Party</option>
											<?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
										</select>

                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="gstin">GST NO.</label>
                                        <select name="gstin" id="gstin" class="form-control select2">
                                            <option value="">Select GST No.</option>
                                            <?php
                                                if(!empty($dataRow->party_id)):
                                                    foreach($gstinList as $row):
                                                        $selected = ($dataRow->gstin == $row->gstin)?"selected":"";
                                                        echo '<option value="'.$row->gstin.'" '.$selected.'>'.$row->gstin.'</option>';
                                                    endforeach;
                                                endif;
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2 form-group">
										<label for="memo_type">Memo Type</label>
										<select name="memo_type" id="memo_type" class="form-control">
											<option value="DEBIT" <?=(!empty($dataRow->memo_type) && $dataRow->memo_type == "DEBIT")?"selected":""?> >Debit</option>
											<option value="CASH" <?=(!empty($dataRow->memo_type) && $dataRow->memo_type == "CASH")?"selected":""?> >Cash</option>
										</select>
									</div>

                                    <div class="col-md-3 form-group">
										<label for="sp_acc_id">GST Type </label>
                                        <select name="sp_acc_id" id="sp_acc_id" class="form-control select2 req">
											<?=getSpAccListOption($salesAccounts,((!empty($dataRow->sp_acc_id))?$dataRow->sp_acc_id:0))?>
										</select>
                                        <input type="hidden" id="inv_type" value="SALES">
									</div>

                                    <div class="col-md-2 form-group">
										<label for="challan_no">Challan No.</label>
										<input type="text" name="challan_no" class="form-control" placeholder="Enter Challan No." value="<?= (!empty($dataRow->challan_no)) ? $dataRow->challan_no : "" ?>" />
									</div>

                                    <div class="col-md-2 form-group">
                                        <label for="doc_no">PO. No.</label>
                                        <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>">
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="doc_date">PO. Date</label>
                                        <input type="date" name="doc_date" id="doc_date" class="form-control" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:getFyDate()?>">
                                    </div>

                                    <div class="col-md-2 form-group hidden">
										<label for="apply_round">Apply Round Off</label>
                                        <select name="apply_round" id="apply_round" class="form-control">
											<option value="1" <?= (!empty($dataRow) && $dataRow->apply_round == 1) ? "selected" : "" ?>>Yes</option>
											<option value="0" <?= (!empty($dataRow) && $dataRow->apply_round == 0) ? "selected" : "" ?>>No</option>
										</select>
                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label for="sales_executive">Sales Executive</label>
                                        <select name="sales_executive" id="sales_executive" class="form-control select2" >
                                            <option value="">Sales Executive</option>
                                            <?php
                                                foreach($salesExecutives as $row):
                                                    $selected = (!empty($dataRow->sales_executive) && $dataRow->sales_executive == $row->id)?"selected":"";
                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2 form-group">
                                        <label for="master_t_col_1">Contact Person</label>
                                        <input type="text" name="masterDetails[t_col_1]" id="master_t_col_1" class="form-control" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>">
                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label for="master_t_col_2">Contact No.</label>
                                        <input type="text" name="masterDetails[t_col_2]" id="master_t_col_2" class="form-control numericOnly" value="<?=(!empty($dataRow->contact_no))?$dataRow->contact_no:""?>">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="master_t_col_3">Address</label>
                                        <input type="text" name="masterDetails[t_col_3]" id="master_t_col_3" class="form-control" value="<?=(!empty($dataRow->ship_address))?$dataRow->ship_address:""?>">
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="master_t_col_4">PAN NO.</label>
                                        <input type="text" name="masterDetails[t_col_4]" id="master_t_col_4" class="form-control" value="<?=(!empty($dataRow->pan_no))?$dataRow->pan_no:""?>">
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="master_t_col_5">Aadhar NO.</label>
                                        <input type="text" name="masterDetails[t_col_5]" id="master_t_col_5" class="form-control" value="<?=(!empty($dataRow->aadhar_no))?$dataRow->aadhar_no:""?>">
                                    </div>

                                    <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                        <label for="port_code">Port Code</label>
                                        <input type="text" name="port_code" id="port_code" class="form-control" value="<?=(!empty($dataRow->port_code))?$dataRow->port_code:""?>">
                                    </div>

                                    <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                        <label for="ship_bill_no">Shipping Bill No.</label>
                                        <input type="text" name="ship_bill_no" id="ship_bill_no" class="form-control" value="<?=(!empty($dataRow->ship_bill_no))?$dataRow->ship_bill_no:""?>">
                                    </div>

                                    <div class="col-md-2 form-group exportData <?=(empty($dataRow))?"hidden":((!empty($dataRow->tax_class) && !in_array($dataRow->tax_class,["EXPORTGSTACC","EXPORTTFACC"]))?"hidden":"")?>">
                                        <label for="ship_bill_date">Shipping Bill Date</label>
                                        <input type="date" name="ship_bill_date" id="ship_bill_date" class="form-control" value="<?=(!empty($dataRow->ship_bill_date))?$dataRow->ship_bill_date:""?>">
                                    </div>
                                </div>

                                <hr>

                                <div class="col-md-12 row">
                                    <div class="col-md-6"><h4>Item Details : </h4></div>
                                    <div class="col-md-4">
                                        <div class="input-group">
										    <input type="text" id="barcode_scanner" class="form-control numericOnly float-right" value="" placeholder="Scan barcode" data-stock_effect="1" data-defualt_location="<?=$this->SHOWROOM_STORE->id?>">
                                            <div class="input-group-append">
                                                <button type="button" id="barcode_search" class="btn btn-info"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="error itemData"></div>
                                    <div class="row form-group">
                                        <div class="table-responsive">
                                            <table id="salesInvoiceItems" class="table table-striped table-borderless">
                                                <thead class="thead-info">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Item Name</th>
                                                        <th>HSN Code</th>
                                                        <th>Qty.</th>
                                                        <th>G.W.</th>
                                                        <th>N.W.</th>
                                                        <th>Unit</th>
                                                        <th>Price</th>
                                                        <th>Making<br>Charge</th>
                                                        <th>Disc.</th>
                                                        <th class="igstCol">IGST</th>
                                                        <th class="cgstCol">CGST</th>
                                                        <th class="sgstCol">SGST</th>
                                                        <th class="amountCol">Amount</th>
                                                        <th class="netAmtCol">Amount</th>
                                                        <th>Remark</th>
                                                        <th class="text-center" style="width:10%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tempItem" class="temp_item">
                                                    <tr id="noData">
                                                        <td colspan="17" class="text-center">No data available in table</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>

                                <?php $this->load->view('includes/tax_summary',['expenseList'=>$expenseList,'taxList'=>$taxList,'ledgerList'=>$ledgerList,'dataRow'=>((!empty($dataRow))?$dataRow:array())])?>

                                <hr>

                                <div class="row">
                                    <div class="col-md-9 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-success waves-effect btn-block" data-toggle="modal" data-target="#termModel">Terms & Conditions (<span id="termsCounter">0</span>)</button>
                                        <div class="error term_id"></div>
                                    </div>
                                    <?php $this->load->view('includes/terms_form',['termsList'=>$termsList,'termsConditions'=>(!empty($dataRow->termsConditions)) ? $dataRow->termsConditions : array()])?>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <h4>Attechments :</h4>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="attechments">Attechments</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input multifiles" name="attachments[]" id="attachments" accept=".jpg, .jpeg, .png" multiple>
                                                <label class="custom-file-label" for="attachments">Choose file</label>
                                            </div>
                                        </div>
                                        <div class="error attachment_error"></div>
                                    </div>

                                    <div class="col-md-8"></div>

                                    <?php 
                                        if(!empty($dataRow->attachments)): 
                                            $attachments = explode(",",$dataRow->attachments);
                                            foreach($attachments as $file):
                                    ?>
                                        <div class="col-md-2 form-group text-center m-t-20">
                                            <img src="<?=base_url("assets/uploads/sales_document/".$file)?>" class="img-zoom" alt="IMG"><br>
                                            <button type="button" class="btn btn-outline-danger m-t-10 removeImage"><i class="ti-trash"></i> Remove</button>
                                            <input type="hidden" name="attachment[]" value="<?=$file?>">
                                        </div>
                                    <?php 
                                            endforeach;
                                        endif; 
                                    ?>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="customStore({'formId':'saveSalesInvoice'});" ><i class="fa fa-check"></i> Save</button>
                            <a href="javascript:void(0)" onclick="window.location.href='<?=base_url($headData->controller)?>'" class="btn waves-effect waves-light btn-outline-secondary float-right btn-close press-close-btn save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModel" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header" style="display:block;"><h4 class="modal-title">Add or Update Item</h4></div>
            <div class="modal-body">
                <form id="itemForm" enctype="multipart/form-data">
                    <div class="col-md-12">

                        <div class="row form-group">
							<div id="itemInputs">
								<input type="hidden" name="id" id="id" value="" />
								<input type="hidden" name="from_entry_type" id="from_entry_type" value="" />
                                <input type="hidden" name="ref_id" id="ref_id" value=""  />
                                
								<input type="hidden" name="row_index" id="row_index" value="">
								<input type="hidden" name="item_code" id="item_code" value="" />
                                <input type="hidden" name="item_type" id="item_type" value="1" />
                                <input type="hidden" name="stock_eff" id="stock_eff" value="1" />

                                <input type="hidden" name="org_price" id="org_price" class="org_price" value="" />
                                <input type="hidden" name="stock_trans_id" id="stock_trans_id" value="" />
                                <input type="hidden" name="location_id" id="location_id" value="" />
                                <input type="hidden" name="standard_qty" id="standard_qty" value="" />
                                <input type="hidden" name="purity" id="purity" value="" />
                                <input type="hidden" name="stock_category" id="stock_category" value="" />

                                <input type="hidden" name="disc_per" id="disc_per" value="0">
                                <input type="hidden" name="gold_weight" id="gold_weight" value="0">
                                <input type="hidden" name="color" id="color" value="">
                                <input type="hidden" name="diamond_carat" id="diamond_carat" value="">
                                <input type="hidden" name="diamond_pcs" id="diamond_pcs" value="0">
                            </div>
                            

                            <div class="col-md-12 form-group popupSelectBox">
								<label for="item_id">Product Name</label>
                                <input type="hidden" name="item_name" id="item_name" class="form-control" value="" />
                                <select name="item_id" id="item_id" class="form-control select2 itemDetails itemOptions" data-res_function="resItemDetail">
                                    <option value="">Select Product Name</option>
                                    <?=getItemListOption($itemList)?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="unique_id">Barcode No.</label>
                                <div class="input-group">
                                    <input type="text" name="unique_id" id="unique_id" class="form-control numericOnly" value="" data-defualt_location="<?=$this->SHOWROOM_STORE->id?>">
                                    <div class="input-group-append">
                                        <button type="button" id="unique_search" class="btn btn-info"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="qty">Quantity</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="disc_amount">Disc. Amount</label>
                                <input type="text" name="disc_amount" id="disc_amount" class="form-control floatOnly" value="0">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control floatOnly req" value="0" />
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="unit_id">Unit</label>        
                                <select name="unit_id" id="unit_id" class="form-control select2">
                                    <option value="">Select Unit</option>
                                    <?=getItemUnitListOption($unitList)?>
                                </select> 
                                <input type="hidden" name="unit_name" id="unit_name" class="form-control" value="" />                       
                            </div>
							<div class="col-md-3 form-group">
                                <label for="hsn_code">HSN Code</label>
                                <select name="hsn_code" id="hsn_code" class="form-control select2">
                                    <option value="">Select HSN Code</option>
                                    <?=getHsnCodeListOption($hsnList)?>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="gst_per">GST Per.(%)</label>
                                <select name="gst_per" id="gst_per" class="form-control select2">
                                    <?php
                                        foreach($this->gstPer as $per=>$text):
                                            echo '<option value="'.$per.'">'.$text.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="gross_weight">Gross Weight</label>
                                <input type="text" name="gross_weight" id="gross_weight" class="form-control floatOnly req" value="0" />
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="net_weight">Net Weight</label>
                                <input type="text" name="net_weight" id="net_weight" class="form-control floatOnly req" value="0" />
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="making_per">Making Charge (%)</label>
                                <input type="text" name="making_per" id="making_per" class="form-control floatOnly" value="0" />
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="making_disc_per">Making Charge Disc.(%)</label>
                                <input type="text" name="making_disc_per" id="making_disc_per" class="form-control floatOnly" value="0" />
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="other_charge">Other Charge</label>
                                <input type="text" name="other_charge" id="other_charge" class="form-control floatOnly" value="0" />
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="vrc_charge">Variety Charge</label>
                                <input type="text" name="vrc_charge" id="vrc_charge" class="form-control floatOnly" value="0" />
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="diamond_amount">Diamond Amount</label>
                                <input type="text" name="diamond_amount" id="diamond_amount" class="form-control floatOnly" value="0" />
                            </div>
                            <div class="col-md-3 form-group hidden" id="gold_platinum_price_div">
                                <label for="gold_platinum_price">Gold/Platinum Amount</label>
                                <input type="text" name="gold_platinum_price" id="gold_platinum_price" class="form-control floatOnly" value="0" />
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="item_remark">Remark</label>
                                <input type="text" name="item_remark" id="item_remark" class="form-control" value="" />
                            </div>                         
                        </div>						
                    </div>          
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/sales-invoice-form.js?v=<?= time() ?>"></script>
<!-- <script src="<?php echo base_url(); ?>assets/js/custom/row-attachment.js?v=<?= time() ?>"></script> -->
<script src="<?php echo base_url(); ?>assets/js/custom/calculate.js?v=<?= time() ?>"></script>

<?php
if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
        $row->gst_per = floatVal($row->gst_per);
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>