<?php

class InventoryReport extends Connection
{
    public $table = "tbl_product_transactions";
    public function view()
    {
        $product_category_id = $this->inputs['product_category_id'];
        $rows = array();

        $category_eq = $product_category_id == -1 ? ">" : $product_category_id;
        $category_col = $product_category_id == -1 ? '0' : '';

        $result = $this->table('tbl_products AS p')
            ->join('tbl_product_transactions AS c', 'p.product_id', '=', 'c.product_id')
            ->selectRaw('p.product_id', 'p.product_name', 'p.product_cost', 'p.product_code', "SUM(IF(type='IN',quantity,-quantity)) AS product_qty")
            ->where('product_category_id', $category_eq, $category_col)
            ->where('status', 1)
            ->groupBy('p.product_id')
            ->get();

        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $row['count'] = $count++;
            $row['product_code'] =  $row['product_code'];
            $row['amount'] = $row['product_qty'] * $row['product_cost'];
            $rows[] = $row;
        }
        return $rows;
    }

    public function balance($product_id)
    {
        $result = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity)) AS qty", "product_id = '$product_id' AND status = 1");
        $row = $result->fetch_assoc();
        return (float) $row['qty'];
    }

    public function balance_total($date)
    {
        $date=date_create($date);
        date_modify($date,"-1 days");
        $inv_date =  date_format($date,"Y-m-d");
        $result = $this->select($this->table, "SUM(IF(type='IN',quantity,-quantity) * cost) AS total", "status = 1 AND date_added < '$inv_date'");
        $row = $result->fetch_assoc();
        return (float) $row['total'];
    }
}
