<?php

class StockCard extends Connection
{
    public $table = "tbl_product_transactions";
    public function view()
    {
        $product_id = $this->inputs['product_id'];
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];
        $rows = array();

        $result = $this->select($this->table, "*,IF(type='IN',quantity,0) AS qty_in,IF(type='OUT',quantity,0) AS qty_out", "product_id = '$product_id' AND status = '1' AND date_added BETWEEN '$start_date' AND '$end_date' ORDER BY date_modified ASC");

        $qty_balance = 0;
        while ($row = $result->fetch_assoc()) {
            $qty = $row['qty_in'] - $row['qty_out'];
            $qty_balance += $qty;

            if($row['module'] == "BB"){
                $module = "Beginning Balance";
            }else if($row['module'] == "PO"){
                $module = "Purchase Order";
            }else if($row['module'] == "SLS"){
                $module = "Sales";
            }else if($row['module'] == "JO"){
                $module = "Job Order";
            }else if($row['module'] == "PC"){
                $module = "Product Conversion";
            }else if($row['module'] == "IA"){
                $module = "Inventory Adjustment";
            }else if($row['module'] == "SR"){
                $module = "Sales Return";
            }else if($row['module'] == "PR"){
                $module = "Purchase Return";
            }

            $row['qty_balance'] = number_format($qty_balance,2);
            $row['module'] = $module;
            $row['amount'] = number_format($qty_balance * $row['cost'],2);
            $row['date'] = date('M d, Y', strtotime($row['date_modified']));
            $rows[] = $row;
        }
        return $rows;
    }

    public function balance($product_id)
    {
        $start_date = $this->inputs['start_date'];
        $product_id = $this->inputs['product_id'];
        $rows = array();
        $result = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity)) AS qty, SUM(IF(type='IN',cost)) AS cost", "product_id = '$product_id' AND status = 1 AND date_added > '$start_date'");
        $row = $result->fetch_array();
        $row['qty'] = $row['qty'];
        $row['amount'] = $row['qty']/$row['cost'];
        $rows = $row;
        return $rows;
    }

    public function trigger_add($product_id, $quantity, $cost, $price, $header_id, $detail_id, $module, $type)
    {
        return "INSERT INTO $this->table (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.$product_id,NEW.$quantity,NEW.$cost,NEW.$price,NEW.$header_id,NEW.$detail_id,'$module','$type')";
    }
}
