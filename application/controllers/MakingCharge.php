<?php
class MakingCharge extends MY_Controller{

	 public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Making Charge";
		$this->data['headData']->controller = "makingCharge";
	}
	
	public function index(){
		$queryData['tableName'] = "stock_transaction";  
		$queryData['select'] = "stock_transaction.*,item_master.item_code,item_master.item_name,item_master.hsn_code,item_master.gst_per,item_master.unit_id,unit_master.unit_name";

		$queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$queryData['where']['stock_type'] = "NEW"; 
	    $this->data['charge_data'] = $this->masterModel->rows($queryData); 
        $this->load->view('making_charge',$this->data);
    }
	 
    public function save(){
        $data = $this->input->post();
        $array = $this->input->post('id');
        $errorMessage = array();
		 foreach($array as $ids){ 
			$this->masterModel->edit("stock_transaction",['id'=>$ids],['stock_type'=>"FRESH",
			'price'=>$data['price'][$ids],
			'making_per'=>$data['making_per'][$ids],
			'making_disc_per'=>$data['making_disc_per'][$ids],
			'otc_amount'=>$data['otc_amount'][$ids],
			'vrc_amount'=>$data['vrc_amount'][$ids],
			'diamond_amount'=>$data['diamond_amount'][$ids]
			]
		);
		 }
		 $this->printJson(['status'=>1,'message'=>"Saved!"]);
	}
}
