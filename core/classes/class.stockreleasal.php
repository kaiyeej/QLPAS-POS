<?php

class StockReleasal extends Connection
{
    public $table = "tbl_sales";
    public function view()
    {
        $customer_id = $this->inputs['customer_id'];
        $product_id = $this->inputs['product_id'];
        $rows = array();

        if($customer_id == "-1"){
            $cust_param = "";
        }else{
            $cust_param = "AND h.customer_id='$customer_id'";
        }

        if($product_id == "-1"){
            $prod_param = "";
        }else{
            $prod_param = "AND d.product_id='$product_id'";
        }

        $Products = new Products();
        $StockWithdrawal = new StockWithdrawal();
        $result = $this->select("tbl_sales as h, tbl_sales_details as d","*, sum(d.quantity) as total_qty","h.sales_id=d.sales_id AND h.status='F' AND h.withdrawal_status='1' $prod_param $cust_param GROUP BY d.sales_detail_id");

        while ($row = $result->fetch_assoc()) {
            
            $remaining_qty = $StockWithdrawal->remaining_qty($row['sales_detail_id']);

            if($remaining_qty > 0){
                $row['product'] = $Products->name($row['product_id']);
                $row['remaining_qty'] = number_format($remaining_qty,2);
                $row['total_qty'] = number_format($row['total_qty'],2);
                $row['date'] = date('M d,Y', strtotime($row['sales_date']));
                $rows[] = $row;
            }
        }
        return $rows;
    }

}
