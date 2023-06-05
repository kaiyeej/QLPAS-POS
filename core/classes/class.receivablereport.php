<?php

class ReceivableReport extends Connection
{
    public $table = "tbl_customers";

    public function view()
    {
        $customer_id = $this->inputs['customer_id'];
        $rows = array();

        if($customer_id == -1){
            $query = "";
        }else{
            $query = "customer_id='$customer_id'";
        }

        $result = $this->select($this->table, "*", $query);

        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $row['balance'] = number_format($this->balance($row['customer_id']),2);
            $row['count'] = $count++;
            $rows[] = $row;
        }
        return $rows;
    }

    public function balance($customer_id)
    {
        $get_payment_h = $this->select("tbl_customer_payment AS ch, tbl_customer_payment_details AS cd", "SUM(cd.amount) as total", "ch.customer_id='$customer_id' AND ch.cp_id=cd.cp_id AND cd.type='DR' AND ch.status='F'");
        $payment_h = $get_payment_h->fetch_assoc();

        $get_sales = $this->select("tbl_sales as h, tbl_sales_details AS d", "SUM((d.price*d.quantity)-d.discount) as total", "h.customer_id='$customer_id' AND d.sales_id=h.sales_id AND (h.status='F' OR h.status='P') AND h.sales_type='H'");
        $sales_row = $get_sales->fetch_assoc();

        $get_sr = $this->select("tbl_sales as h, tbl_sales_details AS d, tbl_sales_return as sr, tbl_sales_return_details as srd", "SUM((srd.quantity_return*srd.price)-(srd.discount/srd.quantity*srd.quantity_return)) as total", "h.customer_id='$customer_id' AND d.sales_id=h.sales_id AND h.status='F' AND h.sales_type='H' AND sr.sales_return_id=srd.sales_return_id AND sr.status='F' AND sr.sales_id=h.sales_id AND d.sales_detail_id=srd.sales_detail_id");
        $sr_row = $get_sr->fetch_assoc();


        $get_bb = $this->select("tbl_beginning_balance", "SUM(bb_amount) as total", "bb_ref_id='$customer_id' AND bb_module='AR'");
        $bb_row = $get_bb->fetch_assoc();

        $get_payment_bb = $this->select("tbl_customer_payment AS ch, tbl_customer_payment_details AS cd", "SUM(cd.amount) as total", "ch.customer_id='$customer_id' AND ch.cp_id=cd.cp_id AND cd.type='BB' AND ch.status='F' AND ch.status='F'");
        $payment_bb = $get_payment_bb->fetch_assoc();

        $get_credit_memo = $this->select("tbl_credit_memo as h, tbl_credit_memo_details as d","sum(d.amount)","h.account_id='$customer_id'  AND memo_type='AR' AND h.status='F' AND h.cm_id=d.cm_id");
        $total_cm = $get_credit_memo->fetch_array();

        $get_debit_memo = $this->select("tbl_debit_memo as h, tbl_debit_memo_details as d","sum(d.amount)","h.account_id='$customer_id'  AND memo_type='AR' AND h.status='F' AND h.dm_id=d.dm_id");
        $total_dm = $get_debit_memo->fetch_array();

        
        return (($sales_row['total']-$sr_row['total'])+$bb_row['total']+$total_dm[0])-($payment_h['total']+$payment_bb['total']+$total_cm[0]);
    }
}
