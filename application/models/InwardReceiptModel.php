<?php
class InwardReceiptModel extends MasterModel{
    private $inwardReceipt = "inward_receipt";

    public function getDTRows($data){
        $data['tableName'] = $this->inwardReceipt;
        $data['select'] = "inward_receipt.id,inward_receipt.trans_number,inward_receipt.trans_date,inward_receipt.party_id,party_master.party_name,inward_receipt.item_id,item_master.item_code,item_master.item_name,inward_receipt.design_no,inward_receipt.qty,inward_receipt.gross_weight,inward_receipt.net_weight,inward_receipt.purchase_price,inward_receipt.sales_price,inward_receipt.remark";

        $data['leftJoin']['party_master'] = "inward_receipt.party_id = party_master.id";
        $data['leftJoin']['item_master'] = "inward_receipt.item_id = item_master.id";

        if($data['status'] == 0):
            $data['where']['inward_receipt.approved_by'] = 0;
            $data['where']['inward_receipt.trans_date <='] = $this->endYearDate;
        elseif($data['status'] == 1):
            $data['where']['inward_receipt.approved_by >'] = 0;
            $data['where']['inward_receipt.trans_date >='] = $this->startYearDate;
            $data['where']['inward_receipt.trans_date <='] = $this->endYearDate;
        endif;

        $data['order_by']['inward_receipt.trans_date'] = "DESC";
        $data['order_by']['inward_receipt.id'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.qty";
        $data['searchCol'][] = "trans_child.dispatch_qty";
        $data['searchCol'][] = "(trans_child.qty - trans_child.dispatch_qty)";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }
}
?>