<?php

ini_set('max_execution_time', -1);

class ReceivableReport extends Connection
{
    public $table = "tbl_customers";

    public function view()
    {
        $customer_id = $this->inputs['customer_id'];
        $rows = array();

        if ($customer_id == -1) {
            $query = "";
        } else {
            $query = "customer_id='$customer_id'";
        }

        $result = $this->select($this->table, "*", $query);

        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $row['balance'] = number_format($this->balance($row['customer_id']), 2);
            $row['total'] = $this->balance($row['customer_id']);
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

        $get_credit_memo = $this->select("tbl_credit_memo as h, tbl_credit_memo_details as d", "sum(d.amount)", "h.account_id='$customer_id'  AND memo_type='AR' AND h.status='F' AND h.cm_id=d.cm_id");
        $total_cm = $get_credit_memo->fetch_array();

        $get_debit_memo = $this->select("tbl_debit_memo as h, tbl_debit_memo_details as d", "sum(d.amount)", "h.account_id='$customer_id'  AND memo_type='AR' AND h.status='F' AND h.dm_id=d.dm_id");
        $total_dm = $get_debit_memo->fetch_array();


        return (($sales_row['total'] - $sr_row['total']) + $bb_row['total'] + $total_dm[0]) - ($payment_h['total'] + $payment_bb['total'] + $total_cm[0]);
    }

    public function show_unpaid()
    {
        $Sales = new Sales;
        $BeginningBalance = new BeginningBalance;
        $CustomerPayment = new CustomerPayment;
        $customer_id = isset($this->inputs['customer_id']) ? $this->inputs['customer_id'] : null;
        $customer_terms = $this->inputs['customer_terms'];
        $rows = array();

        $result = $this->select("tbl_sales", "reference_number", "customer_id='$customer_id' AND status='F' AND paid_status='0' AND sales_type='H' UNION ALL SELECT reference_number FROM tbl_beginning_balance WHERE bb_ref_id='$customer_id' AND bb_module='AR' AND bb_paid_status= 0");

        while ($row = $result->fetch_assoc()) {
            $trans = substr($row['reference_number'], 0, 2);
            if ($trans == "BB") {
                $row = $BeginningBalance->rows($row['reference_number']);
                $bb_id = $row['bb_id'];

                
                $terms = $customer_terms;
                $sales_date = $row['bb_date'];
                $sales_date = $row['sales_date'];
                $due = date('Y-m-d', strtotime($sales_date. ' + '.$terms.' days'));

                $row['payment'] =  number_format($CustomerPayment->total_paid($bb_id, "BB"), 2);
                $row['balance'] = number_format($BeginningBalance->bb_balance_ar($bb_id), 2);
                $row['ref_id'] = "BB-" . $bb_id;
                $row['sales_date'] = $sales_date;
                $row['pmamount'] = $CustomerPayment->total_paid($bb_id, "DR");
                $row['dramount'] = $Sales->total($bb_id);
                $row['due_date'] = $due;
                $rows[] = $row;
            } else {
                $row = $Sales->rows($row['reference_number']);
                $sales_id = $row['sales_id'];
                $bal = $Sales->dr_balance($sales_id);
                if ($bal > 0) {
                    $terms = $row['terms'] > 0 ? $row['terms'] : $customer_terms;
                    $sales_date = $row['sales_date'];
                    $due = date('Y-m-d', strtotime($sales_date. ' + '.$terms.' days'));

                    $row['payment'] = number_format($CustomerPayment->total_paid($sales_id, "DR"), 2);
                    $row['balance'] = number_format($bal, 2);
                    $row['amount'] = number_format($Sales->total($sales_id));
                    $row['ref_id'] = "DR-" . $sales_id;
                    $row['due_date'] = $due;
                    $row['sales_date'] = $sales_date;
                    $rows[] = $row;
                }
            }
        }
        return $rows;
    }

    public function getSoa()
    {
        $Customers = new Customers;
        $id = $_POST['id'];
        $total = $_POST['total'];
        $row = $Customers->view($id);
        $terms = $row['customer_terms'];
        $row['terms'] = $terms > 1 ? $terms." days" : $terms." day";
        $row['total'] = number_format($total, 2);
        $row['amount_to_words'] = "<i>".strtoupper($this->convertNumberToWord($total))." ONLY</i>";
        $row['today'] = date('M d, Y', strtotime($this->getCurrentDate()));
        $rows[] = $row;
        return $rows;
    }


    function convertNumberToWord($num = false)
    {
        $num = str_replace(array(',', ' '), '', trim($num));
        if (!$num) {
            return false;
        }
        $num = (int) $num;
        $words = array();
        $list1 = array(
            '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array(
            '', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
        );
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ($tens < 20) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
            } else {
                $tens = (int)($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return implode(' ', $words);
    }
}
