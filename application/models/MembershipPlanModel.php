<?php
class MembershipPlanModel extends MasterModel{
    private $planMaster = "membership_plan";

    public function getPlanList($data=array()){
        $queryData['tableName'] = $this->planMaster;
        
        if(!empty($data['is_active'])):
            $queryData['where_in']['is_active'] = $data['is_active'];
        endif;

        $result = $this->rows($queryData);
        return $result;
    }

    public function getPlan($data){
        $queryData['tableName'] = $this->planMaster;
        $queryData['where']['id'] = $data['id'];
        $result = $this->row($queryData);
        return $result;
    }
}
?>