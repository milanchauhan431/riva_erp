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
                            $i = 1;$totalGw = $totalNw = 0;
                            foreach($issueItemList as $row):
                                $checkBoxInput = '<input type="checkbox" id="return_checkbox'.$i.'" class="filled-in chk-col-success termCheck" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' />
                                <label for="return_checkbox'.$i.'"></label>';
                                echo '<tr>
                                    <td style="width:10%;">
                                        '.$checkBoxInput.'
                                    </td>
                                    <td>'.$row->item_name.'  <input type="hidden" name="itemData['.$id.'][id]" value="'.$row->id.'"></td>
                                    <td>'.$row->item_desc.'</td>
                                    <td>'.$row->gross_weight.'</td>
                                    <td>'.($row->gross_weight - $row->net_weight).'</td>
                                    <td>'.$row->net_weight.'</td>
                                    <td>'.$row->sold_status.'</td>
                                </tr>';
                                $totalGw += $row->gross_weight;
                                $totalNw += $row->net_weight;
                                $i++;
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
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</form>