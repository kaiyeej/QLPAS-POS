<?php

class InventoryReport extends Connection
{
    public $table = "tbl_product_transactions";
    public function view()
    {   
        $branch_id = $this->getBranch();
        $product_category_id = $this->inputs['product_category_id'];
        $warehouse_id = $this->inputs['warehouse_id'];
        $warehouse = '';
        if ($warehouse_id != -1) {
            $warehouse = "AND warehouse_id = '$warehouse_id'";
        }
        $rows = array();

        $category_eq = $product_category_id == -1 ? ">" : $product_category_id;
        $category_col = $product_category_id == -1 ? '0' : '';

        $query = $this->table('tbl_products AS p')
            ->join('tbl_product_transactions AS c', 'p.product_id', '=', 'c.product_id')
            ->selectRaw('p.product_id, p.product_name, p.product_cost, p.product_code, SUM(IF(c.type="IN", c.quantity, -c.quantity)) AS product_qty')
            ->where('product_category_id', $category_eq, $category_col)
            ->where('status', 1)
            ->where('branch_id', $branch_id);
        if ($warehouse_id != -1) {
            $query->where('warehouse_id', $warehouse_id);
        }
        $result = $query->groupBy('p.product_id')
            ->get();

        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $for_pickup = $this->pick_up_balance($row['product_id'], $branch_id, $warehouse_id);
            $row['count'] = $count++;
            $row['product_code'] =  $row['product_code'];
            $row['for_pickup'] = number_format($for_pickup, 2);
            $row['in_stock'] = $row['product_qty'] + $for_pickup;
            // $row['product_qty'] = $row['product_qty'] + $for_pickup;
            $row['amount'] =  number_format($row['product_qty'] * $row['product_cost'], 2);
            $rows[] = $row;
        }
        return $rows;
    }

    public function pick_up_balance($product_id, $branch_id, $warehouse_id)
    {   
        $param = ($warehouse_id != -1)? "AND h.warehouse_id = '$warehouse_id' AND h.branch_id = '$branch_id'" : "";

        $fetchCS = $this->select("tbl_sales h LEFT JOIN tbl_sales_details d ON h.sales_id=d.sales_id", "sum(d.quantity) as total_qty, sales_detail_id,customer_id", "h.withdrawal_status=1  AND h.for_pick_up=1 AND h.status='F' AND d.product_id='$product_id' $param GROUP BY d.sales_detail_id");
        $total = 0;
        $StockWithdrawal = new StockWithdrawal;
        $SalesReturn = new SalesReturn;
        while ($row = $fetchCS->fetch_assoc()) {
            // $total += $StockWithdrawal->remaining_qty($row['sales_detail_id']);
            $return_qty = $SalesReturn->return_by_sd_id($row['sales_detail_id']);
            $total_release = $StockWithdrawal->pickup_out($row['sales_detail_id']);
            $remaining_qty = $row['total_qty'] - $total_release - $return_qty;
            $total += $remaining_qty;
        }

        return $total;
    }

    public function balance($product_id)
    {
        $result = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity)) AS qty", "product_id = '$product_id' AND status = 1");
        $row = $result->fetch_assoc();
        return (float) $row['qty'];
    }

    public function balance_per_warehouse($product_id, $branch_id, $warehouse_id)
    {
        $result = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity)) AS qty", "product_id = '$product_id' AND branch_id = '$branch_id' AND warehouse_id = '$warehouse_id' AND status = 1");
        $row = $result->fetch_assoc();
        return (float) $row['qty'];
    }

    public function current_qty()
    {
        $Warehouses = new Warehouses;
        $product_id = $this->inputs['product_id'];
        $warehouse_id = $this->inputs['warehouse_id'];
        $branch_id = $Warehouses->warehouse_branch_id($warehouse_id);
        $result = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity)) AS qty", "product_id = '$product_id' AND status = 1 AND warehouse_id='$warehouse_id' AND branch_id=$branch_id");
        $row = $result->fetch_assoc();
        return (float) $row['qty'];
    }

    public function balance_total($date)
    {
        $date = date_create($date);
        date_modify($date, "-1 days");
        $inv_date =  date_format($date, "Y-m-d");
        $result = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity) * cost) AS total", "status = 1 AND date_added < '$inv_date'");
        $row = $result->fetch_assoc();
        return (float) $row['total'];
    }

    public function update_product_qty($table_detail, $primary_key, $primary_id, $branch_id, $warehouse_id, $product_field = "product_id"){

        $fetch = $this->select($table_detail, $product_field, "$primary_key='$primary_id'");
        while($row = $fetch->fetch_assoc()){
            $product_id = $row[$product_field];

            $fetch_inv = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity)) AS qty", "product_id = '$product_id' AND branch_id='$branch_id' AND warehouse_id='$warehouse_id' AND status = 1");
            $inv_row = $fetch_inv->fetch_assoc();
            $current_qty =  $inv_row['qty']*1;
            
            $fetch_count = $this->select("tbl_product_warehouses", "product_warehouse_id", "product_id='$product_id' AND branch_id='$branch_id' AND warehouse_id='$warehouse_id'");
            $count_row = $fetch_count->fetch_assoc();

            if($count_row['product_warehouse_id'] > 0){
                $this->update("tbl_product_warehouses",["product_qty" => $current_qty], "product_id='$product_id' AND branch_id='$branch_id' AND warehouse_id='$warehouse_id'");
            }else{
                $form = array(
                    "product_id" => $product_id,
                    "branch_id" => $branch_id,
                    "warehouse_id" => $warehouse_id,
                    "product_qty" => $current_qty
                );

                $this->insert("tbl_product_warehouses", $form);
            }
        }
    }

    public function inventory_script_runner(){
        $fetch = $this->select("tbl_products","*","product_id > 0");
        while($row = $fetch->fetch_assoc()){
            $product_id = $row['product_id'];
            // fetch branch
            $fetch_branches = $this->select("tbl_branch","branch_id","branch_id = 1");
            while($row_branches = $fetch_branches->fetch_assoc()){
                $branch_id = $row_branches['branch_id'];
                $fetch_warehouse = $this->select("tbl_warehouses", "warehouse_id", "warehouse_id = 1");
                while($warehouse_row = $fetch_warehouse->fetch_assoc()){
                    $warehouse_id = $warehouse_row['warehouse_id'];

                    // current inv

                    $fetch_inv = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity)) AS qty", "product_id = '$product_id' AND branch_id='$branch_id' AND warehouse_id='$warehouse_id' AND status = 1");
                    $inv_row = $fetch_inv->fetch_assoc();
                    $current_qty =  $inv_row['qty']*1;


                    // insert or update
                    $fetch_count = $this->select("tbl_product_warehouses", "product_warehouse_id", "product_id='$product_id' AND branch_id='$branch_id' AND warehouse_id='$warehouse_id'");
                    $count_row = $fetch_count->fetch_assoc();

                    if($count_row['product_warehouse_id'] > 0){
                        $this->update("tbl_product_warehouses",["product_qty" => $current_qty], "product_id='$product_id' AND branch_id='$branch_id' AND warehouse_id='$warehouse_id'");
                    }else{
                        $form = array(
                            "product_id" => $product_id,
                            "branch_id" => $branch_id,
                            "warehouse_id" => $warehouse_id,
                            "product_qty" => $current_qty
                        );

                        $this->insert("tbl_product_warehouses", $form);
                    }
                }
            }
        }

        return json_encode("All scripts are good!");
    }
}
