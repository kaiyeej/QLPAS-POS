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
        $JobOrder = new JobOrder;
        $PurchaseOrder = new PurchaseOrder;
        $BeginningBalance = new BeginningBalance;
        $Sales = new Sales;
        $ProductConversion = new ProductConversion;
        $InventoryAdjustment = new InventoryAdjustment;
        $SalesReturn = new SalesReturn;
        $PurchaseReturn = new PurchaseReturn;

        $result = $this->select($this->table, "*,IF(type='IN',quantity,0) AS qty_in,IF(type='OUT',quantity,0) AS qty_out", "product_id = '$product_id' AND status = '1' AND date_added BETWEEN '$start_date' AND '$end_date' ORDER BY date_modified ASC");

        $bf = $this->balance_fowarded();
        $qty_balance = $bf['qty'];
        while ($row = $result->fetch_assoc()) {
            $qty = $row['qty_in'] - $row['qty_out'];
            $qty_balance += $qty;

            if($row['module'] == "BB"){
                $module = "Beginning Balance";
                $reference_number = $BeginningBalance->name($row['header_id']);
            }else if($row['module'] == "PO"){
                $module = "Purchase Order";
                $reference_number = $PurchaseOrder->name($row['header_id']);
            }else if($row['module'] == "SLS"){
                $module = "Sales";
                $reference_number = $Sales->name($row['header_id']);
            }else if($row['module'] == "JO"){
                $module = "Job Order";
                $reference_number = $JobOrder->name($row['header_id']);
            }else if($row['module'] == "PC"){
                $module = "Product Conversion";
                $reference_number = $ProductConversion->name($row['header_id']);
            }else if($row['module'] == "IA"){
                $module = "Inventory Adjustment";
                $reference_number = $InventoryAdjustment->name($row['header_id']);
            }else if($row['module'] == "SR"){
                $module = "Sales Return";
                $reference_number = $SalesReturn->name($row['header_id']);
            }else if($row['module'] == "PR"){
                $module = "Purchase Return";
                $reference_number = $PurchaseReturn->name($row['header_id']);
            }

            $row['qty_balance'] = number_format($qty_balance,2);
            $row['module'] = $module;
            $row['reference_number'] = $reference_number;
            $row['amount'] = number_format($qty_balance * $row['cost'],2);
            $row['date'] = date('M d, Y', strtotime($row['date_modified']));
            $rows[] = $row;
        }
        return $rows;
    }

    public function balance_fowarded()
    {
        $start_date = $this->inputs['start_date'];
        $product_id = $this->inputs['product_id'];
        $rows = array();
        // $result = $this->select($this->table, "IF(type='IN',quantity,0) AS qty_in,IF(type='OUT',quantity,0) AS qty_out, SUM(CASE WHEN type = 'IN' THEN cost*quantity ELSE 0 END) as total_cost, SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END) as total_qty", "product_id = '$product_id' AND status = 1 AND date_added < '$start_date'");
        $result = $this->select($this->table, "SUM(CASE WHEN type = 'IN' THEN cost*quantity ELSE 0 END) as total_cost", "product_id = '$product_id' AND status = 1 AND date_added < '$start_date'");
        $row = $result->fetch_array();

        $result_in = $this->select($this->table, "SUM(quantity) as qty_in", "product_id = '$product_id' AND status = 1 AND date_added < '$start_date' AND type='IN'");
        $rowIN = $result_in->fetch_array();

        $resultOut = $this->select($this->table, "SUM(quantity) as qty_out", "product_id = '$product_id' AND status = 1 AND date_added < '$start_date' AND type='OUt'");
        $rowOut = $resultOut->fetch_array();

        $qty = $rowIN['qty_in'] - $rowOut['qty_out'];
        $row['qty'] = $qty;
        $row['amount'] = number_format(($qty*($row['total_cost']/$rowIN['qty_in'])),2);
        $rows = $row;
        return $rows;
    }

    public function trigger_add($product_id, $quantity, $cost, $price, $header_id, $detail_id, $module, $type)
    {
        return "INSERT INTO $this->table (product_id,quantity,cost,price,header_id,detail_id,module,type) VALUES (NEW.$product_id,NEW.$quantity,NEW.$cost,NEW.$price,NEW.$header_id,NEW.$detail_id,'$module','$type')";
    }
}
