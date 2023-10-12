<form>
    <div class="col-md-12">
        <input type="hidden" name="id" id="id" value="<?=$id?>" />
        <div class="row">
            <div class="table table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Barcode No.</th>
                            <th>Gross Weight</th>
                            <th>Stone Weight</th>
                            <th>Net Weight</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 0;$totalGw = $totalNw = 0;
                            foreach($issueItemList as $row):
                                $i++;

                                $checked = "checked";
                                $checkBoxInput = '<input type="checkbox" name="itemData['.$i.'][trans_status]" id="return_checkbox'.$i.'" class="filled-in chk-col-success" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' value="1" />
                                <label for="return_checkbox'.$i.'"></label>';

                                if($row->sold_status == "SOLD"):
                                    $checkBoxInput = "";
                                endif;

                                if($row->trans_status > 0):
                                    $checkBoxInput = '<input type="hidden" name="itemData['.$i.'][trans_status]" value="'.$row->trans_status.'">';
                                endif;

                                if($row->sold_status != "SOLD" && $row->trans_status == 1):
                                    $row->sold_status = "RETURN";
                                endif;

                                echo '<tr>
                                    <td style="width:10%;">
                                        '.$checkBoxInput.'
                                    </td>
                                    <td>
                                        '.$row->item_name.'
                                        <input type="hidden" name="itemData['.$i.'][id]" value="'.$row->id.'">
                                        <input type="hidden" name="itemData['.$i.'][item_remark]" value="'.$row->sold_status.'">
                                        <input type="hidden" name="itemData['.$i.'][stData][id]" value="'.$row->ref_id.'">
                                        <input type="hidden" name="itemData['.$i.'][stData][location_id]" value="'.$row->initiate_by.'">
                                    </td>
                                    <td>'.$row->item_desc.'</td>
                                    <td>'.$row->gross_weight.'</td>
                                    <td>'.($row->gross_weight - $row->net_weight).'</td>
                                    <td>'.$row->net_weight.'</td>
                                    <td>'.$row->sold_status.'</td>
                                </tr>';
                                $totalGw += $row->gross_weight;
                                $totalNw += $row->net_weight;
                                
                            endforeach;
                        ?>
                    </tbody>
                    <tfoot class="thead-info">
                        <tr>
                            <th colspan="2">Total</th>
                            <th><?=$i?></th>
                            <th><?=$totalGw?></th>
                            <th><?=($totalGw - $totalNw)?></th>
                            <th><?=$totalNw?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</form>