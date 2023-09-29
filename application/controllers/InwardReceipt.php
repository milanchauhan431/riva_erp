<?php
class InwardReceipt extends MY_Controller{
    private $indexPage = "inward_receipt/index";
    private $form = "inward_receipt/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Inward Receipt";
		$this->data['headData']->controller = "inwardReceipt";
		$this->data['headData']->pageUrl = "inwardReceipt";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'inwardReceipt']);
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->inwardReceipt->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getInwardReceiptData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInward(){        
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];

        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList();
        $this->data['purityList'] = $this->purity->getPurityList();
        $this->data['fineList'] = $this->fine->getFineList();
        $this->data['polishList'] = $this->polish->getPolishList();
        $this->data['colorList'] = $this->color->getColorList();
        $this->data['clarityList'] = $this->clarity->getClarityList();

        $this->load->view($this->form,$this->data);
    }
}
?>