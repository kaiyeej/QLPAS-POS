<?php

class ReceivableLedger extends Connection
{


    public function view()
    {
        $customer_id = $this->inputs['customer_id'];
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];
        
        $rows = array();

        $result = $this->select("tbl_sales","reference_number","customer_id='$customer_id' AND (sales_date >= '$start_date' AND sales_date <= '$end_date') AND (status='F' OR status='P') AND sales_type='H' UNION ALL SELECT reference_number FROM tbl_customer_payment WHERE customer_id='$customer_id' AND (payment_date >= '$start_date' AND payment_date <= '$end_date') AND status='F' UNION ALL SELECT reference_number FROM tbl_beginning_balance WHERE bb_ref_id='$customer_id' AND (bb_date >= '$start_date' AND bb_date <= '$end_date') AND bb_module='AR'");
        
        $Sales = new Sales;
        $CustomerPayment = new CustomerPayment;
        $BeginningBalance = new BeginningBalance;
        $bf = $this->total();
        $balance = (float) $bf[0];
        while ($row = $result->fetch_assoc()) {

            $trans = substr($row['reference_number'], 0, 2);

            if($trans == "CP"){
                $trans = "Customer Payment";
                $id = $CustomerPayment->pk_by_name($row['reference_number']);
                $debit = 0;
                $credit = $CustomerPayment->total($id);
                $balance -= $credit;
                $date = $CustomerPayment->dataRow($id, 'payment_date');
                $ref_number = $row['reference_number'];
            }else if($trans == "BB"){
                $trans = "Beginning Balance";
                $id = $BeginningBalance->pk_by_name($row['reference_number']);
                $debit = $BeginningBalance->total($id);
                $credit = 0;
                $balance += $debit;
                $date = $BeginningBalance->get_row($id, 'bb_date');
                $ref_number = $row['reference_number'];
            }else{
                $trans = "Sales";
                $id = $Sales->pk_by_name($row['reference_number']);
                $debit = ($Sales->total($id));
                $credit = 0;
                $balance += $debit;
                $date = $Sales->dataRow($id, 'sales_date');
                $ref_number = $row['reference_number'];
            }

            $row['date'] = $date;
            $row['reference_number'] = $ref_number;
            $row['transaction'] = $trans;
            $row['debit'] = number_format($debit,2);
            $row['credit'] = number_format($credit,2);
            $row['balance'] = number_format($balance,2);
            $rows[] = $row;
        }
        return $rows;
    }

    public function total()
    {
        $customer_id = $this->inputs['customer_id'];
        $start_date = $this->inputs['start_date'];

        $get_sales = $this->select("tbl_sales as h, tbl_sales_details as d","sum((d.quantity*d.price)-d.discount)","h.customer_id='$customer_id' AND h.sales_date < '$start_date' AND h.sales_type='H' AND (h.status='F' OR h.status='F') AND h.sales_id=d.sales_id");
        $total_sales = $get_sales->fetch_array();

       $getSales = $this->select("tbl_sales" , "sales_id", "customer_id='$customer_id' AND sales_date < '$start_date' AND status='F'");
       $total_sr = 0;
       while($drRow = $getSales->fetch_array()){
            $fetch_sr = $this->select("tbl_sales_return as h, tbl_sales_return_details as d","sum(d.price*d.quantity_return)","h.status='F' AND h.sales_return_id=d.sales_return_id AND h.sales_id='$drRow[sales_id]'");
            $sum_pr = $fetch_sr->fetch_array();
            $total_sr = $sum_pr[0];
       }

        $get_payment = $this->select("tbl_customer_payment as h, tbl_customer_payment_details as d","sum(d.amount)","h.customer_id='$customer_id' AND h.payment_date < '$start_date' AND h.status='F' AND h.cp_id=d.cp_id");
        $total_payment = $get_payment->fetch_array();

        $get_bb = $this->select("tbl_beginning_balance","sum(bb_amount)","bb_ref_id='$customer_id' AND bb_date < '$start_date' AND bb_module='AR'");
        $total_bb = $get_bb->fetch_array();

        $bf = ($total_sales[0]+$total_bb[0])-($total_payment[0]+$total_sr);
        $total = "";
        
        return [$bf,$total];
        
    }
    
}
