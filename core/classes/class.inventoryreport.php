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
            $for_pickup = $this->pick_up_balance($row['product_id']);
            $row['count'] = $count++;
            $row['product_code'] =  $row['product_code'];
            $row['for_pickup'] = number_format($for_pickup, 2);
            $row['in_stock'] = $row['product_qty'] + $for_pickup;
            $row['amount'] =  number_format($row['product_qty'] * $row['product_cost'], 2);
            $rows[] = $row;
        }
        return $rows;
    }

    public function pick_up_balance($product_id)
    {

        $fetchCS = $this->select("tbl_sales as h, tbl_sales_details as d", "sales_detail_id,customer_id", "h.sales_id=d.sales_id AND h.withdrawal_status=1  AND h.for_pick_up=1 AND h.status='F' AND d.product_id='$product_id' GROUP BY d.sales_detail_id");
        $total = 0;
        $StockWithdrawal = new StockWithdrawal;
        while ($row = $fetchCS->fetch_assoc()) {
            $total += $StockWithdrawal->remaining_qty($row['sales_detail_id']);
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
        $product_id = $this->inputs['product_id'];
        $warehouse_id = $this->inputs['warehouse_id'];
        $result = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity)) AS qty", "product_id = '$product_id' AND status = 1 AND warehouse_id='$warehouse_id'");
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
}
