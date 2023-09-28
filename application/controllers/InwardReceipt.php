<?php
class InwardReceipt extends MY_Controller{
    private $indexPage = "inward_receipt/index";
    private $form = "inward_receipt/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Inward Receipt";
		$this->data['headData']->controller = "inwardReceipt";
		$this->data['headData']->pageUrl = "inwardReceipt";
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->inwardReceipt->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getInwardReceiptData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    
}
?>