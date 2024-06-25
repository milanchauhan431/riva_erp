<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="payment_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />

            <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?= (!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix ?>" />
            <input type="hidden" name="trans_no" id="trans_no" value="<?= (!empty($dataRow->trans_no)) ? $dataRow->trans_no : $trans_no ?>" />
            <input type="hidden" name="vou_name_s" id="vou_name_s" value="<?= (!empty($dataRow->vou_name_s)) ? $dataRow->vou_name_s : "BCRct" ?>" />
            <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($dataRow->ref_id)) ? $dataRow->ref_id : "" ?>" />
            <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?= (!empty($dataRow->from_entry_type)) ? $dataRow->from_entry_type : "" ?>" />

            <div class="col-md-3 form-group">
                <label for="trans_no">Voucher No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?= (!empty($dataRow->trans_number)) ? $dataRow->trans_number : $trans_number ?>" readonly />
            </div>

            <div class="col-md-3 form-group">
                <label for="trans_date">Voucher Date</label>
                <input type="date" class="form-control fyDates" name="trans_date" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
            </div>

            <div class="col-md-6 form-group">
                <label>Party Name</label>
                <input type="text" name="party_name" id="party_name" class="form-control" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" readonly>

                <input type="hidden" name="opp_acc_id" id="opp_acc_id" value="<?=(!empty($dataRow->party_id))?$dataRow->party_id:""?>">
            </div>

            <div class="col-md-6 form-group">
                <label>Bank/Cash Account</label>
                <select name="vou_acc_id" id="vou_acc_id" class="form-control select2">
                <option value="">Select Ledger</option>
                    <?=getPartyListOption($ledgerList,((!empty($dataRow->vou_acc_id))?$dataRow->vou_acc_id:0))?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label>Amount</label>
                <input type="text" name="net_amount" id="net_amount" class="form-control floatOnly" value="<?= (!empty($dataRow->net_amount)) ? $dataRow->net_amount : ""; ?>">
            </div>

            <div class="col-md-3 form-group">
                <label>Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-control select2" data-selected="<?=(!empty($dataRow->payment_mode)) ? $dataRow->payment_mode:''?>">
                    <option value="">Select Payment Mode</option>
                    <?php
                        foreach($this->paymentMode as $row):
                            $selected = (!empty($dataRow->payment_mode) && $row == $dataRow->payment_mode) ? "selected":"";
                            echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label>Ref. No.</label>
                <input type="text" class="form-control" id="doc_no" name="doc_no" value="<?= (!empty($dataRow->doc_no)) ? $dataRow->doc_no : ""; ?>">
            </div>

            <div class="col-md-2 form-group">
                <label>Ref. Date</label>
                <input type="date" class="form-control" id="doc_date" name="doc_date" max="<?=getFyDate()?>" value="<?= (!empty($dataRow->doc_date)) ? $dataRow->doc_date : getFyDate(); ?>">
            </div>

            <div class="col-md-8 form-group">
                <label for="remark">Note</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : ""; ?>" readonly>
            </div>
         <div>
    </div>
</form>

