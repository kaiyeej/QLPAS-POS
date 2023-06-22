<?php

class StatementOfAccounts extends Connection
{


    public function view()
    {
        $customer_id = $this->inputs['customer_id'];
        $start_date = $this->inputs['start_date'];
        $end_date = $this->inputs['end_date'];
        
        $rows = array();

        $result = $this->select("tbl_sales","reference_number","customer_id='$customer_id' AND (sales_date >= '$start_date' AND sales_date <= '$end_date') AND (status='F' OR status='P') UNION ALL SELECT reference_number FROM tbl_customer_payment WHERE customer_id='$customer_id' AND (payment_date >= '$start_date' AND payment_date <= '$end_date') AND status='F' UNION ALL SELECT reference_number FROM tbl_beginning_balance WHERE bb_ref_id='$customer_id' AND (bb_date >= '$start_date' AND bb_date <= '$end_date') AND bb_module='AR' UNION ALL SELECT reference_number FROM tbl_credit_memo WHERE account_id='$customer_id' AND (memo_date >= '$start_date' AND memo_date <= '$end_date') AND status='F' AND memo_type='AR' UNION ALL SELECT reference_number FROM tbl_debit_memo WHERE account_id='$customer_id' AND (memo_date >= '$start_date' AND memo_date <= '$end_date') AND status='F' AND memo_type='AR'");
        
        $Sales = new Sales;
        $CustomerPayment = new CustomerPayment;
        $BeginningBalance = new BeginningBalance;
        $CreditMemo = new CreditMemo;
        $DebitMemo = new DebitMemo;
        $bf = $this->total();
        $balance = (float) $bf[0];
        while ($row = $result->fetch_assoc()) {

            $trans = substr($row['reference_number'], 0, 2);

            if($trans == "CP"){
                $trans = "Customer Payment";
                $data = $CustomerPayment->rows($row['reference_number']);
                $debit = 0;
                $credit = $CustomerPayment->total($data['cp_id']);
                $balance -= $credit;
                $date = $data['payment_date'];
                $ref_number = $row['reference_number'];
            }else if($trans == "BB"){
                $trans = "Beginning Balance";
                $data = $BeginningBalance->rows($row['reference_number']);
                $debit = $BeginningBalance->total($data['bb_id']);
                $credit = 0;
                $balance += $debit;
                $date = $data['bb_date'];
                $ref_number = $row['reference_number'];
            }else if($trans == "CM"){
                $trans = "Credit Memo";
                $data = $CreditMemo->rows($row['reference_number']);
                $credit = $CreditMemo->total($data['cm_id']);
                $debit = 0;
                $balance -= $credit;
                $date = $data['memo_date'];
                $ref_number = $row['reference_number'];
            }else if($trans == "DM"){
                $trans = "Debit Memo";
                $data = $DebitMemo->rows($row['reference_number']);
                $credit = 0;
                $debit = $DebitMemo->total($data['dm_id']);
                $balance += $debit;
                $date = $data['memo_date'];
                $ref_number = $row['reference_number'];
            }else{
                $trans = "Sales";
                $data = $Sales->rows($row['reference_number']);
                $debit = ($Sales->total($data['sales_id']));
                $credit = $data['sales_type'] == "C" ? ($Sales->total($data['sales_id'])) : 0;
                $balance += ($debit-$credit);
                $date = $data['sales_date'];
                $ref_number = $data['sales_type'] == "C" ? "<i style='font-size: 11px;color: #4CAF50;'>CA #</i> ".$row['reference_number'] : "<i style='font-size: 11px;color: #4CAF50;'>CH #</i> " .$row['reference_number'];
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

        $get_sales = $this->select("tbl_sales as h, tbl_sales_details as d","sum((d.quantity*d.price)-d.discount)","h.customer_id='$customer_id' AND h.sales_date < '$start_date' AND h.sales_type='H' AND (h.status='F' OR h.status='P') AND h.sales_id=d.sales_id");
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

        $get_credit_memo = $this->select("tbl_credit_memo as h, tbl_credit_memo_details as d","sum(d.amount)","h.account_id='$customer_id' AND h.memo_date < '$start_date'  AND memo_type='AR' AND h.status='F' AND h.cm_id=d.cm_id");
        $total_cm = $get_credit_memo->fetch_array();

        $get_debit_memo = $this->select("tbl_debit_memo as h, tbl_debit_memo_details as d","sum(d.amount)","h.account_id='$customer_id' AND h.memo_date < '$start_date'  AND memo_type='AR' AND h.status='F' AND h.dm_id=d.dm_id");
        $total_dm = $get_debit_memo->fetch_array();

        $get_bb = $this->select("tbl_beginning_balance","sum(bb_amount)","bb_ref_id='$customer_id' AND bb_date < '$start_date' AND bb_module='AR'");
        $total_bb = $get_bb->fetch_array();

        $bf = ($total_sales[0]+$total_bb[0]+$total_dm[0])-($total_payment[0]+$total_sr+$total_cm[0]);
        $total = "";
        
        return [$bf,$total];
        
    }
    
}
