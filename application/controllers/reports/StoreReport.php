<?php
class StoreReport extends MY_Controller{
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store Report";
		$this->data['headData']->controller = "reports/storeReport";
    }

    public function stockRegister(){
        $this->data['pageHeader'] = 'STOCK REGISTER';
        $this->data['headData']->pageUrl = "reports/storeReport/stockRegister";
        $this->load->view("reports/store_report/item_stock",$this->data);
    }

    public function getStockRegisterData(){
        $data = $this->input->post();
        $result = $this->storeReport->getStockRegisterData($data);

        $tbody = '';$i=1;
        foreach($result as $row):
            $tbody .= '<tr>
                <td  class="text-center">'.$i++.'</td>
                <td  class="text-left">'.$row->item_code.'</td>
                <td  class="text-left"><a href="'.base_url("reports/storeReport/locationWiseStockRegister/".$row->id).'" target="_blank">'.$row->item_name.'</a></td>
                <td  class="text-right">'.floatVal($row->stock_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->gross_weight,3)).'</td>
                <td  class="text-right">'.floatVal(round($row->net_weight,3)).'</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    public function locationWiseStockRegister($item_id=""){
        $this->data['pageHeader'] = 'LOCATION WISE STOCK REGISTER';
        $this->data['headData']->pageUrl = "reports/storeReport/stockRegister";
        $this->data['item_id'] = $item_id;
        $this->data['itemList'] = $this->item->getItemList();
        $this->load->view("reports/store_report/item_location_stock",$this->data);
    }

    public function getLocationWiseStockData(){
        $data = $this->input->post();
        $result = $this->storeReport->getLocationWiseStockData($data);

        $totalStock = $totalGrossWeight = $totalNetWeight = 0;
        $tbody = '';$i=1;
        foreach($result as $row):
            $tbody .= '<tr>
                <td  class="text-center">'.$i++.'</td>
                <td  class="text-left">
                    <a href="'.base_url("reports/storeReport/serialNumberWiseStockRegister/".$row->item_id."/".$row->location_id).'">'.$row->location.'</a>
                </td>
                <td  class="text-right">'.floatVal($row->stock_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->gross_weight)).'</td>
                <td  class="text-right">'.floatVal(round($row->net_weight,3)).'</td>
            </tr>';

            $totalStock += $row->stock_qty;
            $totalGrossWeight += round($row->gross_weight,3);
            $totalNetWeight += round($row->net_weight,3);
        endforeach;

        $tfoot = '<tr>
            <th colspan="2">Total</th>
            <th  class="text-right">'.$totalStock.'</th>
            <th  class="text-right">'.$totalGrossWeight.'</th>
            <th  class="text-right">'.$totalNetWeight.'</th>
        </tr>';

        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function serialNumberWiseStockRegister($item_id="",$location_id=""){
        $this->data['pageHeader'] = 'LOCATION WISE STOCK REGISTER';
        $this->data['headData']->pageUrl = "reports/storeReport/stockRegister";
        $this->data['item_id'] = $item_id;
        $this->data['location_id'] = $location_id;
        $this->data['itemList'] = $this->item->getItemList();
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location' => 1]);
        $this->load->view("reports/store_report/item_serial_num_stock",$this->data);
    }

    public function getSerialNumberWiseStockData(){
        $data = $this->input->post();
        $result = $this->storeReport->getSerialNumberWiseStockData($data);

        $totalStock = $totalGrossWeight = $totalNetWeight = 0;
        $tbody = '';$i=1;
        foreach($result as $row):
            $tbody .= '<tr>
                <td  class="text-center">'.$i++.'</td>
                <td  class="text-left">'.$row->unique_id.'</td>
                <td  class="text-right">'.floatVal($row->stock_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->gross_weight,3)).'</td>
                <td  class="text-right">'.floatVal(round($row->net_weight,3)).'</td>
            </tr>';

            $totalStock += $row->stock_qty;
            $totalGrossWeight += round($row->gross_weight,3);
            $totalNetWeight += round($row->net_weight,3);
        endforeach;

        $tfoot = '<tr>
            <th colspan="2">Total</th>
            <th  class="text-right">'.$totalStock.'</th>
            <th  class="text-right">'.$totalGrossWeight.'</th>
            <th  class="text-right">'.$totalNetWeight.'</th>
        </tr>';

        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    public function itemHistory(){
        $this->data['pageHeader'] = 'ITEM HISTORY';
        $this->data['headData']->pageUrl = "reports/storeReport/itemHistory";
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view("reports/store_report/item_history",$this->data);
    }

    public function getItemHistoryData(){
        $data = $this->input->post();
        $result = $this->storeReport->getItemHistoryData($data);

        $tbody = '';$i=1;
        foreach($result as $row):
            $tbody .= '<tr>
                <td  class="text-center">'.$i++.'</td>
                <td  class="text-left">'.$row->item_code.'</td>
                <td  class="text-left"><a href="'.base_url("reports/storeReport/itemHistoryDetail/".$row->id).'" target="_blank">'.$row->item_name.'</a></td>

                <td  class="text-right">'.floatVal($row->op_stock_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->op_gross_weight,3)).'</td>
                <td  class="text-right">'.floatVal(round($row->op_net_weight,3)).'</td>

                <td  class="text-right">'.floatVal($row->in_stock_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->in_gross_weight,3)).'</td>
                <td  class="text-right">'.floatVal(round($row->in_net_weight,3)).'</td>

                <td  class="text-right">'.floatVal($row->out_stock_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->out_gross_weight,3)).'</td>
                <td  class="text-right">'.floatVal(round($row->out_net_weight,3)).'</td>

                <td  class="text-right">'.floatVal($row->cl_stock_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->cl_gross_weight,3)).'</td>
                <td  class="text-right">'.floatVal(round($row->cl_net_weight,3)).'</td>
            </tr>';
        endforeach;

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    public function itemHistoryDetail($id=""){
        $this->data['pageHeader'] = 'ITEM HISTORY DETAIL';
        $this->data['headData']->pageUrl = "reports/storeReport/itemHistory";
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['item_id'] = $id;
        $this->load->view("reports/store_report/item_history_detail",$this->data);
    }

    public function getItemHistoryDetailData(){
        $data = $this->input->post();
        $itemDetail = $this->storeReport->getItemHistoryData($data);
        $result = $this->storeReport->getItemHistoryDetailData($data);

        $thead = '<tr>
            <th class="text-center" colspan="14">[ '.$itemDetail->item_code.' ] '.$itemDetail->item_name.'</th>
        </tr>
        <tr>
            <th class="text-right" colspan="11">Opening</th>
            <th>Qty. : '.$itemDetail->op_stock_qty.'</th>
            <th>G.W. : '.round($itemDetail->op_gross_weight,3).'</th>
            <th>N.W. : '.round($itemDetail->op_net_weight,3).'</th>
        </tr>
        <tr>
            <th rowspan="2" class="text-center">#</th>
            <th rowspan="2" class="text-left">Vou. Name</th>
            <th rowspan="2" class="text-left">Vou. Date</th>
            <th rowspan="2" class="text-left">Vou. No.</th>
            <th rowspan="2" class="text-left">Serial No.</th>
            <th colspan="3" class="text-center">IN</th>
            <th colspan="3" class="text-center">OUT</th>
            <th colspan="3" class="text-center">Balance</th>
        </tr>
        <tr>
            <th class="text-right">Qty.</th>
            <th class="text-right">G.W.</th>
            <th class="text-right">N.W.</th>

            <th class="text-right">Qty.</th>
            <th class="text-right">G.W.</th>
            <th class="text-right">N.W.</th>

            <th class="text-right">Qty.</th>
            <th class="text-right">G.W.</th>
            <th class="text-right">N.W.</th>
        </tr>';

        $balanceQty = $itemDetail->op_stock_qty;
        $balanceGW = round($itemDetail->op_gross_weight,3);
        $balanceNW = round($itemDetail->op_net_weight,3);
        $tbody = '';$i=1;
        foreach($result as $row):
            $balanceQty += $row->qty * $row->p_or_m;
            $balanceGW += round(($row->gross_weight * $row->p_or_m),3);
            $balanceNW += round(($row->net_weight * $row->p_or_m),3);

            $tbody .= '<tr>
                <td  class="text-center">'.$i++.'</td>
                <td  class="text-left">'.$row->vou_name_long.'</td>
                <td  class="text-left">'.formatDate($row->ref_date).'</td>
                <td  class="text-left">'.$row->ref_no.'</td>
                <td  class="text-left">'.$row->unique_id.'</td>

                <td  class="text-right">'.floatVal($row->in_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->in_gross_weight,3)).'</td>
                <td  class="text-right">'.floatVal(round($row->in_net_weight,3)).'</td>

                <td  class="text-right">'.floatVal($row->out_qty).'</td>
                <td  class="text-right">'.floatVal(round($row->out_gross_weight,3)).'</td>
                <td  class="text-right">'.floatVal(round($row->out_net_weight,3)).'</td>

                <td  class="text-right">'.floatVal($balanceQty).'</td>
                <td  class="text-right">'.floatVal(round($balanceGW,3)).'</td>
                <td  class="text-right">'.floatVal(round($balanceNW,3)).'</td>
            </tr>';
        endforeach;

        $tfoot = '<tr>
            <th colspan="5">Total</th>
            <th>'.$itemDetail->in_stock_qty.'</th>
            <th>'.round($itemDetail->in_gross_weight,3).'</th>
            <th>'.round($itemDetail->in_net_weight,3).'</th>
            <th>'.$itemDetail->out_stock_qty.'</th>
            <th>'.round($itemDetail->out_gross_weight,3).'</th>
            <th>'.round($itemDetail->out_net_weight,3).'</th>
            <th>'.$itemDetail->cl_stock_qty.'</th>
            <th>'.round($itemDetail->cl_gross_weight,3).'</th>
            <th>'.round($itemDetail->cl_net_weight,3).'</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }
}
?>