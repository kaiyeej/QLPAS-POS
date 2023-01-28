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

        $count = 1;
        $result = $this->select("tbl_sales as h, tbl_sales_details as d","reference_number,sum((quantity*price)-discount) as amount","d.product_id='$row[product_id]' AND h.sales_id=d.sales_id AND YEAR(h.sales_date) = '$year' AND MONTH(h.sales_date) = '$count' AND h.status='F'");
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $row['item'] = Products::name($row['product_id']);
            while ($count <= 12) {
                // $sRow = $fetchSAles->fetch_array();
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
            $fetchSAles = $this->select("tbl_sales as h, tbl_sales_details as d","sum((quantity*price)-discount) as amount, sum(quantity) as qty","d.product_id='$row[product_id]' AND h.sales_id=d.sales_id AND (h.sales_date >= '$start_date' AND h.sales_date <= '$end_date') AND h.status='F'");
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
            $fetchSAles = $this->select("tbl_sales as h, tbl_sales_details as d, tbl_users as u","sum((quantity*price)-discount) as amount, sum(quantity) as qty","d.product_id='$row[product_id]' AND u.user_category='C' AND u.user_id=h.encoded_by $param AND h.sales_id=d.sales_id AND (h.sales_date >= '$start_date' AND h.sales_date <= '$end_date') AND h.status='F'");
            $sRow = $fetchSAles->fetch_array();

            $row['qty'] = number_format($sRow['qty'],2);
            $row['amount'] = number_format($sRow['amount'],2);
                
            $rows[] = $row;
        }
        return $rows;
    }

    public function generate_customer()
    {
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];
        $customer_id = $this->inputs['customer_id'];
        
        if($customer_id >= 0){
            $param = "AND h.customer_id = '$customer_id'";
        }else{
            $param = "";
        }

        $result = $this->select("tbl_products","*","");
        $rows = array();
        while($row = $result->fetch_assoc()) {
            
            $row['item'] = Products::name($row['product_id']);
            $fetchSAles = $this->select("tbl_sales as h, tbl_sales_details as d","sum((quantity*price)-discount) as amount, sum(quantity) as qty","d.product_id='$row[product_id]' $param AND h.sales_id=d.sales_id AND (h.sales_date >= '$start_date' AND h.sales_date <= '$end_date') AND h.status='F'");
            $sRow = $fetchSAles->fetch_array();

            $row['qty'] = number_format($sRow['qty'],2);
            $row['amount'] = number_format($sRow['amount'],2);
                
            $rows[] = $row;
        }
        return $rows;
    }

    public function generate_summary()
    {
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];
        $user_id = $this->inputs['user_id'];
        
        if($user_id >= 0){
            $param = "AND cashier_id = '$user_id'";
        }else{
            $param = "";
        }

        $Sales = new Sales();
        //$SalesReturn = new SalesReturn();
        $result = $this->select("tbl_sales_summary","*","(date_added >= '$start_date' AND date_added <= '$end_date') AND status='F' $param");
        $rows = array();
        while($row = $result->fetch_assoc()) {
            
            //$total_return = $SalesReturn->total_return_per_summary($row['sales_summary_id']);
            
            $row['cashier'] = Users::name($row['cashier_id']);
            $row['starting_balance'] = number_format($row['starting_balance'],2);
            $row['total_sales_amount'] = number_format($row['total_sales_amount'], 2);
            $row['total_sales_cash'] = number_format($Sales->total_cash_sales_summary($row['sales_summary_id']),2);
            $row['total_sales_charge'] = number_format($Sales->total_charge_sales_summary($row['sales_summary_id']),2);
            $row['total_amount_collected'] = number_format($row['total_amount_collected'],2);
            $row['total_deficit'] = number_format($row['total_deficit'],2);   
                
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
