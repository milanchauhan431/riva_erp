<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>">
            <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_prefix?>">
            <input type="hidden" name="entry_type" id="entry_type" value="<?=(!empty($dataRow->entry_type))?$dataRow->entry_type:$entry_type?>">

            <div class="col-md-3 form-group">
                <label for="trans_number">Membership No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:$trans_number?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">Membership Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control req fyDates" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="party_id">Customer Name</label>
                <select name="party_id" id="party_id" class="form-control select2 partyDetails1 partyOptions1 req" data-res_function="resPartyDetail" data-party_category="1">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="ref_id">Plan Name</label>
                <select name="ref_id" id="ref_id" class="form-control select2 req">
                    <option value="">Select Plan</option>
                    <?php
                        foreach($planList as $row):
                            $selected = (!empty($dataRow->ref_id) && $dataRow->ref_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" data-month="'.$row->plan_month.'" '.$selected.'>'.$row->plan_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="credit_period">Total EMI</label>
                <input type="text" name="credit_period" id="credit_period" class="form-control calculateEmi req" value="<?=(!empty($dataRow->credit_period))?$dataRow->credit_period:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="total_amount">EMI Amount</label>
                <input type="text" name="total_amount" id="total_amount" class="form-control calculateEmi floatOnly req" value="<?=(!empty($dataRow->total_amount))?$dataRow->total_amount:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="net_amount">Total Amount</label>
                <input type="text" name="net_amount" id="net_amount" class="form-control calculateEmi floatOnly req" value="<?=(!empty($dataRow->net_amount))?$dataRow->net_amount:""?>">
            </div>

            <div class="col-md-4 form-group">
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

            <div class="col-md-8 form-group">
                <label for="remark">Note</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('change','#ref_id',function(){
        var plan_month = $(this).find(':selected').data('month') || 0;
        $("#credit_period").val(plan_month);
        $("#credit_period").trigger('change');
    });

    $(document).on('change keyup','.calculateEmi',function(){
        var input = $(this).attr('id');
        var total_emi = $("#credit_period").val() || 0;
        var emi_amount = $("#total_amount").val() || 0;
        var total_amount = $("#net_amount").val() || 0;

        if(input == "credit_period"){
            if(parseFloat(total_amount) > 0 && parseFloat(emi_amount) <= 0){
                emi_amount = parseFloat(parseFloat(total_amount) / parseFloat(total_emi)).toFixed(2);
                $("#total_amount").val(emi_amount);
            }else{
                total_amount = parseFloat(parseFloat(emi_amount) * parseFloat(total_emi)).toFixed(2);
                $("#net_amount").val(total_amount);
            }
        }

        if(input == "total_amount"){
            total_amount = parseFloat(parseFloat(emi_amount) * parseFloat(total_emi)).toFixed(2);
            $("#net_amount").val(total_amount);
        }

        if(input == "net_amount"){
            emi_amount = parseFloat(parseFloat(total_amount) / parseFloat(total_emi)).toFixed(2);
            $("#total_amount").val(emi_amount);
        }
    });
});
</script>