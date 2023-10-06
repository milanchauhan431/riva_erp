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
    public function reversalApproval(){ 
		$data['pageTitle'] = "Approval reversal"; 
        $post = $this->input->post();
        $data['dataRow'] = $this->inwardReceipt->getInwardReceipt($post);
        $this->load->view('inward_receipt/reversal',$data);
    }

    public function saveReversalApproval(){  
        $post = $this->input->post();
		$this->printJson($this->inwardReceipt->update($post)); 
		
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
        $this->data['trans_no'] = $this->transMainModel->getNextTransNo(['table_name'=>"inward_receipt"]);
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];

        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList();
        $this->data['purityList'] = $this->purity->getPurityList();
        $this->data['diamondQualityList'] = $this->diamondQuality->getList();
        $this->data['fineList'] = $this->fine->getFineList();
        $this->data['polishList'] = $this->polish->getPolishList();
        $this->data['colorList'] = $this->color->getColorList();
        $this->data['clarityList'] = $this->clarity->getClarityList();

        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_number']))
            $errorMessage['trans_number'] = "Inward No. is required.";
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Inward Date is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Product Name is required.";
        if(empty($data['inward_type']))
            $errorMessage['inward_type'] = "Inward Type is required.";
        if(empty($data['order_type']))
            $errorMessage['order_type'] = "Challan Type is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty is required.";
        if(empty($data['gross_weight']))
            $errorMessage['gross_weight'] = "Gross Weight is required.";
        if(empty($data['net_weight']))
            $errorMessage['net_weight'] = "Net Weight is required.";
        if(empty($data['purchase_price']) && $data['order_type'] == "Regular")
            $errorMessage['purchase_price'] = "Purchase Price is required.";

        if(!empty($data['approved_by']) && empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $attachments = array();
            if(!empty($data['id']) && !empty($data['attachment'])):
                $attachments = $data['attachment']; unset($data['attachment']);
            endif;

            if(!empty($_FILES['attachments']['name'][0])):
                $this->load->library('upload');
                $this->load->library('image_lib'); 

                $errorMessage['attachment_error'] = "";
                
                foreach($_FILES['attachments']['name'] as $key => $fileName):
                    $_FILES['userfile']['name']     = $fileName;
                    $_FILES['userfile']['type']     = $_FILES['attachments']['type'][$key];
                    $_FILES['userfile']['tmp_name'] = $_FILES['attachments']['tmp_name'][$key];
                    $_FILES['userfile']['error']    = $_FILES['attachments']['error'][$key];
                    $_FILES['userfile']['size']     = $_FILES['attachments']['size'][$key];

                    $imagePath = realpath(APPPATH . '../assets/uploads/inventory_img/');

                    $fileName = preg_replace('/[^A-Za-z0-9]+/', '_', strtolower($fileName));
                    $config = ['file_name' => time()."_IR_".$fileName,'allowed_types' => 'jpg|jpeg|png|gif|JPG|JPEG|PNG','max_size' => 10240,'overwrite' => FALSE, 'upload_path' => $imagePath];                

                    $this->upload->initialize($config);
                    if(!$this->upload->do_upload()):
                        $errorMessage['attachment_error'] .= $fileName." => ". $this->upload->display_errors();
                    else:
                        $uploadData = $this->upload->data();
                        $attachments[] = $uploadData['file_name'];

                        $imgConfig['image_library'] = 'gd2';
                        $imgConfig['source_image'] = $uploadData['full_path'];
                        $imgConfig['maintain_ratio'] = TRUE;
                        $imgConfig['width'] = 640;
                        $imgConfig['height'] = 480;
                        $imgConfig['quality'] = "50%";

                        $this->image_lib->clear();
                        $this->image_lib->initialize($imgConfig);                   

                        if(!$this->image_lib->resize()):
                            $errorMessage['attachment_error'] .= $fileName." => ". $this->image_lib->display_errors();
                        endif;
                    endif;
                endforeach;

                if(!empty($errorMessage['attachment_error'])):
                    foreach($attachments as $file):
                        if(file_exists($imagePath.'/'.$file)): unlink($imagePath.'/'.$file); endif;
                    endforeach;
                    $this->printJson(['status'=>0,'message'=>$errorMessage]);
                endif;                
            endif;

            if(!empty($attachments)):
                $data['attachments'] = implode(",",$attachments);
            endif;

            $this->printJson($this->inwardReceipt->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        if(!empty($data['is_approve'])):
            $this->data['approved_by'] = $this->loginId;
            $this->data['locationList'] = $this->storeLocation->getStoreLocationList(['final_location'=>1]);
        endif;
        
        $this->data['dataRow'] = $this->inwardReceipt->getInwardReceipt($data);

        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>"1,2,3"]);
        $this->data['itemList'] = $this->item->getItemList();
        $this->data['purityList'] = $this->purity->getPurityList();
        $this->data['fineList'] = $this->fine->getFineList();
        $this->data['polishList'] = $this->polish->getPolishList();
        $this->data['colorList'] = $this->color->getColorList();
        $this->data['clarityList'] = $this->clarity->getClarityList();

        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->inwardReceipt->delete($id));
        endif;
    }
}
?>