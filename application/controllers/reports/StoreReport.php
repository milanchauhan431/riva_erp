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
                <td  class="text-right">'.floatVal($row->gross_weight).'</td>
                <td  class="text-right">'.floatVal($row->net_weight).'</td>
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
                <td  class="text-right">'.floatVal($row->gross_weight).'</td>
                <td  class="text-right">'.floatVal($row->net_weight).'</td>
            </tr>';

            $totalStock += $row->stock_qty;
            $totalGrossWeight += $row->gross_weight;
            $totalNetWeight += $row->net_weight;
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
                <td  class="text-right">'.floatVal($row->gross_weight).'</td>
                <td  class="text-right">'.floatVal($row->net_weight).'</td>
            </tr>';

            $totalStock += $row->stock_qty;
            $totalGrossWeight += $row->gross_weight;
            $totalNetWeight += $row->net_weight;
        endforeach;

        $tfoot = '<tr>
            <th colspan="2">Total</th>
            <th  class="text-right">'.$totalStock.'</th>
            <th  class="text-right">'.$totalGrossWeight.'</th>
            <th  class="text-right">'.$totalNetWeight.'</th>
        </tr>';

        $this->printJson(['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }
}
?>