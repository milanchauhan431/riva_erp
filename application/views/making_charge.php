<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Update Making Charge</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="addMakingCharge">
                            <div class="col-md-12">

                                <div class="row">
                                    <table id="" class="table table-striped table-borderless">
                                        <thead class="thead-info">
                                            <tr>
                                                <th style="width:5%;">#</th>
                                                <th>Item Name</th>
                                                <th>Qty.</th>
                                                <th>G.W.</th>
                                                <th>N.W.</th>
                                                <th>Unit</th>
                                                <th>Price</th>
                                                <th>Making Charge</th>
                                                <th>Other Charge</th>
                                                <th>Making Charge Discount(%)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tempItem" class="temp_item">
                                            <?php foreach ($charge_data as $row) { ?>
                                                <tr>
                                                    <td style="width:5%;"><?php echo $row->unique_id; ?></td>
                                                    <td><?php echo $row->item_name; ?><input type="hidden" name="id[]" value="<?php echo $row->id; ?>"></td>
                                                    <td><?php echo $row->qty; ?></td>
                                                    <td><?php echo $row->gross_weight; ?></td>
                                                    <td><?php echo $row->net_weight; ?></td>
                                                    <td><?php echo $row->unit_name; ?></td>
                                                    <td><?php echo $row->purchase_price; ?></td>
                                                    <td><input type="text" name="mc_per_gm[<?php echo $row->id; ?>]" value="<?php echo $row->mc_per_gm; ?>"></td>
                                                    <td><input type="text" name="oc_per_gm[<?php echo $row->id; ?>]" value="<?php echo $row->oc_per_gm; ?>"></td>
                                                    <td><input type="text" name="mdc_per_gm[<?php echo $row->id; ?>]" value="<?php echo $row->mdc_per_gm; ?>"></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right save-form permission-write" onclick="customStore({'formId':'addMakingCharge','fnsave':'save'});"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>