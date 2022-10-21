<?php

class SalesReport extends Connection
{
    public $table = "tbl_sales";
    public $table_details = "tbl_sales_details";

    public function generate_yearly()
    {
        $year = $this->inputs['sales_year'];
        $item = $this->inputs['product_category_id'];

        if($item >= 0){
            $param = "product_category_id = '$item'";
        }else{
            $param = "";
        }

        $result = $this->select("tbl_products","*",$param);
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $row['item'] = Products::name($row['product_id']);
            $count = 1;
            while ($count <= 12) {
                $fetchSAles = $this->select("tbl_sales as h, tbl_sales_details as d","sum(quantity*price) as amount","d.product_id='$row[product_id]' AND h.sales_id=d.sales_id AND YEAR(h.sales_date) = '$year' AND MONTH(h.sales_date) = '$count' AND h.status='F'");
                $sRow = $fetchSAles->fetch_array();
                $row[$count] = ($sRow['amount'] <= 0 ? "" : number_format($sRow['amount'],2));
                $count++;
            }
            $rows[] = $row;
        }
        return $rows;
    }
    
    public function generate_daily()
    {
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];
        $item = $this->inputs['product_category_id'];

        if($item >= 0){
            $param = "product_category_id = '$item'";
        }else{
            $param = "";
        }

        $result = $this->select("tbl_products","*",$param);
        $rows = array();
        while($row = $result->fetch_assoc()) {
            $row['item'] = Products::name($row['product_id']);
            $fetchSAles = $this->select("tbl_sales as h, tbl_sales_details as d","sum(quantity*price) as amount, sum(quantity) as qty","d.product_id='$row[product_id]' AND h.sales_id=d.sales_id AND (h.sales_date >= '$start_date' AND h.sales_date <= '$end_date') AND h.status='F'");
            $sRow = $fetchSAles->fetch_array();

            $row['qty'] = number_format($sRow['qty'],2);
            $row['amount'] = number_format($sRow['amount'],2);//($sRow['amount'] <= 0 ? "" : number_format($sRow['amount'],2));
                
            $rows[] = $row;
        }
        return $rows;
    }

    public function generate_cashier()
    {
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];
        $user_id = $this->inputs['user_id'];
        
        if($user_id >= 0){
            $param = "AND h.encoded_by = '$user_id'";
        }else{
            $param = "";
        }

        $result = $this->select("tbl_products","*","");
        $rows = array();
        while($row = $result->fetch_assoc()) {
            
            $row['item'] = Products::name($row['product_id']);
            $fetchSAles = $this->select("tbl_sales as h, tbl_sales_details as d, tbl_users as u","sum(quantity*price) as amount, sum(quantity) as qty","d.product_id='$row[product_id]' AND u.user_category='C' AND u.user_id=h.encoded_by $param AND h.sales_id=d.sales_id AND (h.sales_date >= '$start_date' AND h.sales_date <= '$end_date') AND h.status='F'");
            $sRow = $fetchSAles->fetch_array();

            $row['qty'] = number_format($sRow['qty'],2);
            $row['amount'] = number_format($sRow['amount'],2);
                
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
}
