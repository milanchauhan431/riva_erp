<?php
class MaterialIssue extends MY_Controller{
    private $indexPage = "material_issue/index";
    private $issueForm = "material_issue/form";
    
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Material Issue";
		$this->data['headData']->controller = "materialIssue";
        $this->data['headData']->pageUrl = "materialIssue";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'materialIssue']);
	}
	 
	public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->materialIssue->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getMaterialIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addIssue(){
        $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1]);
        $this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->load->view($this->issueForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['vou_acc_id']))
            $errorMessage['vou_acc_id'] = "Collected By is required.";
        if(empty($data['sales_executive']))
            $errorMessage['sales_executive'] = "Sales Executive is required.";
        if(empty($data['feasibility_by']))
            $errorMessage['feasibility_by'] = "To Location is required.";
        if(empty($data['itemData']))
            $errorMessage['item_error'] = "Please add at liest one item.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['trans_prefix'] =  $this->data['entryData']->trans_prefix;
            $data['entry_type'] = $this->data['entryData']->id;
            $this->printJson($this->materialIssue->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->materialIssue->delete($id));
        endif;
    }
}
?>