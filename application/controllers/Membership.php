<?php
class Membership extends MY_Controller{
    private $index = "membership/index";
    private $form = "membership/form"; 
    private $viewEmiStatement = "membership/emi_statement";

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

    public function viewEmiStatement($jsonData = ''){
        $postData = (Array) decodeURL($jsonData);
        $this->data['dataRow'] = $dataRow = $this->membership->getMembership(['id'=>$postData['id']]);
        $this->data['partyDetail'] = $this->party->getParty(['id'=>$dataRow->party_id]);
        $this->load->view($this->viewEmiStatement,$this->data);
    }

    public function getEmiStatement($jsonData = ''){
        if(!empty($jsonData)):
            $postData = (Array) decodeURL($jsonData);
        else:
            $postData = $this->input->post();
        endif;

        $result = $this->membership->getEmiStatement($postData);

        $tbody = '';$i=1;
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-center">'.$i.'</td>
                <td class="text-center">'.$row->trans_number.'</td>
                <td class="text-center">'.formatDate($row->trans_date).'</td>
                <td class="text-center">'.floatval($row->net_amount).'</td>
            </tr>';
            $i++;
        endforeach;

        if(empty($jsonData)):
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        else:
            $dataRow = $this->membership->getMembership(['id'=>$postData['ref_id']]);
            $partyDetail = $this->party->getParty(['id'=>$dataRow->party_id]);

            $companyData = $this->masterModel->getCompanyInfo();
            $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
            $logo = base_url('assets/images/' . $logoFile);

            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">EMI STATEMENT</td>
                    <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                    <td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.date("d-m-Y").'</td>
                </tr>
            </table>
            <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
            </table>';

            $pdfData = '<table class="table table-bordered item-list-bb" repeat_header="1">
                <tr>
                    <th>Customer Name</th>
                    <td>'.$partyDetail->party_name.'</td>
                    <th>Contact No.</th>
                    <td>'.$partyDetail->party_mobile.'</td>
                    <th>Membership No.</th>
                    <td>'.$dataRow->trans_number.'</td>
                    <th>Membership Date</th>
                    <td>'.formatDate($dataRow->trans_date).'</td>
                </tr>
                <tr>                                    
                    <th>Plan Name</th>
                    <td>'.$dataRow->plan_name.'</td>
                    <th>No. of EMI</th>
                    <td>'.floatval($dataRow->credit_period).'</td>
                    <th>EMI Amount</th>
                    <td>'.floatval($dataRow->total_amount).'</td>
                    <th>Total Amount</th>
                    <td>'.floatval($dataRow->net_amount).'</td>
                </tr>
                <tr>                                   
                    <th>Received EMI</th>
                    <td>'.floatval($dataRow->received_emi).'</td>
                    <th>Received Amount</th>
                    <td>'.floatval($dataRow->rop_amount).'</td>
                    <th>Pending EMI</th>
                    <td>'.floatval(($dataRow->credit_period - $dataRow->received_emi)).'</td>
                    <th>Pending Amount</th>
                    <td>'.floatval(($dataRow->net_amount - $dataRow->rop_amount)).'</td>
                </tr>
            </table>';


            $pdfData .= '<hr><h2>Payment Detail :</h2><table class="table table-bordered item-list-bb" repeat_header="1" style="margin-top:10px;">
                <thead class="thead-info" id="theadData">
                    <tr>
                        <th>#</th>
                        <th>Vou. No.</th>
                        <th>Vou. Date</th>
                        <th>Vou. Amount</th>
                    </tr>
                </thead>
                <tbody id="tbodyData">
                    '.$tbody.'
				</tbody>
            </table>';

            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                    <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'EMI_STATEMENT.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle('EMI STATEMENT');
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('P','','','','',5,5,19,20,3,3,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        endif;
    }
}
?>