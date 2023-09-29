<form>
    <div class="col-md-12">

        <div class="hiddenInputs">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>">
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>">
        </div>

        <div class="row">
            <div class="col-md-2 form-group">
                <label for="trans_number">Inward No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:$trans_number?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_date">Inward Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control fyDates req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="party_id">Party Name</label>
                <div class="float-right">                    
                    <span class="dropdown float-right m-r-5">
                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">

                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                            
                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty" data-controller="parties" data-postdata='{"party_category" : 1 }' data-res_function="resPartyMaster" data-js_store_fn="customStore" data-form_title="Add Customer">+ Customer</a>

                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty" data-controller="parties" data-postdata='{"party_category" : 2 }' data-res_function="resPartyMaster" data-js_store_fn="customStore" data-form_title="Add Supplier">+ Supplier</a>

                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty" data-controller="parties" data-postdata='{"party_category" : 3 }' data-res_function="resPartyMaster" data-js_store_fn="customStore" data-form_title="Add Vendor">+ Vendor</a>
                            
                        </div>
                    </span>
                </div>

                <select name="party_id" id="party_id" class="form-control select2 partyOptions req" data-res_function="resPartyDetail" data-party_category="1,2,3">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                </select>

            </div>

            <div class="col-md-4 form-group">
                <label for="item_id">Product Name</label>
                <div class="float-right">                    
                    <span class="dropdown float-right m-r-5">
                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">

                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                            
                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addItem" data-controller="items" data-postdata='{"item_type" : 1 }' data-res_function="resItemMaster" data-js_store_fn="customStore" data-form_title="Add Product">+ Product</a>
                            
                        </div>
                    </span>
                </div>

                <select name="item_id" id="item_id" class="form-control select2 itemDetails itemOptions req" data-res_function="resItemDetail">
                    <option value="">Select Product Name</option>
                    <?=getItemListOption($itemList,((!empty($dataRow->item_id))?$dataRow->item_id:0))?>
                </select>
                <input type="hidden" name="item_name" id="item_name" class="form-control" value="" />
            </div>

            <div class="col-md-2 form-group">
                <label for="inward_type">Inward Type</label>
                <select name="inward_type" id="inward_type" class="form-control select2 req">
                    <option value="">Select Type</option>
                    <?php
                        foreach($this->inwardTypes as $row):
                            $selected = (!empty($dataRow->inward_type) && $dataRow->inward_type == $row)?"selected":"";
                            echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="design_no">Design No.</label>
                <input type="text" name="design_no" id="design_no" class="form-control" value="<?=(!empty($dataRow->design_no))?$dataRow->design_no:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="qty">Qty.</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="gross_weight">Gross Weight</label>
                <input type="text" name="gross_weight" id="gross_weight" class="form-control floatOnly" value="<?=(!empty($dataRow->gross_weight))?$dataRow->gross_weight:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="net_weight">Net Weight</label>
                <input type="text" name="net_weight" id="net_weight" class="form-control floatOnly" value="<?=(!empty($dataRow->net_weight))?$dataRow->net_weight:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="purchase_price">Purchase Price</label>
                <input type="text" name="purchase_price" id="purchase_price" class="form-control floatOnly req" value="<?=(!empty($dataRow->purchase_price))?$dataRow->purchase_price:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="sales_price">Sales Price</label>
                <input type="text" name="sales_price" id="sales_price" class="form-control floatOnly" value="<?=(!empty($dataRow->sales_price))?$dataRow->sales_price:""?>">
            </div>

            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12 form-group">
                <h4>Item Details : </h4>
            </div>

            <div class="col-md-2 form-group">
                <label for="purity_id">Purity</label>
                <div class="float-right">                    
                    <span class="dropdown float-right m-r-5">
                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">

                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                            
                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-md" data-function="addPurity" data-controller="purity" data-res_function="resPurityMaster" data-js_store_fn="customStore" data-form_title="Add Purity">+ Purity</a>
                            
                        </div>
                    </span>
                </div>

                <select name="purity_id" id="purity_id" class="form-control select2 purityOptions">
                    <option value="">Select Purity</option>
                    <?=getPurityListOptions($purityList,((!empty($dataRow->purity_id))?$dataRow->purity_id:0))?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="fine_id">Fine</label>
                <div class="float-right">                    
                    <span class="dropdown float-right m-r-5">
                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">

                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                            
                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-md" data-function="addFine" data-controller="fine" data-res_function="resFineMaster" data-js_store_fn="customStore" data-form_title="Add Fine">+ Fine</a>
                            
                        </div>
                    </span>
                </div>

                <select name="fine_id" id="fine_id" class="form-control select2 fineOptions">
                    <option value="">Select Fine</option>
                    <?=getFineListOptions($fineList,((!empty($dataRow->fine_id))?$dataRow->fine_id:0))?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="polish_id">Polish</label>
                <div class="float-right">                    
                    <span class="dropdown float-right m-r-5">
                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">

                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                            
                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-md" data-function="addPolish" data-controller="polish" data-res_function="resPolishMaster" data-js_store_fn="customStore" data-form_title="Add Polish">+ Polish</a>
                            
                        </div>
                    </span>
                </div>

                <select name="polish_id" id="polish_id" class="form-control select2 polishOptions">
                    <option value="">Select Polish</option>
                    <?=getPolishListOptions($polishList,((!empty($dataRow->polish_id))?$dataRow->polish_id:0))?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="color_id">Color</label>
                <div class="float-right">                    
                    <span class="dropdown float-right m-r-5">
                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">

                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                            
                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-md" data-function="addColor" data-controller="color" data-res_function="resColorMaster" data-js_store_fn="customStore" data-form_title="Add Color">+ Color</a>
                            
                        </div>
                    </span>
                </div>

                <select name="color_id" id="color_id" class="form-control select2 colorOptions">
                    <option value="">Select Color</option>
                    <?=getColorListOptions($colorList,((!empty($dataRow->color_id))?$dataRow->color_id:0))?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="clarity_id">Clarity</label>
                <div class="float-right">                    
                    <span class="dropdown float-right m-r-5">
                        <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                        <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">

                            <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                            
                            <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-md" data-function="addClarity" data-controller="clarity" data-res_function="resClarityMaster" data-js_store_fn="customStore" data-form_title="Add Clarity">+ Clarity</a>
                            
                        </div>
                    </span>
                </div>

                <select name="clarity_id" id="clarity_id" class="form-control select2 clarityOptions">
                    <option value="">Select Clarity</option>
                    <?=getColorListOptions($clarityList,((!empty($dataRow->clarity_id))?$dataRow->clarity_id:0))?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="diamond_weight">Diamond Weight</label>
                <input type="text" name="diamond_weight" id="diamond_weight" class="form-control floatOnly" value="<?=(!empty($dataRow->diamond_weight))?$dataRow->diamond_weight:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="diamond_carat">Diamond Carat</label>
                <input type="text" name="diamond_carat" id="diamond_carat" class="form-control" value="<?=(!empty($dataRow->diamond_carat))?$dataRow->diamond_carat:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="diamond_pcs">Diamond Pcs.</label>
                <input type="text" name="diamond_pcs" id="diamond_pcs" class="form-control numericOnly" value="<?=(!empty($dataRow->diamond_pcs))?$dataRow->diamond_pcs:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="diamond_size_cut">Diamond Size Cut</label>
                <input type="text" name="diamond_size_cut" id="diamond_size_cut" class="form-control" value="<?=(!empty($dataRow->diamond_size_cut))?$dataRow->diamond_size_cut:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="kundan_weight">Kundan Weight</label>
                <input type="text" name="kundan_weight" id="kundan_weight" class="form-control floatOnly" value="<?=(!empty($dataRow->kundan_weight))?$dataRow->kundan_weight:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="stone_weight">Stone Weight</label>
                <input type="text" name="stone_weight" id="stone_weight" class="form-control floatOnly" value="<?=(!empty($dataRow->stone_weight))?$dataRow->stone_weight:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="moti_weight">Moti Weight</label>
                <input type="text" name="moti_weight" id="moti_weight" class="form-control floatOnly" value="<?=(!empty($dataRow->moti_weight))?$dataRow->moti_weight:""?>">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12 form-group">
                <h4>Charges : </h4>
            </div>

            <div class="col-md-3 form-group">
                <label for="macking_charge">Macking Charge</label>
                <input type="text" name="macking_charge" id="macking_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->macking_charge))?$dataRow->macking_charge:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="variety_charge">Variety Charge</label>
                <input type="text" name="variety_charge" id="variety_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->variety_charge))?$dataRow->variety_charge:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="labor_charge">Labor Charge</label>
                <input type="text" name="labor_charge" id="labor_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->labor_charge))?$dataRow->labor_charge:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="other_charge">Other Charge</label>
                <input type="text" name="other_charge" id="other_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->other_charge))?$dataRow->other_charge:""?>">
            </div>
        </div>
    </div>
</form>

<script>
function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_name").val(itemDetail.item_name);
    }else{
        $("#itemForm #item_name").val("");
    }
}
</script>