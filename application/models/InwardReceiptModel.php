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
        $data['searchCol'][] = "inward_receipt.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(inward_receipt.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "inward_receipt.design_no";
        $data['searchCol'][] = "inward_receipt.qty";
        $data['searchCol'][] = "inward_receipt.gross_weight";
        $data['searchCol'][] = "inward_receipt.net_weight";
        $data['searchCol'][] = "inward_receipt.purchase_price";
        $data['searchCol'][] = "inward_receipt.sales_price";
        $data['searchCol'][] = "inward_receipt.remark";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getInwardReceipt($data=array()){
        $queryData = array();
        $queryData['tableName'] = $this->inwardReceipt;
        $queryData['where']['id'] = $data['id'];
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                $data['trans_no'] = $this->transMainModel->nextTransNo($data['entry_type']);
                $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            endif;

            $result = $this->store($this->inwardReceipt,$data,'Inward Receipt');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->inwardReceipt,['id'=>$id],'Inward Receipt');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}
?>