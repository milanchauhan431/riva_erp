<html>

<head>
    <title>Quotation</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url(); ?>assets/images/favicon.png">
    <style>
        <?php echo $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v=' . time())); ?>body {
            margin: 0px;
            padding: 0px;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <table>
                <tr align="center">
                    <td>
                        <p style="font-size:25px;font-weight:bold">RIVA</p>
                    </td>
                </tr>
            </table>
            <p class="fs-18 text-center">Estimate </p>
             <p> <b>BILL TO</b><br>
                <b>M/S. <?= $dataRow->party_name ?></b><br>
                <?= (!empty($dataRow->ship_address) ? $dataRow->ship_address . " - " . $dataRow->ship_pincode : '') ?><br>
            </p>
            <p>
                <b>Qtn. No. : <?= $dataRow->trans_number ?></b><br>
                <b> Rev No. :</b> <?= sprintf("%02d", $dataRow->quote_rev_no) ?> / <?= formatDate($dataRow->doc_date) ?><br>
                <b>Qtn. Date</b> : <?= formatDate($dataRow->trans_date) ?><br>
             </p>

            <h4>Item Details: </h4>
            <hr />
            <table class="table item-list-bb" style="margin-top:10px;">

                <tbody>
                    <?php
                    $i = 1;
                    $totalQty = 0;
                    $migst = 0;
                    $mcgst = 0;
                    $msgst = 0;
                    if (!empty($dataRow->itemList)) :
                        foreach ($dataRow->itemList as $row) :
                            echo '<strong>Item:</strong> ' . $row->item_name . ' -  ' . $row->unique_id . '<br>';
                            echo '<strong>HSN/SAC:</strong> ' . $row->hsn_code . '<br>';
                            echo '<strong>Qty:</strong> '. floatVal($row->qty) . ' (' . $row->unit_name . ')<br>';
                            echo '<strong>Rate:</strong> ' . floatVal($row->price) . '<br>';
                            echo '<strong>Making Charge:</strong> ' . floatVal($row->making_charge - $row->making_charge_dicount) . '<br>';
                            echo '<strong>Disc (%):</strong> ' . floatVal($row->disc_per) . '<br>';
                            echo '<strong>GST(%):</strong> ' . $row->gst_per . '<br>';
                            echo '<strong>Amount (' . $partyData->currency . '):</strong> ' . $row->taxable_amount . '<br>';
                            echo '<hr />';

                            $totalQty += $row->qty;
                            if ($row->gst_per > $migst) {
                                $migst = $row->gst_per;
                                $mcgst = $row->cgst_per;
                                $msgst = $row->sgst_per;
                            }
                        endforeach;
                    endif;


                    $rwspan = 0;
                    $srwspan = '';
                    $beforExp = "";
                    $afterExp = "";
                    $invExpenseData = (!empty($dataRow->expenseData)) ? $dataRow->expenseData : array();
                    foreach ($expenseList as $row) :
                        $expAmt = 0;
                        $amtFiledName = $row->map_code . "_amount";
                        if (!empty($invExpenseData) && $row->map_code != "roff") :
                            $expAmt = floatVal($invExpenseData->{$amtFiledName});
                        endif;

                        if (!empty($expAmt)) :
                            if ($row->position == 1) :
                                if ($rwspan == 0) :
                                    $beforExp .= '<th colspan="4" class="text-right">' . $row->exp_name . '</th>
                                        <td class="text-right" colspan="4" >' . sprintf('%.2f', $expAmt) . '</td>';
                                else :
                                    $beforExp .= '<tr>
                                            <th colspan="4" class="text-right">' . $row->exp_name . '</th>
                                            <td class="text-right" colspan="4" >' . sprintf('%.2f', $expAmt) . '</td>
                                        </tr>';
                                endif;
                            else :
                                $afterExp .= '<tr>
                                        <th colspan="4" class="text-right">' . $row->exp_name . '</th>
                                        <td class="text-right" colspan="4">  ' . sprintf('%.2f', $expAmt) . '</td>
                                    </tr>';
                            endif;
                            $rwspan++;
                        endif;
                    endforeach;

                    $taxHtml = '';
                    foreach ($taxList as $taxRow) :
                        $taxAmt = 0;
                        $taxAmt = floatVal($dataRow->{$taxRow->map_code . '_amount'});
                        if (!empty($taxAmt)) :
                            if ($rwspan == 0) :
                                $taxHtml .= '<th colspan="4" class="text-right">' . $taxRow->name . ' @' . (($dataRow->gst_type == 1) ? floatVal($migst / 2) : $migst) . '%</th>
                                    <td class="text-right" colspan="4" >' . sprintf('%.2f', $taxAmt) . '</td>';
                            else :
                                $taxHtml .= '<tr>
                                        <th colspan="4" class="text-right">' . $taxRow->name . ' @' . (($dataRow->gst_type == 1) ? floatVal($migst / 2) : $migst) . '%</th>
                                        <td class="text-right" colspan="4" >' . sprintf('%.2f', $taxAmt) . '</td>
                                    </tr>';
                            endif;

                            $rwspan++;
                        endif;
                    endforeach;
                    $fixRwSpan = (!empty($rwspan)) ? 3 : 0;
                    ?>
                    <tr>
                        <th colspan="4" class="text-right">Total Qty.</th>
                        <th class="text-right" colspan="4"><?= sprintf('%.3f', $totalQty) ?></th>

                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Sub Total</th>
                        <th class="text-right" colspan="4"><?= sprintf('%.2f', $dataRow->taxable_amount) ?></th>
                    </tr>
                    <tr>

                    </tr>
                    <?= $beforExp . $taxHtml . $afterExp ?>
                    <tr>
                        <th class="text-left" colspan="4">
                            Amount In Words
                        </th>
                        <td class="text-right" colspan="4"><?= numToWordEnglish(sprintf('%.2f', $dataRow->net_amount)) ?> </td>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Round Off</th>
                        <td class="text-right" colspan="4"><?= sprintf('%.2f', $dataRow->round_off_amount) ?></td>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Grand Total</th>
                        <th class="text-right" colspan="4"><?= sprintf('%.2f', $dataRow->net_amount) ?></th>
                    </tr>
                </tbody>
            </table> 
            <h4>Terms & Conditions :-</h4>
            <table class="table top-table" style="margin-top:10px;">
                <?php
                if (!empty($dataRow->termsConditions)) :
                    foreach ($dataRow->termsConditions as $row) :
                        echo '<tr>';
                        /* echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>'; */
                        echo '<td class=" fs-11"><ul><li> ' . $row->condition . ' </li></ul></td>';
                        echo '</tr>';
                    endforeach;
                endif;
                ?>
            </table>

            <htmlpagefooter name="lastpage">
                <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;"></td>
                        <td style="width:20%;"></td>
                        <th class="text-center">For, <?= $companyData->company_name ?></th>
                    </tr>

                </table>

            </htmlpagefooter>
            <sethtmlpagefooter name="lastpage" value="on" />
        </div>
    </div>
</body>

</html>
<script>
    window.print();
    window.onafterprint = window.close;
</script>