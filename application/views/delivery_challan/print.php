<html>
    <head>
        <title>Delivery Challan</title>
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url();?>assets/images/favicon.png">
    </head>
    <body>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered" style="margin-bottom:10px;">
                    <tr>
                        <!-- <td>
                            <img src="<?= $letter_head ?>" class="img">
                        </td> -->
                        <th class="text-center fs-30"> 
                            <?=$companyData->company_name?>
                        </th>                    
                    </tr>
                    <tr>
                        <td class="text-center"> 
                            <?=$companyData->company_address.', '.$companyData->company_city.' - '.$companyData->company_pincode?><br>
                            Contact No. : <?=$companyData->company_contact.((!empty($companyData->company_phone))?" / ".$companyData->company_phone:"")?> Email : <?=$companyData->company_email?>
                        </td>
                    </tr>
                </table>

                <table class="table bg-light-grey">
                    <tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
                        <td style="width:33%;" class="fs-18 text-left">
                            GSTIN: <?=$companyData->company_gst_no?>
                        </td>
                        <td style="width:33%;" class="fs-18 text-center">Delivery Challan</td>
                        <td style="width:33%;" class="fs-18 text-right"></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb fs-22" style="margin-top:5px;">
                    <tr >
                        <td rowspan="4" style="width:67%;vertical-align:top;">
                            <b>M/S. <?=$dataRow->party_name?></b><br>
                            <?=(!empty($dataRow->ship_address) ? $dataRow->ship_address ." - ".$dataRow->ship_pincode : '')?><br>
                            <b>Kind. Attn. : <?=$dataRow->contact_person?></b> <br>
                            Contact No. : <?=$dataRow->contact_no?><br>
                            Email : <?=$partyData->contact_email?><br><br>
                            GSTIN : <?=$dataRow->gstin?>
                        </td>
                        <td>
                            <b>DC. No.</b>
                        </td>
                        <td>
                            <?=$dataRow->trans_number?>
                        </td>
                    </tr>
                    <tr>
				        <th class="text-left">DC Date</th>
                        <td><?=formatDate($dataRow->trans_date)?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Cust. PO. No.</th>
                        <td><?=$dataRow->doc_no?></td>
                    </tr>
                    <tr>
                        <th class="text-left">Cust. PO. Date</th>
                        <td><?=(!empty($dataRow->doc_date)) ? formatDate($dataRow->doc_date) : ""?></td>
                    </tr>
                </table>
                
                <table class="table item-list-bb" style="margin-top:10px;">
                    <thead>
                        <tr>
                            <th style="width:40px;">No.</th>
                            <th class="text-left">Item Description</th>
                            <th style="width:90px;">HSN/SAC</th>
                            <th style="width:90px;">G.W.</th>
                            <th style="width:90px;">N.W.</th>
                            <th style="width:100px;">Qty</th>
                            <th style="width:50px;">UOM</th>
                            <th style="width:50px;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1;$totalQty = 0;
                            if(!empty($dataRow->itemList)):
                                foreach($dataRow->itemList as $row):
                                    
                                    echo '<tr>';
                                        echo '<td class="text-center">'.$i++.'</td>';
                                        echo '<td>';
                                    echo '<b>'.$row->item_name . '</b><br>';
                                    echo '<small>Serial No. : '.$row->unique_id . '</small><br>';

                                    if(in_array($row->stock_category,["Gold","Gold Items","Gold + Diamond Items","Lab Grown Diamond + Gold Items","Platinum + Gold + Diamond Items"])):
                                        echo '<small>Gold Carat: ' . $row->purity . '</small><br>';
                                    endif;
                                    if(!empty($row->gold_platinum_price) && $row->gold_platinum_price != 0 && in_array($row->stock_category,["Lab Grown Diamond + Gold Items","Platinum + Gold + Diamond Items"])):
                                        echo '<small>Gold Amount : ' . floatVal($row->gold_platinum_price) . '</small><br>';
                                    endif;
                                    if(!empty($gold_weight) && $gold_weight > 0 && in_array($row->stock_category,["Lab Grown Diamond + Gold Items","Platinum + Gold + Diamond Items"])):
                                        $row->gold_weight = $gold_weight;
                                        echo '<small>Gold Weight : ' . floatVal($row->gold_weight) . '</small><br>';
                                    endif;
                                    if(!empty($row->other_charge) && $row->other_charge > 0):
                                        echo '<small>Other Charge : ' . floatVal($row->other_charge) . '</small><br>';
                                    endif;
                                    if(!empty($row->vrc_charge) && $row->vrc_charge > 0):
                                        echo '<small>Variety Charge : ' . floatVal($row->vrc_charge) . '</small><br>';
                                    endif;
                                    if(!empty($row->diamond_amount) && $row->diamond_amount > 0):
                                        echo '<small>Diamond Amount : ' . floatVal($row->diamond_amount) . '</small><br>';
                                    endif;
                                    if(!empty($row->color)):
                                        echo '<small>Diamond Color :</small> ' . $row->color . '<br>';
                                    endif;
                                    if(!empty($row->diamond_carat)):
                                        echo '<small>Diamond Carat :</small> ' . $row->diamond_carat . '<br>';
                                    endif;
                                    if(!empty($row->diamond_pcs) && $row->diamond_pcs > 0):
                                        echo '<small>Diamond Pcs. :</small> ' . floatVal($row->diamond_pcs) . '<br>';
                                    endif;
                                echo '</td>';
                                        echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                        echo '<td class="text-center">'.$row->gross_weight.'</td>';
                                        echo '<td class="text-center">'.$row->net_weight.'</td>';
                                        echo '<td class="text-right">'.$row->qty.'</td>';
                                        echo '<td class="text-center">'.$row->unit_name.'</td>';
                                        echo '<td class="text-center">'.$row->price.'</td>';
                                    echo '</tr>';
                                    $totalQty += $row->qty;
                                endforeach;
                            endif;

                            $blankLines = (15 - $i);
                            if ($blankLines > 0) :
                                for ($j = 1; $j <= $blankLines; $j++) :
                                    echo '<tr>
                                        <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                        <td style="border-top:none;border-bottom:none;"></td>
                                    </tr>';
                                endfor;
                            endif;
                        ?>
                        <tr>
                            <th colspan="5" class="text-right">Total Qty.</th>
                            <th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
                            <th class="text-right"></th>
                            <th class="text-right"></th>
                        </tr>
                        <tr>
                            <td colspan="8" class="text-left"><b>Note: </b> <?= $dataRow->remark ?></td>
                        </tr>
                    </tbody>
                </table>
		

                <h4>Terms & Conditions :-</h4>
                <table class="table top-table" style="margin-top:10px;">
                    <?php
                        if(!empty($dataRow->termsConditions)):
                            foreach($dataRow->termsConditions as $row):
                                echo '<tr>';
                                    /* echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>'; */
                                    echo '<td class=" fs-11"><ul><li> '.$row->condition.' </li></ul></td>';
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
                            <th class="text-center">For, <?=$companyData->company_name?></th>
                        </tr>
                        <tr>
                            <td colspan="3" height="50"></td>
                        </tr>
                        <tr>
                            <td><br>This is a computer-generated challan.</td>
                            <td class="text-center"><?=$dataRow->created_name?><br>Prepared By</td>
                            <td class="text-center"><br>Authorised By</td>
                        </tr>
                    </table>
                    <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;">DC No. & Date : <?=$dataRow->trans_number.' ['.formatDate($dataRow->trans_date).']'?></td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>
                </htmlpagefooter>
				<sethtmlpagefooter name="lastpage" value="on" />                
            </div>
        </div>        
    </body>
</html>
