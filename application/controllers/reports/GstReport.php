<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory as io_factory; 
class GstReport extends MY_Controller{
    private $gstr1_report = "reports/gst_report/gstr1_report";
    private $gstr2_report = "reports/gst_report/gstr2_report";
    private $gstr2b_match = "reports/gst_report/gstr2b_match";

    private $gstr1ReportTypes = [
        'b2b'=>'b2b,sez,de',
        'b2ba' => 'b2ba',
        'b2cl' => 'b2cl',
        'b2cla' => 'b2cla',
        'b2cs' => 'b2cs',
        'b2csa' => 'b2csa',
        'cdnr' => 'cdnr',
        'cdnra' => 'cdnra',
        'cdnur' => 'cdnur',
        'cdnura' => 'cdnura',
        'exp'=>'exp',
        'expa' => 'expa',
        'at' => 'at',
        'ata' => 'ata',
        'atadj' => 'atadj',
        'atadja' => 'atadja',
        'exemp' => 'exemp',
        'hsn' => 'hsn',
        'docs' => 'docs' 
    ];

    private $gstr2ReportTypes = [
        'b2b' => "b2b",
        'b2bur' => "b2bur",
        'imps' => "imps",
        'impg' => "impg",
        'cdnr' => "cdnr",
        'cdnur' => "cdnur",
        'at' => 'at',
        'atadj' => 'atadj',
        'exemp' => 'exemp',
        'itcr' => 'itcr',
        'hsn' => 'hsnsum'
    ];

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "GST Report";
		$this->data['headData']->controller = "reports/gstReport";
	}

    public function gstr1(){
        $this->data['headData']->pageUrl = "reports/gstReport/gstr1";
        $this->data['headData']->pageTitle = "GSTR 1 REPORT";
        $this->data['pageHeader'] = 'GSTR 1 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['gstr1Types'] = $this->gstr1ReportTypes;
        $this->load->view($this->gstr1_report, $this->data);
    }

    public function gstr2(){
        $this->data['headData']->pageUrl = "reports/gstReport/gstr2";
        $this->data['headData']->pageTitle = "GSTR 2 REPORT";
        $this->data['pageHeader'] = 'GSTR 2 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['gstr2Types'] = $this->gstr2ReportTypes;
        $this->load->view($this->gstr2_report, $this->data);
    }

    public function getGstr1Report($jsonData=''){        
        if(!empty($jsonData)):
            $data =(array) decodeURL($jsonData);

            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];
            $bgPrimary = [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DAEAFA'],
                ],
                'font' => ['bold' => true]
            ];

            $i = 0;$data['report'] = "gstr1";
            foreach($this->gstr1ReportTypes as $key => $value):
                $html = "";
                $html = $this->{$key}($data)['html'];                

                $pdfData = '<table>'.$html.'</table>';
                $pdfData = str_replace('&', '&amp;', $pdfData);
                
                $reader->setSheetIndex($i);

                if($i == 0):
                    $spreadsheet = $reader->loadFromString($pdfData);
                else:
                    $spreadsheet = $reader->loadFromString($pdfData,$spreadsheet);
                endif;

                $spreadsheet->getSheet($i)->setTitle($value);
                $excelSheet = $spreadsheet->getSheet($i);
                $hcol = $excelSheet->getHighestColumn();
                $hrow = $excelSheet->getHighestRow();
                $packFullRange = 'A1:' . $hcol . $hrow;
                foreach (range('A', $hcol) as $col):
                    $excelSheet->getColumnDimension($col)->setAutoSize(true);
                endforeach;
                $excelSheet->getStyle($packFullRange)->applyFromArray($borderStyle);
                $excelSheet->getStyle('A1:'.$hcol.'2')->applyFromArray($bgPrimary);
                $excelSheet->getStyle('A4:'.$hcol.'4')->applyFromArray($bgPrimary);
                $i++;
            endforeach;

            $fileDirectory = realpath(APPPATH . '../assets/uploads/gst_report');
            $fileName = '/gstr1_' . time() . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/gst_report') . $fileName);
        else:
            $data = $this->input->post();
            $data['report'] = "gstr1";
            $result = $this->{$data['report_type']}($data);
            $this->printJson($result);
        endif;
    }

    public function getGstr2Report($jsonData=''){        
        if(!empty($jsonData)):
            $data =(array) decodeURL($jsonData);

            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];
            $bgPrimary = [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DAEAFA'],
                ],
                'font' => ['bold' => true]
            ];

            $i = 0;$data['report'] = "gstr2";
            foreach($this->gstr2ReportTypes as $key => $value):
                $html = "";
                $html = $this->{$key}($data)['html'];                

                $pdfData = '<table>'.$html.'</table>';
                $pdfData = str_replace('&', '&amp;', $pdfData);
                
                $reader->setSheetIndex($i);

                if($i == 0):
                    $spreadsheet = $reader->loadFromString($pdfData);
                else:
                    $spreadsheet = $reader->loadFromString($pdfData,$spreadsheet);
                endif;

                $spreadsheet->getSheet($i)->setTitle($value);
                $excelSheet = $spreadsheet->getSheet($i);
                $hcol = $excelSheet->getHighestColumn();
                $hrow = $excelSheet->getHighestRow();
                $packFullRange = 'A1:' . $hcol . $hrow;
                foreach (range('A', $hcol) as $col):
                    $excelSheet->getColumnDimension($col)->setAutoSize(true);
                endforeach;
                $excelSheet->getStyle($packFullRange)->applyFromArray($borderStyle);
                $excelSheet->getStyle('A1:'.$hcol.'2')->applyFromArray($bgPrimary);
                $excelSheet->getStyle('A4:'.$hcol.'4')->applyFromArray($bgPrimary);
                $i++;
            endforeach;

            $fileDirectory = realpath(APPPATH . '../assets/uploads/gst_report');
            $fileName = '/gstr2_' . time() . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/gst_report') . $fileName);
        else:
            $data = $this->input->post();
            $data['report'] = "gstr2";
            $result = $this->{$data['report_type']}($data);
            $this->printJson($result);
        endif;
    }

    public function b2b($data){
        $data['vou_name_s'] = ($data['report'] == "gstr1")?"'Sale','GInc'":"'Purc','GExp'";
        $result = $this->gstReport->_b2b($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;
        $total_aigst_amount = $total_acgst_amount = $total_asgst_amount = $total_acess = 0;

        $html = '';
        if($data['report'] == "gstr1"):
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $invType = "Regular B2B";
                if($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies with payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies without payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "Deemed Exp";
                elseif(in_array($row->tax_class,["SALESIGSTACC","SALESJOBIGSTACC"])):
                    $invType = "Intra-State supplies attracting IGST";
                endif;

                $tbody .= '<tr>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                    <td class="text-center">N</td>
                    <td class="text-left"></td>
                    <td class="text-left">'.$invType.'</td>
                    <td class="text-left"></td>
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;

                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount = 0;
                $total_cgst_amount = 0;
                $total_sgst_amount = 0;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="13">Summary For B2B,SEZ,DE(4A,4B,6B,6C)</th>
                </tr>
                <tr>
                    <th>No. of Recipients</th>
                    <th></th>
                    <th>No. of Invoices</th>
                    <th></th>
                    <th>Total Invoice Value</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td>'.$no_of_recipients.'</td>
                    <td></td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                </tr>
                <tr>
                    <th>GSTIN/UIN of Recipient</th>
                    <th>Receiver Name</th>
                    <th>Invoice Number</th>
                    <th>Invoice date</th>
                    <th>Invoice Value</th>
                    <th>Place Of Supply</th>
                    <th>Reverse Charge</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Invoice Type</th>
                    <th>E-Commerce GSTIN</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        else:
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $invType = "Regular";
                if($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies with payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies without payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "Deemed Exp";
                endif;

                $isRc = (in_array($row->itc,["Capital Goods"]))?"Y":"N";

                $tbody .= '<tr>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                    <td class="text-center">'.$isRc.'</td>
                    <td class="text-left">'.$invType.'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                    <td class="text-left">'.$row->itc.'</td>
                    <td class="text-right">'.(($isRc == "Y")?sprintf("%.2F",$row->igst_amount):"0.00").'</td>
                    <td class="text-right">'.(($isRc == "Y")?sprintf("%.2F",$row->cgst_amount):"0.00").'</td>
                    <td class="text-right">'.(($isRc == "Y")?sprintf("%.2F",$row->sgst_amount):"0.00").'</td>
                    <td class="text-right">'.(($isRc == "Y")?sprintf("%.2F",$row->cess_amount):"0.00").'</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;

                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;

                $total_aigst_amount += ($isRc == "Y")?$row->igst_amount:0;
                $total_acgst_amount += ($isRc == "Y")?$row->cgst_amount:0;
                $total_asgst_amount += ($isRc == "Y")?$row->sgst_amount:0;
                $total_acess += ($isRc == "Y")?$row->cess_amount:0;
            endforeach;

            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="18">Summary Of Supplies From Registered Suppliers B2B(3)</th>
                </tr>
                <tr>
                    <th>No. of Recipients</th>
                    <th>No. of Invoices</th>
                    <th></th>
                    <th>Total Invoice Value</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Integrated Tax Paid</th>
                    <th>Total Central Tax Paid</th>
                    <th>Total State/UT Tax Paid</th>
                    <th>Total Cess Paid</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td>'.$no_of_recipients.'</td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_igst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_cgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_sgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_aigst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_acgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_asgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_acess).'</td>
                </tr>
                <tr>
                    <th>GSTIN of Supplier</th>
                    <th>Invoice Number</th>
                    <th>Invoice date</th>
                    <th>Invoice Value</th>
                    <th>Place Of Supply</th>
                    <th>Reverse Charge</th>
                    <th>Invoice Type</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Paid</th>
                    <th>Central Tax Paid</th>
                    <th>State/UT Tax Paid</th>
                    <th>Cess Paid</th>
                    <th>Eligibility For ITC</th>
                    <th>Availed ITC Integrated Tax</th>
                    <th>Availed ITC Central Tax</th>
                    <th>Availed ITC State/UT Tax</th>
                    <th>Availed ITC Cess</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';            
        endif;

        return ['status'=>1,'html'=>$html];
    }

    public function b2bur($data){
        $data['vou_name_s'] = "'Purc','GExp'";
        $result = $this->gstReport->_b2bur($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;
        $total_aigst_amount = $total_acgst_amount = $total_asgst_amount = $total_acess = 0;

        $html = '';
        $transMainId = array(); $tbody = '';
        foreach($result as $row):

            $tbody .= '<tr>
                <td class="text-left">'.$row->party_name.'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-center">'.date("d-M-Y",strtotime($row->doc_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                <td class="text-left">'.(($row->gst_type == 2)?"Intra State":"Inter State").'</td>
                <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left">'.$row->itc.'</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_recipients;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;

            $total_taxable_value += $row->taxable_amount;
            $total_igst_amount += $row->igst_amount;
            $total_cgst_amount += $row->cgst_amount;
            $total_sgst_amount += $row->sgst_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="17">Summary Of Supplies From Unregistered Suppliers B2BUR(4B)</th>
            </tr>
            <tr>
                <th>No. of Recipients</th>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Integrated Tax Paid</th>
                <th>Total Central Tax Paid</th>
                <th>Total State/UT Tax Paid</th>
                <th>Total Cess Paid</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>'.$no_of_recipients.'</td>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                <td>'.sprintf("%.2F",$total_igst_amount).'</td>
                <td>'.sprintf("%.2F",$total_cgst_amount).'</td>
                <td>'.sprintf("%.2F",$total_sgst_amount).'</td>
                <td>'.sprintf("%.2F",$total_cess).'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Supplier Name</th>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Supply Type</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Integrated Tax Paid</th>
                <th>Central Tax Paid</th>
                <th>State/UT Tax Paid</th>
                <th>Cess Paid</th>
                <th>Eligibility For ITC</th>
                <th>Availed ITC Integrated Tax</th>
                <th>Availed ITC Central Tax</th>
                <th>Availed ITC State/UT Tax</th>
                <th>Availed ITC Cess</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';

        return ['status'=>1,'html'=>$html];
    }

    public function b2ba($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th>Summary For B2BA</th>
                <th colspan="3">Original Details</th>
                <th colspan="11">Revised details</th>
            </tr>
            <tr>
                <th>No. of Recipients</th>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td>0</td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>GSTIN/UIN of Recipient</th>
                <th>Receiver Name</th>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Reverse Charge</th>
                <th>Applicable % of Tax Rate</th>
                <th>Invoice Type</th>
                <th>E-Commerce GSTIN</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function b2cl($data){
        $data['vou_name_s'] = "'Sale','GInc'";
        $result=$this->gstReport->_b2cl($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = $total_cess = 0;
        $transMainId = array(); $html = $tbody = '';
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                <td class="text-center">N</td>
                <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left"></td>
                <td class="text-left"></td>                
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_recipients;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;
            $total_taxable_value += $row->taxable_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="10">Summary For B2CL(5)</th>
            </tr>
            <tr>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                <td>'.sprintf("%.2F",$total_cess).'</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
                <th>Sale from Bonded WH</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';
        
        return ['status'=>1,'html'=>$html];
    }

    public function b2cla($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th>Summary For B2CLA</th>
                <th colspan="2">Original Details</th>
                <th colspan="9">Revised details</th>
            </tr>
            <tr>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Original Place Of Supply</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
                <th>Sale from Bonded WH</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function b2cs($data){
        $data['vou_name_s'] = "'Sale','GInc'";
        $result=$this->gstReport->_b2cs($data);

        $total_taxable_value = $total_cess = 0;
        $transMainId = array(); $html = $tbody = '';
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-left">OE</td>
                <td class="text-left">'.$row->party_state_code.' - '.$row->state_name.'</td>
                <td class="text-left"></td>
                <td class="text-right">'.$row->gst_per.'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left"></td>
            </tr>';

            $total_taxable_value += $row->taxable_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-info text-center">
            <tr>    
                <th colspan="7">Summary For B2CS(7)</th>
            </tr>
            <tr>    
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
            </tr>
            <tr>    
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                <td>'.sprintf("%.2F",$total_cess).'</td>
                <td></td>
            </tr>
            <tr>
                <th>Type</th>
                <th>Place Of Supply</th>
                <th>Applicable % Of Tax</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commarce GSTIN</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';

        return ['status'=>1,'html'=>$html];
    }

    public function b2csa($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th>Summary For B2CSA</th>
                <th>Original Details</th>
                <th colspan="7">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td></td>
            </tr>
            <tr>
                <th>Financial Year</th>
                <th>Original Month</th>
                <th>Place Of Supply</th>
                <th>Type</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function cdnr($data){      
        $data['vou_name_s'] = "'C.N.','D.N.'";
        $result = $this->gstReport->_cdnr($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;

        $html = '';
        if($data['report'] == "gstr1"):
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $invType = "Regular B2B";
                if($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies with payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "SEZ supplies without payment";
                elseif($row->tax_class == "SEZSGSTACC"):
                    $invType = "Deemed Exp";
                elseif(in_array($row->tax_class,["SALESIGSTACC","SALESJOBIGSTACC"])):
                    $invType = "Intra-State supplies attracting IGST";
                endif;
                
                $tbody .= '<tr>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-center">'.$row->trans_date.'</td>
                    <td class="text-center">'.(($row->vou_name_s == "C.N.")?"C":"D").'</td>
                    <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                    <td class="text-center">N</td>
                    <td class="text-left">'.$invType.'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-center">0.00</td>  
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;
    
                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="13">Summary For CDNR(9B)</th>
                </tr>
                <tr>
                    <th>No. of Recipient</th>
                    <th></th>
                    <th>No. of Notes</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Note Value</th>
                    <th></th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td>'.$no_of_recipients.'</td>
                    <td></td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                </tr>
                <tr>
                    <th>GSTIN/UIN of Recipient</th>
                    <th>Receiver Name</th>
                    <th>Note Number</th>
                    <th>Note Date</th>
                    <th>Note Type</th>
                    <th>Place Of Supply</th>
                    <th>Reverse Charge</th>
                    <th>Note Supply Type</th>
                    <th>Note Value</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        else:            
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $tbody .= '<tr>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-left">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                    <td class="text-center">'.$row->doc_no.'</td>
                    <td class="text-center">'.((!empty($row->doc_date))?date("d-M-Y",strtotime($row->doc_date)):"").'</td>
                    <td class="text-center">N</td>
                    <td class="text-center">'.(($row->vou_name_s == "C.N.")?"C":"D").'</td>
                    <td class="text-center">07-Others</td>
                    <td class="text-center">'.(($row->gst_type == 2)?"Intra State":"Inter State").'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                    <td class="text-center"></td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;
    
                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="21">Summary For CDNR(6C)</th>
                </tr>
                <tr>
                    <th>No. of Recipient</th>
                    <th>No. of Notes</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Note/Refund Voucher Value</th>
                    <th></th>
                    <th>Total Taxable Value</th>
					<th>Total Integrated Tax Paid</th>
                    <th>Total Central Tax Paid</th>
                    <th>Total State/UT Tax Paid</th>
                    <th>Total Cess</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td>'.$no_of_recipients.'</td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_invoice_value).'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_igst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_cgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_sgst_amount).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>GSTIN of Supplier</th>
                    <th>Note/Refund Voucher Number</th>
                    <th>Note/Refund Voucher date</th>
                    <th>Invoice/Advance Payment Voucher Number</th>
                    <th>Invoice/Advance Payment Voucher date</th>
                    <th>Pre GST</th>
                    <th>Document Type</th>
                    <th>Reason For Issuing document</th>
                    <th>Supply Type</th>
                    <th>Note/Refund Voucher Value</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Paid</th>
                    <th>Central Tax Paid</th>
                    <th>State/UT Tax Paid</th>
                    <th>Cess Paid</th>
                    <th>Eligibility For ITC</th>
                    <th>Availed ITC Integrated Tax</th>
                    <th>Availed ITC Central Tax</th>
                    <th>Availed ITC State/UT Tax</th>
                    <th>Availed ITC Cess</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        endif;
        
        return ['status'=>1,'html'=>$html];
    }

    public function cdnra($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For CDNRA</th>
                <th colspan="5" class="text-center">Original Details</th>
                <th colspan="9" class="text-center">Revised details</th>
            </tr>
            <tr>
                <th>No. of Recipient</th>
                <th></th>
                <th>No. of Notes/Vouchers</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td>0</td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>GSTIN/UIN of Recipient</th>
                <th>Receiver Name</th>
                <th>Original Note Number</th>
                <th>Original Note Date</th>
                <th>Revised Note Number</th>
                <th>Revised Note Date</th>
                <th>Note Type</th>
                <th>Place Of Supply</th>
                <th>Reverse Charge</th>
                <th>Note Supply Type</th>
                <th>Note Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function cdnur($data){
        $data['vou_name_s'] = "'C.N.','D.N.'";
        $result = $this->gstReport->_cdnur($data);

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;

        $html = '';
        if($data['report'] == "gstr1"):
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $urType = "B2CL";
                if($row->tax_class == "EXPORTGSTACC"):
                    $invType = "EXPWP";
                elseif($row->tax_class == "EXPORTTFACC"):
                    $invType = "EXPWOP";
                endif;

                $tbody .= '<tr>
                    <td class="text-left">'.$urType.'</td>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-center">'.$row->trans_date.'</td>
                    <td class="text-center">'.(($row->entry_type == "C.N.")?"C":"D").'</td>
                    <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-center">N</td>  
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;
    
                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="10">Summary For CDNUR(9B)</th>
                </tr>
                <tr>
                    <th></th>
                    <th>No. of Notes</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Note Value</th>
                    <th></th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td></td>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.$total_invoice_value.'</td>
                    <td></td>
                    <td></td>
                    <td>'.$total_taxable_value.'</td>
                    <td>'.$total_cess.'</td>
                </tr>
                <tr>
                    <th>UR Type</th>
                    <th>Note Number</th>
                    <th>Note Date</th>
                    <th>Note Type</th>
                    <th>Place Of Supply</th>
                    <th>Note Value</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        else:
            $transMainId = array(); $tbody = '';
            foreach($result as $row):
                $tbody .= '<tr>
                    <td class="text-left">'.$row->trans_number.'</td>
                    <td class="text-left">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                    <td class="text-center">'.$row->doc_no.'</td>
                    <td class="text-center">'.((!empty($row->doc_date))?date("d-M-Y",strtotime($row->doc_date)):"").'</td>
                    <td class="text-center">N</td>
                    <td class="text-center">'.(($row->entry_type == 13)?"C":"D").'</td>
                    <td class="text-center">07-Others</td>
                    <td class="text-center">'.(($row->gst_type == 2)?"Intra State":"Inter State").'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->sgst_amount).'</td>
                    <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                    <td class="text-center">Ineligible</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                    <td class="text-center">0.00</td>
                </tr>';

                if(!in_array($row->id,$transMainId)):
                    $transMainId[] = $row->id;
                    ++$no_of_recipients;
                    ++$no_of_invoice;
                    $total_invoice_value += $row->net_amount;
                endif;
    
                $total_taxable_value += $row->taxable_amount;
                $total_igst_amount += $row->igst_amount;
                $total_cgst_amount += $row->cgst_amount;
                $total_sgst_amount += $row->sgst_amount;
                $total_cess += $row->cess_amount;
            endforeach;

            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="20">Summary For CDNUR(6C)</th>
                </tr>
                <tr>
                    <th>No. of Vouchers</th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th>Total Note/Voucher Value</th>                    
                    <th></th>                    
                    <th>Total Taxable Value</th>
					<th>Total Integrated Tax Paid</th>
                    <th>Total Central Tax Paid</th>
                    <th>Total State/UT Tax Paid</th>
                    <th>Total Cess</th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                    <th></th>                    
                </tr>
                <tr>
                    <td>'.$no_of_invoice.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.$total_invoice_value.'</td>
                    <td></td>
                    <td>'.$total_taxable_value.'</td>
                    <td>'.$total_igst_amount.'</td>
                    <td>'.$total_cgst_amount.'</td>
                    <td>'.$total_sgst_amount.'</td>
                    <td>'.$total_cess.'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Note/Voucher Number</th>
                    <th>Note/Voucher date</th>
                    <th>Invoice/Advance Payment Voucher number</th>
                    <th>Invoice/Advance Payment Voucher date</th>
                    <th>Pre GST</th>
                    <th>Document Type</th>
                    <th>Reason For Issuing document</th>
                    <th>Supply Type</th>
                    <th>Note/Voucher Value</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Paid</th>
                    <th>Central Tax Paid</th>
                    <th>State/UT Tax Paid</th>
                    <th>Cess Paid</th>
                    <th>Eligibility For ITC</th>
                    <th>Availed ITC Integrated Tax</th>
                    <th>Availed ITC Central Tax</th>
                    <th>Availed ITC State/UT Tax</th>
                    <th>Availed ITC Cess</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function cdnura($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For CDNURA</th>
                <th colspan="4" class="text-center">Original Details</th>
                <th colspan="7" class="text-center">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Notes/Vouchers</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>UR Type</th>
                <th>Original Note Number</th>
                <th>Original Note Date</th>
                <th>Revised Note Number</th>
                <th>Revised Note Date</th>
                <th>Note Type</th>
                <th>Place Of Supply</th>
                <th>Note Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function exp($data){
        $data['vou_name_s'] = "'Sale'";
        $result=$this->gstReport->_exp($data);

        $no_of_invoice = $total_invoice_value = $total_taxable_value = $total_cess = 0;
        $transMainId = array(); $html = $tbody = '';
        foreach($result as $row):
            $tbody .= '<tr>
                <td class="text-left">'.(($row->tax_class == "EXPORTGSTACC")?"WPAY":"WOPAY").'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-left">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.$row->port_code.'</td>
                <td class="text-left">'.$row->ship_bill_no.'</td>
                <td class="text-left">'.date("d-M-Y",strtotime($row->ship_bill_date)).'</td>
                <td class="text-left">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;

            $total_taxable_value += $row->taxable_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="10">Summary For EXP(6)</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th>No. of Shipping Bill</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td>'.$total_invoice_value.'</td>
                <td></td>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td></td>
                <td>'.$total_taxable_value.'</td>
                <td>'.$total_cess.'</td>
            </tr>
            <tr>
                <th>Export Type</th>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Port Code</th>
                <th>Shipping Bill Number</th>
                <th>Shipping Bill Date</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function expa($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For EXP(6)</th>
                <th colspan="2">Original Details</th>
                <th colspan="9">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th>No. of Shipping Bill</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>Export Type</th>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Port Code</th>
                <th>Shipping Bill Number</th>
                <th>Shipping Bill Date</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function at($data){
        $result = array();

        $html = '';
        if($data['report'] == "gstr1"):
            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="5">Summary For Advance Received (11B)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Advanced Received</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>0.00</td>
                    <td>0.00</td>
                </tr>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Rate</th>
                    <th>Gross Advance Received</th>
                    <th>Cess Amount</th>
                </tr>
            </thead>';
        else:
            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="4">Summary For  Tax Liability on Advance Paid  under reverse charge(10 A)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Total Gross Advance Paid</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>0.00</td>
                    <td>0.00</td>
                </tr>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Rate</th>
                    <th>Gross Advance Paid</th>
                    <th>Cess Amount</th>
                </tr>
            </thead>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function ata($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For Amended Tax Liability(Advance Received)</th>
                <th colspan="2">Original Details</th>
                <th colspan="4">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Advanced Received</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>Financial Year  </th>
                <th>Original Month</th>
                <th>Original Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Gross Advance Received</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function atadj($data){
        $html = '';

        if($data['report'] == "gstr1"):
            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="5" class="text-center">Summary For Advance Adjusted (11B)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Advanced Adjusted</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Rate</th>
                    <th>Gross Advance Adjusted</th>
                    <th>Cess Amount</th>
                </tr>
            </thead>';
        else:
            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="4" class="text-center">Summary For Adjustment of advance tax paid earlier for reverse charge supplies (10 B)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Total Gross Advance Paid to be Adjusted</th>
                    <th>Total Cess Adjusted</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>0.00</td>
                    <td>0.00</td>
                </tr>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Rate</th>
                    <th>Gross Advance Paid to be Adjusted</th>
                    <th>Cess Adjusted</th>
                </tr>
            </thead>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function atadja($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For Amendement Of Adjustment Advances</th>
                <th colspan="2">Original Details</th>
                <th colspan="4">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Advanced Adjusted</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>Financial Year  </th>
                <th>Original Month</th>
                <th>Original Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Gross Advance Adjusted</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function exemp($data){        
        $result = $this->gstReport->_exemp($data);
        
        $totalComposition = $totalNillRated = $totaExempted = $totalNonGst = 0;
        $html = $tbody = '';
        if($data['report'] == "gstr1"):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$row->description.'</td>
                    <td>'.sprintf("%.2F",$row->nill_rate_amount).'</td>
                    <td>'.sprintf("%.2F",$row->exe_amount).'</td>
                    <td>'.sprintf("%.2F",$row->non_gst_amount).'</td>
                </tr>';

                $totalNillRated += $row->nill_rate_amount;
                $totaExempted += $row->exe_amount;
                $totalNonGst += $row->non_gst_amount;
            endforeach;

            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="5">Summary For Nil rated, exempted and non GST outward supplies (8)</th>
                </tr>
                <tr>
                    <th></th>
                    <th>Total Nil Rated Supplies</th>
                    <th>Total Exempted Supplies</th>
                    <th>Total Non-GST Supplies</th>
                </tr>
                <tr>
                    <td></td>
                    <td>'.$totalNillRated.'</td>
                    <td>'.$totaExempted.'</td>
                    <td>'.$totalNonGst.'</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <th>Nil Rated Supplies</th>
                    <th>Exempted(other than nil rated/non GST supply)</th>
                    <th>Non-GST supplies</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        else:
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$row->description.'</td>
                    <td>'.sprintf("%.2F",$row->compo_amount).'</td>
                    <td>'.sprintf("%.2F",$row->nill_rate_amount).'</td>
                    <td>'.sprintf("%.2F",$row->exe_amount).'</td>
                    <td>'.sprintf("%.2F",$row->non_gst_amount).'</td>
                </tr>';

                $totalComposition += $row->compo_amount;
                $totalNillRated += $row->nill_rate_amount;
                $totaExempted += $row->exe_amount;
                $totalNonGst += $row->non_gst_amount;
            endforeach;

            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="5">Summary For Composition,Nil rated,exempted and non GST inward supplies</th>
                </tr>
                <tr>
                    <th></th>
                    <th>Total Composition taxable person</th>
                    <th>Total Nil Rated Supplies</th>
                    <th>Total Exempted Supplies</th>
                    <th>Total Non-GST Supplies</th>
                </tr>
                <tr>
                    <td></td>
                    <td>'.$totalComposition.'</td>
                    <td>'.$totalNillRated.'</td>
                    <td>'.$totaExempted.'</td>
                    <td>'.$totalNonGst.'</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <th>Composition taxable person</th>
                    <th>Nil Rated Supplies</th>
                    <th>Exempted (other than nil rated/non GST supply )</th>
                    <th>Non-GST supplies</th>
                </tr>
            </thead><tbody>'.$tbody.'</tbody>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function hsn($data){
        $data['vou_name_s'] = ($data['report'] == "gstr1")?"'Sale','GInc','C.N.','D.N.'":"'Purc','GExp','C.N.','D.N.'";
        $result=$this->gstReport->_hsn($data);

        $total_taxable_value = 0;
        $total_value = 0;
        $total_cgst = 0;
        $total_sgst = 0;
        $total_igst = 0;
        $total_cess = 0;

        if(!empty($result)):
            $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
            $total_value = array_sum(array_column($result,'net_amount'));
            $total_cgst = array_sum(array_column($result,'cgst_amount'));
            $total_sgst = array_sum(array_column($result,'sgst_amount'));
            $total_igst = array_sum(array_column($result,'igst_amount'));       
            $total_cess = array_sum(array_column($result,'cess_amount')); 
        endif;

        $html = '';
        if($data['report'] == "gstr1"):
            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="11">Summary For HSN(12)</th>
                </tr>
                <tr>
                    <th>No. of HSN</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Value</th>
                    <th></th>
                    <th>Total Taxable Value</th>
                    <th>Total Integrated Tax</th>
                    <th>Total Central Tax</th>
                    <th>Total State/UT Tax</th>
                    <th>Total Cess</th>
                </tr>
                <tr>
                    <td>'.count($result).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_value).'</td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_igst).'</td>
                    <td>'.sprintf("%.2F",$total_cgst).'</td>
                    <td>'.sprintf("%.2F",$total_sgst).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                </tr>
                <tr>
                    <th>HSN</th>
                    <th>Description</th>
                    <th>UQC</th>
                    <th>Total Quantity</th>
                    <th>Total Value</th>
                    <th>Rate</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Amount</th>
                    <th>Central Tax Amount</th>
                    <th>State/UT Tax Amount</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>';

            foreach($result as $row):
                $html .= '<tr>
                <td>'.$row->hsn_code.'</td>
                <td></td>
                <td>'.$row->unit_name.' - '.$row->unit_description.'</td>
                <td>'.$row->qty.'</td>
                <td>'.sprintf("%.2F",$row->net_amount).'</td>
                <td>'.sprintf("%.2F",$row->gst_per).'</td>
                <td>'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td>'.sprintf("%.2F",$row->igst_amount).'</td>
                <td>'.sprintf("%.2F",$row->cgst_amount).'</td>
                <td>'.sprintf("%.2F",$row->sgst_amount).'</td>
                <td>'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';
            endforeach;
            $html .='</tbody>';
        else:
            $html .= '<thead class="thead-info text-center">
                <tr>
                    <th colspan="10">Summary For HSN(13)</th>
                </tr>
                <tr>
                    <th>No. of HSN</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Value</th>
                    <th>Total Taxable Value</th>
                    <th>Total Integrated Tax Amount</th>
                    <th>Total Central Tax Amount</th>
                    <th>Total State/UT Tax Amount</th>
                    <th>Total Cess Amount</th>
                </tr>
                <tr>
                    <td>'.count($result).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.sprintf("%.2F",$total_value).'</td>
                    <td>'.sprintf("%.2F",$total_taxable_value).'</td>
                    <td>'.sprintf("%.2F",$total_igst).'</td>
                    <td>'.sprintf("%.2F",$total_cgst).'</td>
                    <td>'.sprintf("%.2F",$total_sgst).'</td>
                    <td>'.sprintf("%.2F",$total_cess).'</td>
                </tr>
                <tr>
                    <th>HSN</th>
                    <th>Description</th>
                    <th>UQC</th>
                    <th>Total Quantity</th>
                    <th>Total Value</th>
                    <th>Taxable Value</th>
                    <th>Integrated Tax Amount</th>
                    <th>Central Tax Amount</th>
                    <th>State/UT Tax Amount</th>
                    <th>Cess Amount</th>
                </tr>
            </thead><tbody>';

            foreach($result as $row):
                $html .= '<tr>
                <td>'.$row->hsn_code.'</td>
                <td></td>
                <td>'.$row->unit_name.' - '.$row->unit_description.'</td>
                <td>'.$row->qty.'</td>
                <td>'.sprintf("%.2F",$row->net_amount).'</td>
                <td>'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td>'.sprintf("%.2F",$row->igst_amount).'</td>
                <td>'.sprintf("%.2F",$row->cgst_amount).'</td>
                <td>'.sprintf("%.2F",$row->sgst_amount).'</td>
                <td>'.sprintf("%.2F",$row->cess_amount).'</td>
                </tr>';
            endforeach;
            $html .='</tbody>';
        endif;
        return ['status'=>1,'html'=>$html];
    }

    public function docs($data){
        $data['vou_name_s']="'Sales','GInc','C.N.','D.N.'";//'6,7,8,13,14,19';//6,7,8,13,14
        $result=$this->gstReport->_docs($data);
        $total_number = 0;
        $total_cancelled = 0;

        if(!empty($result)):
            $total_number = (!empty($result))?array_sum(array_column($result,'total_inv')):0;
            $total_cancelled = (!empty($result))?array_sum(array_column($result,'total_cnl_inv')):0;
        endif;

        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="5">Summary of documents issued during the tax period(13)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Number</th>
                <th>Total Cancelled</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_number.'</td>
                <td>'.$total_cancelled.'</td>
            </tr>
            <tr>
                <th>Nature of Document</th>
                <th>Sr. No. From</th>
                <th>Sr. No. To</th>
                <th>Total Number</th>
                <th>Cancelled</th>
            </tr>
        </thead>';
        foreach($result as $row):
            $html .= '<tr>
                <td class="text-left">'.$row->document.'</td>
                <td class="text-left">'.$row->trans_prefix.$row->min_trans_no.'</td>
                <td class="text-left">'.$row->trans_prefix.$row->max_trans_no.'</td>
                <td class="text-right">'.$row->total_inv.'</td>
                <td class="text-left">'.$row->total_cnl_inv.'</td>
            </tr>';
        endforeach;
        return ['status'=>1,'html'=>$html];
    }

    public function imps($data){
        $data['vou_name_s'] = "'Purc','GExp'";
        $result=$this->gstReport->_imps($data);
        $html = '';

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;
        $total_aigst_amount = $total_acgst_amount = $total_asgst_amount = $total_acess = 0;

        $html = '';
        $transMainId = array(); $tbody = '';
        foreach($result as $row):

            $tbody .= '<tr>
                <td class="text-center">'.$row->trans_number.'</td>
                <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.$row->party_state_code.'-'.$row->state_name.'</td>
                <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left">'.$row->itc.'</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_recipients;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;

            $total_taxable_value += $row->taxable_amount;
            $total_igst_amount += $row->igst_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="11">Summary For IMPS(4C)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
				<th>Total Integrated Tax Paid</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>'.$total_invoice_value.'</td>
                <td></td>
                <td></td>
                <td>'.$total_taxable_value.'</td>
                <td>'.$total_igst_amount.'</td>
                <td>'.$total_cess.'</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Invoice Number of Reg Recipient</th>
                <th>Invoice Date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Integrated Tax Paid</th>
                <th>Cess Paid</th>
                <th>Eligibility For ITC</th>
                <th>Availed ITC Integrated Tax</th>
                <th>Availed ITC Cess</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';

        return ['status'=>1,'html'=>$html];
    }

    public function impg($data){
        $data['vou_name_s'] = "'Purc','GExp'";
        $result=$this->gstReport->_impg($data);
        $html = '';

        $no_of_recipients = $no_of_invoice = $total_invoice_value = $total_taxable_value = 0;
        $total_igst_amount = $total_cgst_amount = $total_sgst_amount = $total_cess = 0;
        $total_aigst_amount = $total_acgst_amount = $total_asgst_amount = $total_acess = 0;

        $html = '';
        $transMainId = array(); $tbody = '';
        foreach($result as $row):

            $tbody .= '<tr>
                <td class="text-left">'.$row->port_code.'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-left">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->net_amount).'</td>
                <td class="text-left">'.(($row->tax_class == "IMPORTACC")?"Imports":"Received from SEZ").'</td>
                <td class="text-left">'.$row->gstin.'</td>
                <td class="text-right">'.sprintf("%.2F",$row->gst_per).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->taxable_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->igst_amount).'</td>
                <td class="text-right">'.sprintf("%.2F",$row->cess_amount).'</td>
                <td class="text-left">'.$row->itc.'</td>
                <td class="text-right">0.00</td>
                <td class="text-right">0.00</td>
            </tr>';

            if(!in_array($row->id,$transMainId)):
                $transMainId[] = $row->id;
                ++$no_of_recipients;
                ++$no_of_invoice;
                $total_invoice_value += $row->net_amount;
            endif;

            $total_taxable_value += $row->taxable_amount;
            $total_igst_amount += $row->igst_amount;
            $total_cess += $row->cess_amount;
        endforeach;

        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="13">Summary For IMPG(5)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>                
                <th></th>
                <th>Total Bill Of Entry Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
				<th>Total Integrated Tax Paid</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_invoice_value.'</td>
                <td></td>
                <td></td>
                <td>'.$total_taxable_value.'</td>
                <td>'.$total_igst_amount.'</td>
                <td>'.$total_cess.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Port Code</th>
                <th>Bill Of Entry Number</th>
                <th>Bill Of Entry Date</th>
                <th>Bill Of Entry Value</th>
                <th>Document type</th>
                <th>GSTIN Of SEZ Supplier</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Integrated Tax Paid</th>
                <th>Cess Paid</th>
                <th>Eligibility For ITC</th>
                <th>Availed ITC Integrated Tax</th>
                <th>Availed ITC Cess</th>
            </tr>
        </thead><tbody>'.$tbody.'</tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function itcr($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="6">Summary For Input Tax credit Reversal/Reclaim(11)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th>Total ITC Integrated Tax Amount</th>
                <th>Total ITC Central Tax Amount</th>
                <th>Total ITC State/UT Tax Amount</th>
                <th>Total ITC Cess Amount</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <th>Description for reversal of ITC</th>
                <th>To be added or reduced from output liability</th>
                <th>ITC Integrated Tax Amount</th>
                <th>ITC Central Tax Amount</th>
                <th>ITC State/UT Tax Amount</th>
                <th>ITC Cess Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Amount in terms of Rule 2(2) of ITC Rule</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Amount in terms of rule 7 (1) (m)of ITC</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Amount in terms of rule 8(1) (h) of the</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Amount in terms of rule 7 (2)(a) of ITC</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Amount in terms of rule 7(2)(b) of ITC R</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>On account of amount paid subsequent to</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>Any other liability</td>
                <td></td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
                <td>0.00</td>
            </tr>
        </tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function gstr2bMatch(){
        $this->data['headData']->pageTitle = "GSTR 2B Match";
        $this->data['pageHeader'] = 'GSTR 2B Match';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->gstr2b_match, $this->data);
    }

    public function matchGstr2bData(){
        $data = $this->input->post();
        if(empty($data['from_date']))
            $errorMessage["from_date"] = "From date is required.";
        if(empty($data['to_date']))
            $errorMessage['to_date'] = "To Date is required.";
        if(!empty($data['from_date']) && !empty($data['to_date'])):
            if($data['from_date'] > $data['to_date']):
                $errorMessage['from_date'] = "From date invalid.";
            endif;
        endif;

        if(empty($_FILES['json_file']['tmp_name']) || $_FILES['json_file']['tmp_name'] == null):
            $errorMessage['json_file'] = "Please select GSTR 2 json file.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $filePath = $_FILES['json_file']['tmp_name'];

            // Read the JSON file
            $jsonData = file_get_contents($filePath);

            // Parse the JSON data
            $gstr2Data = json_decode($jsonData, false);
            

            $data['report'] = "gstr2"; $tbody = '';
            if($gstr2Data !== null):
                foreach($this->gstr2ReportTypes as $key=>$val):
                    if(in_array($key,['b2b'])):

                        if(in_array($key,['b2b','b2bur','imps','impg'])):
                            $data['vou_name_s'] = "'Purc','GExp'";
                        elseif(in_array($key,['cdnr','cdnur'])):
                            $data['vou_name_s'] = "'C.N.','D.N.'";
                        endif;

                        $result = $this->gstReport->{"_".$key}($data);

                        $jsonData = (isset($gstr2Data->data->docdata->{$key}))?$gstr2Data->data->docdata->{$key}:array();

                        $jsonDocData = array();
                        foreach($jsonData as $row):
                            foreach($row->inv as $inv):
                                foreach($inv->items as $item):
                                    $jsonRow = [
                                        'trdnm' => $row->trdnm,
                                        'ctin' => $row->ctin,

                                        'dt' => $inv->dt,
                                        'val' => $inv->val,
                                        'rev' => $inv->rev,
                                        'itcavl' => $inv->itcavl,
                                        'diffprcnt' => $inv->diffprcnt,
                                        'pos' => $inv->pos,
                                        'typ' => $inv->typ,
                                        'inum' => $inv->inum,
                                        'rsn' => $inv->rsn,

                                        'txval' => (isset($item->txval))?$item->txval:0,
                                        'rt' => (isset($item->rt))?$item->rt:0,
                                        'num' => (isset($item->num))?$item->num:0,
                                        'cgst' => (isset($item->cgst))?$item->cgst:0,
                                        'sgst' => (isset($item->sgst))?$item->sgst:0,
                                        'igst' => (isset($item->igst))?$item->igst:0,
                                        'cess' => (isset($item->cess))?$item->cess:0
                                    ];

                                    $jsonDocData[] = (object) $jsonRow;
                                endforeach;
                            endforeach;
                        endforeach;

                        $jsonData = (object) $jsonDocData;
                        
                        if(!empty($result) || !empty($jsonData)):
                            if(count((array)$result) > count((array)$jsonData)):
                                //print_r("ok");exit;
                                foreach($result as $resultRow):
                                    $tbodyHtml = '';
                                    $matchingRow = array();$bgColor="none";$matchingStatus = false;
                                    foreach($jsonData as $jsonRow):
                                        if (
                                            trim($resultRow->gstin) == trim($jsonRow->ctin) &&
                                            trim(strtoupper($resultRow->party_name)) == trim(strtoupper($jsonRow->trdnm)) &&
                                            floatVal($resultRow->net_amount) == floatVal($jsonRow->val) &&
                                            floatVal($resultRow->taxable_amount) == floatVal($jsonRow->txval) &&
                                            floatVal($resultRow->cgst_amount) == floatVal($jsonRow->cgst) &&
                                            floatVal($resultRow->sgst_amount) == floatVal($jsonRow->sgst) &&
                                            floatVal($resultRow->igst_amount) == floatVal($jsonRow->igst) &&
                                            floatVal($resultRow->cess_amount) == floatVal($jsonRow->cess) &&
                                            trim($resultRow->trans_number) == trim($jsonRow->inum) &&
                                            formatDate($resultRow->trans_date) == formatDate($jsonRow->dt)
                                        ):
                                            $matchingStatus = true; 
                                        endif;

                                        if(
                                            trim($resultRow->gstin) == trim($jsonRow->ctin) &&
                                            trim($resultRow->trans_number) == trim($jsonRow->inum) &&
                                            formatDate($resultRow->trans_date) == formatDate($jsonRow->dt)
                                        ):
                                            $matchingRow = $jsonRow;
                                            break;
                                        endif;

                                        
                                    endforeach;

                                    $bgColor = ($matchingStatus == true) ? "#cdffcd" : "#fdbec4";
                                    $status = ($matchingStatus == true) ? "Matched" : "Unmatched";
                                    $isRc = (in_array($resultRow->itc,["Capital Goods"]))?"Y":"N";
                                    $invType = "Regular";
                                    if($resultRow->tax_class == "SEZSGSTACC"):
                                        $invType = "SEZ supplies with payment";
                                    elseif($resultRow->tax_class == "SEZSGSTACC"):
                                        $invType = "SEZ supplies without payment";
                                    elseif($resultRow->tax_class == "SEZSGSTACC"):
                                        $invType = "Deemed Exp";
                                    endif;

                                    //Erp Data                                    
                                    $tbodyHtml .= '<tr style="background-color: '.$bgColor.'; ">
                                        <td>'.$status.'</td>
                                        <td>'.strtoupper($key).'</td>
                                        <td>'.$resultRow->gstin.'</td>
                                        <td>'.$resultRow->party_name.'</td>
                                        <td>'.$resultRow->trans_number.'</td>
                                        <td>'.formatDate($resultRow->trans_date).'</td>
                                        <td>'.$resultRow->net_amount.'</td>
                                        <td>'.$isRc.'</td>
                                        <td>'.$resultRow->party_state_code.'</td>
                                        <td>'.$invType.'</td>
                                        <td>'.$resultRow->taxable_amount.'</td>
                                        <td>'.$resultRow->gst_per.'</td>
                                        <td>'.$resultRow->cgst_amount.'</td>
                                        <td>'.$resultRow->sgst_amount.'</td>
                                        <td>'.$resultRow->igst_amount.'</td>
                                        <td>'.$resultRow->cess_amount.'</td>
                                    </tr>';
                                    
                                    //Json Data
                                    if(!empty($matchingRow)):
                                        $tbodyHtml .= '<tr style="background-color: '.$bgColor.'; ">
                                            <td>'.$status.'</td>
                                            <td>'.strtoupper($key).'</td>
                                            <td>'.$matchingRow->ctin.'</td>
                                            <td>'.$matchingRow->trdnm.'</td>
                                            <td>'.$matchingRow->inum.'</td>
                                            <td>'.formatDate($matchingRow->dt).'</td>
                                            <td>'.$matchingRow->val.'</td>
                                            <td>'.$matchingRow->rev.'</td>
                                            <td>'.$matchingRow->pos.'</td>
                                            <td>'.$matchingRow->typ.'</td>
                                            <td>'.$matchingRow->txval.'</td>
                                            <td>'.$matchingRow->rt.'</td>
                                            <td>'.$matchingRow->cgst.'</td>
                                            <td>'.$matchingRow->sgst.'</td>
                                            <td>'.$matchingRow->igst.'</td>
                                            <td>'.$matchingRow->cess.'</td>
                                        </tr>';
                                    else:
                                        $tbodyHtml .= '<tr style="background-color: '.$bgColor.'; ">
                                            <td style="height:20px;">NOT FOUND IN JSON</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>';
                                    endif;

                                    //blank line
                                    $bgColor = "none";
                                    $tbodyHtml .= '<tr style="background-color: '.$bgColor.'; ">
                                        <td style="height:20px;"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';

                                    if($data['match_status'] == 1 && $matchingStatus == false):
                                        $tbodyHtml = '';
                                        $tbody .= $tbodyHtml;
                                    elseif($data['match_status'] == 2 && $matchingStatus == true):
                                        $tbodyHtml = '';
                                        $tbody .= $tbodyHtml;
                                    else:
                                        $tbody .= $tbodyHtml;
                                    endif;
                                endforeach;

                            elseif(count((array)$result) < count((array)$jsonData)):
                                //print_r(count((array)$jsonData));exit;
                                foreach($jsonData as $jsonRow):
                                    $tbodyHtml = '';
                                    $matchingRow = array();$bgColor="none";$matchingStatus = false;
                                    foreach($result as $resultRow):
                                        if (
                                            trim($resultRow->gstin) == trim($jsonRow->ctin) &&
                                            trim(strtoupper($resultRow->party_name)) == trim(strtoupper($jsonRow->trdnm)) &&
                                            floatVal($resultRow->net_amount) == floatVal($jsonRow->val) &&
                                            floatVal($resultRow->taxable_amount) == floatVal($jsonRow->txval) &&
                                            floatVal($resultRow->cgst_amount) == floatVal($jsonRow->cgst) &&
                                            floatVal($resultRow->sgst_amount) == floatVal($jsonRow->sgst) &&
                                            floatVal($resultRow->igst_amount) == floatVal($jsonRow->igst) &&
                                            floatVal($resultRow->cess_amount) == floatVal($jsonRow->cess) &&
                                            trim($resultRow->trans_number) == trim($jsonRow->inum) &&
                                            formatDate($resultRow->trans_date) == formatDate($jsonRow->dt)
                                        ):
                                            $matchingStatus = true;                                            
                                        endif;  
                                        
                                        if(
                                            trim($resultRow->gstin) == trim($jsonRow->ctin) &&
                                            trim($resultRow->trans_number) == trim($jsonRow->inum) &&
                                            formatDate($resultRow->trans_date) == formatDate($jsonRow->dt)
                                        ):
                                            $matchingRow = $resultRow;
                                            break;
                                        endif;
                                    endforeach;

                                    $bgColor = ($matchingStatus == true) ? "#cdffcd" : "#fdbec4";
                                    $status = ($matchingStatus == true) ? "Matched" : "Unmatched";
                                    $isRc = (in_array($matchingRow->itc,["Capital Goods"]))?"Y":"N";
                                    $invType = "Regular";
                                    if($matchingRow->tax_class == "SEZSGSTACC"):
                                        $invType = "SEZ supplies with payment";
                                    elseif($matchingRow->tax_class == "SEZSGSTACC"):
                                        $invType = "SEZ supplies without payment";
                                    elseif($matchingRow->tax_class == "SEZSGSTACC"):
                                        $invType = "Deemed Exp";
                                    endif;

                                    //Erp Data
                                    if(!empty($matchingRow)):
                                        $tbodyHtml .= '<tr style="background-color: '.$bgColor.'; ">
                                            <td>'.$status.'</td>
                                            <td>'.strtoupper($key).'</td>
                                            <td>'.$matchingRow->gstin.'</td>
                                            <td>'.$matchingRow->party_name.'</td>
                                            <td>'.$matchingRow->trans_number.'</td>
                                            <td>'.formatDate($matchingRow->trans_date).'</td>
                                            <td>'.floatVal($matchingRow->net_amount).'</td>
                                            <td>'.$isRc.'</td>
                                            <td>'.$matchingRow->party_state_code.'</td>
                                            <td>'.$invType.'</td>
                                            <td>'.floatVal($matchingRow->taxable_amount).'</td>
                                            <td>'.floatVal($matchingRow->gst_per).'</td>
                                            <td>'.floatVal($matchingRow->cgst_amount).'</td>
                                            <td>'.floatVal($matchingRow->sgst_amount).'</td>
                                            <td>'.floatVal($matchingRow->igst_amount).'</td>
                                            <td>'.floatVal($matchingRow->cess_amount).'</td>
                                        </tr>';
                                    else:
                                        $tbodyHtml .= '<tr style="background-color: '.$bgColor.'; ">
                                            <td style="height:20px;">NOT FOUND IN ERP</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>';
                                    endif;

                                    //Json Data
                                    $tbodyHtml .= '<tr style="background-color: '.$bgColor.'; ">
                                        <td>'.$status.'</td>
                                        <td>'.strtoupper($key).'</td>
                                        <td>'.$jsonRow->ctin.'</td>
                                        <td>'.$jsonRow->trdnm.'</td>
                                        <td>'.$jsonRow->inum.'</td>
                                        <td>'.formatDate($jsonRow->dt).'</td>
                                        <td>'.$jsonRow->val.'</td>
                                        <td>'.$jsonRow->rev.'</td>
                                        <td>'.$jsonRow->pos.'</td>
                                        <td>'.$jsonRow->typ.'</td>
                                        <td>'.$jsonRow->txval.'</td>
                                        <td>'.$jsonRow->rt.'</td>
                                        <td>'.$jsonRow->cgst.'</td>
                                        <td>'.$jsonRow->sgst.'</td>
                                        <td>'.$jsonRow->igst.'</td>
                                        <td>'.$jsonRow->cess.'</td>
                                    </tr>';

                                    //blank line
                                    $bgColor = "none";
                                    $tbodyHtml .= '<tr style="background-color: '.$bgColor.'; ">
                                        <td style="height:20px;"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';

                                    if($data['match_status'] == 1 && $matchingStatus == false):
                                        $tbodyHtml = '';
                                        $tbody .= $tbodyHtml;
                                    elseif($data['match_status'] == 2 && $matchingStatus == true):
                                        $tbodyHtml = '';
                                        $tbody .= $tbodyHtml;
                                    else:
                                        $tbody .= $tbodyHtml;
                                    endif;
                                endforeach;

                            endif;
                        endif;
                    endif;
                endforeach; 

                //print_r($tbody);exit;

                $tableData = '<thead class="thead-info">
                    <tr>
                        <th>Status</th>
                        <th>Section</th>
                        <th>GSTIN</th>
                        <th>Party Name</th>
                        <th>INV. No.</th>
                        <th>INV Date</th>
                        <th>Invoice Value</th>
                        <th>Revarse Charge</th>
                        <th>POS</th>
                        <th>Invoice Type</th>
                        <th>Taxable Value</th>
                        <th>Rate</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>IGST</th>
                        <th>CESS</th>
                    </tr>
                </thead><tbody>'.$tbody.'</tbody>';

                $this->printJson(['status'=>1,'tableData'=>$tableData]);
            else:
                $this->printJson(['status'=>0,'message'=>["json_file"=>"Failed to parse JSON data."]]);
            endif;
        endif;
    }
}
?>