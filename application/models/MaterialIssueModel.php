<?php
class MaterialIssueModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $stockTrans = "stock_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id,trans_main.trans_number,trans_main.trans_date,trans_main.vou_acc_id as collected_by,cb.emp_name as collected_by_name,trans_main.sales_executive,se.emp_name as sales_executive_name, tc.total_issue, tc.total_return, tc.total_sold";

        $data['leftJoin']['employee_master as cb'] = "cb.id = trans_main.vou_acc_id";
        $data['leftJoin']['employee_master as se'] = "se.id = trans_main.sales_executive";
        $data['leftJoin']['(SELECT trans_main_id, SUM(IF(trans_status = 0,1,0)) as total_issue, SUM(IF(trans_status = 1,1,0)) as total_return, SUM(IF(trans_status = 2,1,0)) as total_sold FROM trans_child WHERE is_delete = 0 AND entry_type = '.$this->data['entryData']->id.' GROUP BY trans_main_id) as tc'] = "tc.trans_main_id = trans_main.id";

        $data['where']['trans_main.entry_type'] = $this->data['entryData']->id;

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_number.trans_date,'%d-%m-%Y')";
        $data['serachCol'][] = "cb.emp_name";
        $data['serachCol'][] = "se.emp_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            $data['id'] = "";
            $data['trans_no'] = $this->transMainModel->nextTransNo($data['entry_type']);
            $data['trans_number'] = $data['trans_prefix'].$data['trans_no'];
            $data['trans_date'] = getFyDate();
            $toLocation = $data['feasibility_by'];
            $itemData = $data['itemData'];
            unset($data['itemData'],$data['feasibility_by']);

            $result = $this->store($this->transMain,$data);

            foreach($itemData as $row):
                $row['entry_type'] = $data['entry_type'];
                $row['trans_main_id'] = $result['id'];
                $row['feasibility_by'] = $toLocation;
                $row['is_delete'] = 0;
                $this->store($this->transChild,$row);

                $this->edit($this->stockTrans,['id'=>$row['ref_id']],['location_id'=>$toLocation]);
            endforeach;
            

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getIssueItems($data){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*";
        $queryData['where']['trans_child.trans_main_id'] = $data['id'];
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $transList = $this->getIssueItems(['id'=>$id]);

            foreach($transList as $row):
                $this->edit($this->stockTrans,['id'=>$row->ref_id],['location_id'=>$row->initiate_by]);
                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            $result = $this->trash($this->transMain,['id'=>$id]);

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