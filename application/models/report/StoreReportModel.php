<?php
class StoreReportModel extends MasterModel{
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";

    public function getStockRegisterData($data){
        $queryData = array();
        $queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,ifnull(st.stock_qty,0) as stock_qty,ifnull(st.gross_weight,0) as gross_weight,ifnull(st.net_weight,0) as net_weight";

        $queryData['leftJoin']['(SELECT SUM(qty * p_or_m) as stock_qty,SUM(gross_weight * p_or_m) as gross_weight,SUM(net_weight * p_or_m) as net_weight,item_id FROM stock_transaction WHERE is_delete = 0 GROUP BY item_id) as st'] = "item_master.id = st.item_id";

        $queryData['where']['item_master.item_type'] = $data['item_type'];
        if(!empty($data['stock_type'])):
            if($data['stock_type'] == 1):
                $queryData['where']['ifnull(st.stock_qty,0) > '] = "ifnull(st.stock_qty,0) > 0";
            else:
                $queryData['where']['ifnull(st.stock_qty,0) <= '] = "0";
            endif;
        endif;

        $result = $this->rows($queryData);
        return $result;
    }

    public function getLocationWiseStockData($data){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.item_id,stock_transaction.location_id,location_master.location,stock_transaction.item_id,SUM(stock_transaction.qty * stock_transaction.p_or_m) as stock_qty,SUM(stock_transaction.gross_weight * stock_transaction.p_or_m) as gross_weight,SUM(stock_transaction.net_weight * stock_transaction.p_or_m) as net_weight";
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $queryData['having'][] = "SUM(stock_transaction.qty * stock_transaction.p_or_m) > 0";
        $queryData['group_by'][] = "stock_transaction.location_id";
        $result = $this->rows($queryData);
        return $result;
    }

    public function getSerialNumberWiseStockData($data){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.item_id,stock_transaction.location_id,location_master.location,stock_transaction.unique_id,stock_transaction.item_id,SUM(stock_transaction.qty * stock_transaction.p_or_m) as stock_qty,SUM(stock_transaction.gross_weight * stock_transaction.p_or_m) as gross_weight,SUM(stock_transaction.net_weight * stock_transaction.p_or_m) as net_weight";
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $queryData['where']['stock_transaction.location_id'] = $data['location_id'];
        $queryData['having'][] = "SUM(stock_transaction.qty * stock_transaction.p_or_m) > 0";
        $queryData['group_by'][] = "stock_transaction.unique_id";
        $result = $this->rows($queryData);
        return $result;
    }
}
?>