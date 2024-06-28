<?php
class Membership extends MY_Controller{
    private $index = "membership/index";
    private $form = "membership/form"; 

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Membership";
		$this->data['headData']->controller = "membership";
        $this->data['headData']->pageUrl = "membership";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'membership']);
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->membership->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;         
            $sendData[] = getMembershipData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMembership(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['planList'] = $this->membershipPlan->getPlanList();
        $this->data['salesExecutives'] = $this->employee->getEmployeeList();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['ref_id']))
			$errorMessage['ref_id'] = "Membership Plan is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['total_amount']))
            $errorMessage['total_amount'] = "EMI Amount is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->membership->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->membership->getMembership($data);
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['planList'] = $this->membershipPlan->getPlanList();
        $this->data['salesExecutives'] = $this->employee->getEmployeeList();
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->membership->delete($id));
        endif;
    }
}
?>