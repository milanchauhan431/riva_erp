<?php
class MembershipModel extends MasterModel{
    private $transMain = "trans_main";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id, trans_main.entry_type, trans_main.trans_number, trans_main.trans_date, trans_main.party_id, party_master.party_name, membership_plan.id as plan_id, membership_plan.plan_name, trans_main.credit_period as total_emi, trans_main.total_amount as emi_amount, trans_main.net_amount as total_amount, ROUND(IF(trans_main.rop_amount <> 0,(trans_main.rop_amount / trans_main.total_amount),0),2) as received_emi, trans_main.rop_amount as received_amount";

        $data['leftJoin']['membership_plan'] = "membership_plan.id = trans_main.ref_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";

        $data['where']['trans_main.entry_type'] = $data['entry_type'];
        if($data['status'] == 0):
            $data['where']['(trans_main.net_amount - trans_main.rop_amount) >'] = 0;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        else:
            $data['where']['(trans_main.net_amount - trans_main.rop_amount) <='] = 0;
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        endif;

        $data['searchCol'][] = "";
		$data['searchCol'][] = "";
		$data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "membership_plan.plan_name";
        $data['searchCol'][] = "trans_main.credit_period";
        $data['searchCol'][] = "trans_main.total_amount";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "IF(trans_main.rop_amount <> 0,(trans_main.rop_amount / trans_main.total_amount),0)";
        $data['searchCol'][] = "trans_main.rop_amount";

        $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(empty($data['id'])):
                $data['trans_no'] = $this->transMainModel->nextTransNo($data['entry_type']);
            endif;
            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];

            $result = $this->store($this->transMain,$data,'Membership');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }        
    }

    public function getMembership($data){
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $postData["table_name"] = $this->transMain;
            $postData['where'] = [['column_name'=>'from_entry_type','column_value'=>$this->data['entryData']->id]];
            $postData['find'] = [['column_name'=>'ref_id','column_value'=>$id]];
            $checkRef = $this->checkEntryReference($postData);
            if($checkRef['status'] == 0):
                return $checkRef;
            endif;

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Membership is currently in use. you cannot delete it.'];
            endif;

            $result = $this->trash($this->transMain,['id'=>$id],'Membership');

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