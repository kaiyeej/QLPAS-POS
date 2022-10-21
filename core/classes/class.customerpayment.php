<?php
class CustomerPayment extends Connection
{
    private $table = 'tbl_customer_payment';
    public $pk = 'cp_id';
    public $name = 'reference_number';

    private $table_detail = 'tbl_customer_payment_details';
    public $pk2 = 'cpd_id';
    public $fk_det = 'ref_id';

    public function add()
    {
        $form = array(
            $this->name     => $this->clean($this->inputs[$this->name]),
            'customer_id'   => $this->inputs['customer_id'],
            'payment_type'  => $this->inputs['payment_type'],
            'payment_date'  => $this->inputs['payment_date'],
            'check_date'    => $this->inputs['check_date'],
            'check_number'  => $this->inputs['check_number'],
            'check_bank'    => $this->inputs['check_bank'],
            'payment_option_id' => $this->inputs['payment_option_id'],
            'encoded_by' => isset($this->inputs['encoded_by']) ? $this->inputs['encoded_by'] :  $_SESSION['user']['id']
        );
        return $this->insertIfNotExist($this->table, $form, '', 'Y');
    }

    public function edit()
    {
        $form = array(
            'payment_type'   => $this->inputs['payment_type'],
            'payment_date'    => $this->inputs['payment_date'],
            'remarks'       => $this->inputs['remarks'],
            'encoded_by' => $_SESSION['user']['id']
        );
        return $this->updateIfNotExist($this->table, $form);
    }

    public function show()
    {
        $Customers = new Customers;
        $Users = new Users;
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['customer'] = $Customers->name($row['customer_id']);
            $row['total'] = number_format($this->total($row['cp_id']),2);
            $row['encoded_name'] = $Users->getUser($row['encoded_by']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function view()
    {
        $Customers = new Customers;
        $primary_id = $this->inputs['id'];
        $result = $this->select($this->table, "*", "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        $row['customer'] = $Customers->name($row['customer_id']);
        $row['payment_type'] = $row['payment_type'];
        $row['paymenttype'] = ($row['payment_type'] == "C" ? "Cash" : ($row['payment_type'] == "H" ?"Check" : "Online Payment"));
        return $row;
    }

    public function show_ref()
    {
        $Sales = new Sales;
        $BeginningBalance = new BeginningBalance;
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();

        $result = $this->select("tbl_sales","reference_number","customer_id='$param' AND status='F' AND paid_status='0' AND sales_type='H' UNION ALL SELECT reference_number FROM tbl_beginning_balance WHERE bb_ref_id='$param' AND bb_module='AR' AND bb_paid_status= 0");

        while ($row = $result->fetch_assoc()) {
            $trans = substr($row['reference_number'], 0, 2);
            if($trans == "DR"){
                $sales_id = $Sales->pk_by_name($row['reference_number']);
                $bal = $Sales->dr_balance($sales_id);
                if($bal > 0){
                    $row['reference'] = $row['reference_number']." (₱".number_format($bal,2).")";
                    $row['ref_id'] = "DR-".$sales_id;
                    $rows[] = $row;
                }
            }else{
                $bb_id = $BeginningBalance->pk_by_name($row['reference_number']);
                $row['reference'] = $row['reference_number']." (₱".number_format($BeginningBalance->bb_balance_ar($bb_id),2).")";
                $row['ref_id'] = "BB-".$bb_id;
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function remove()
    {
        $ids = implode(",", $this->inputs['ids']);
        $result = $this->select($this->table_detail, "ref_id,type", "$this->pk IN($ids)");
        while ($row = $result->fetch_assoc()) {
            if($row['type'] == "DR"){
                $form_ = array(
                    'paid_status'   => '0',
                );
                $this->update('tbl_sales', $form_, "sales_id = '$row[ref_id]'");
            }else{
                $form_ = array(
                    'bb_paid_status'   => '0',
                );
                $this->update('tbl_beginning_balance', $form_, "bb_id = '$row[ref_id]' AND bb_module = 'AR'");
            }
        }

        return $this->delete($this->table, "$this->pk IN($ids)");
    }

    public function generate()
    {
        return 'CP-' . date('ymdHis');
    }

    public function finish()
    {
        
        $primary_id = $this->inputs['id'];

        $result = $this->select($this->table_detail, "ref_id, type, sum(amount) as total", "$this->pk = '$primary_id' GROUP BY ref_id,type");
        while($row = $result->fetch_array()){
            if($row['type'] == "DR"){
                $Sales = new Sales;
                $dr_paid = $Sales->dr_balance($row['ref_id']) - ($row['total']+$this->total_paid($primary_id, 'DR'));
                if($dr_paid <= 0){
                    $form_ = array(
                        'paid_status'   => 1,
                    );
                    $this->update('tbl_sales', $form_, 'sales_id='.$row['ref_id'].'');
                }
            }else{
                $BeginningBalance = new BeginningBalance;
                $bb_paid = $BeginningBalance->bb_balance_ar($row['ref_id']) - ($row['total']+$this->total_paid($primary_id, 'BB'));
                if($bb_paid <= 0){
                    $form_ = array(
                        'bb_paid_status' => 1,
                    );
                    $this->update('tbl_beginning_balance', $form_, "bb_id=".$row['ref_id']." AND bb_module='AR'");
                }
            }
        }

        $form = array(
            'status' => 'F',
        );
        return  $this->update($this->table, $form, "$this->pk = '$primary_id'");

    }

    public function pk_by_name($name = null)
    {
        $name = $name == null ? $this->inputs[$this->name] : $name;
        $result = $this->select($this->table, $this->pk, "$this->name = '$name'");
        $row = $result->fetch_assoc();
        return $row[$this->pk] * 1;
    }

    public function name($primary_id)
    {
        $result = $this->select($this->table, $this->name, "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function reference($primary_id)
    {
        $result = $this->select($this->table, "*", "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row['reference_number']." (Amount: ".$this->total($primary_id).")";
    }

    public function add_detail()
    {
        $primary_id = $this->inputs[$this->pk];
        $fk_det     = substr($this->inputs[$this->fk_det], 3);
        $type = substr($this->inputs[$this->fk_det], 0, 2);

        if($type == "DR"){
            $Sales = new Sales;
            $balance = ($Sales->total($fk_det)) - (($this->inputs['amount'])+($this->total_saved($primary_id,$fk_det, 'DR'))+$this->total_paid($fk_det, 'DR'));
        }else{
            $BeginningBalance = new BeginningBalance;
            $balance = ($BeginningBalance->total($fk_det)) - (($this->inputs['amount'])+($this->total_saved($primary_id,$fk_det, 'BB'))+$this->total_paid($fk_det, 'BB'));
        }

        if($balance < 0){
            return 3;
        }else{
            $form = array(
                $this->pk => $this->inputs[$this->pk],
                $this->fk_det => $fk_det,
                'type' => $type,
                'amount' => $this->inputs['amount']
            );

            return $this->insert($this->table_detail, $form);
        }

    }

    public function show_detail()
    {
        $Sales = new Sales();
        $BeginningBalance = new BeginningBalance();
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table_detail, '*', $param);
        while ($row = $result->fetch_assoc()) {
            $row['ref_id'] = $row['type'] == "DR" ? $Sales->name($row['ref_id']) : $BeginningBalance->name($row['ref_id']);
            $row['amount'] = number_format($row['amount'],2);
            $rows[] = $row;
        }
        return $rows;
    }

    public function remove_detail()
    {
        $ids = implode(",", $this->inputs['ids']);
        return $this->delete($this->table_detail, "$this->pk2 IN($ids)");
    }

    public function total($primary_id)
    {
        $result = $this->select($this->table_detail, 'sum(amount)', "$this->pk = '$primary_id'");
        $row = $result->fetch_array();
        return $row[0];
    }

    public function total_paid($ref_id, $type)
    {
        $result = $this->select('tbl_customer_payment as h, tbl_customer_payment_details as d', 'sum(d.amount)', "d.ref_id = '$ref_id' AND h.status='F' AND h.cp_id=d.cp_id AND d.type='$type'");
        $row = $result->fetch_array();
        return $row[0];
    }

    public function total_per_sales($primary_id,$sales_id){
        $result = $this->select($this->table_detail, 'sum(amount)', "$this->pk = '$primary_id' AND sales_id = '$sales_id'");
        $row = $result->fetch_array();
        return $row[0];
    }

    public function cancel()
    {
        $ids = implode(",", $this->inputs['ids']);

        $result = $this->select($this->table_detail, "ref_id,type", "$this->pk IN($ids)");
        while ($row = $result->fetch_assoc()) {
            if($row['type'] == "DR"){
                $form_ = array(
                    'paid_status'   => '0',
                );
                $this->update('tbl_sales', $form_, "sales_id = '$row[ref_id]'");
            }else{
                $form_ = array(
                    'bb_paid_status'   => '0',
                );
                $this->update('tbl_beginning_balance', $form_, "bb_id = '$row[ref_id]' AND bb_module = 'AR'");
            }
        }

        $form = array(
            'status'   => 'C',
        );

        return $this->update($this->table, $form, "$this->pk IN($ids)");
    }
    

    public function addPaymentPOS(){

        $amount = $this->inputs['amount'];
        $totalPayable = $this->inputs['total_payment_amount'];
        $amount_paid = $this->getTotalPaymentPOS();

        if(($amount + $amount_paid) <= $totalPayable){
            $Sales = new Sales;
            $this->inputs['payment_date'] = $this->getCurrentDate();
            $this->inputs['remarks'] = $this->inputs['other_payment_reference'];
            $this->inputs['payment_type'] = "O";
            
            $primary_id = $this->add();
            $this->inputs[$this->pk] = $primary_id;
            $sales_num = $this->inputs['sales_num'];
            $param = "reference_number = '$sales_num'";
            $this->inputs[$this->fk_det] = $Sales->getID($param);
            // return $this->add_detail();

            $form = array(
                $this->pk => $this->inputs[$this->pk],
                $this->fk_det => $this->inputs[$this->fk_det],
                'amount' => $this->inputs['amount'],
                'type' => 'DR'
            );

            return $this->insert($this->table_detail, $form);
        }else{
            return -1;
        }
    }

    public function getTotalPaymentPOS(){
        $Sales = new Sales;
        $sales_num = $this->inputs['sales_num'];
        $param = "reference_number='$sales_num'";
        $sales_id = $Sales->getID($param);
        $result = $this->select($this->table_detail, 'sum(amount)', "ref_id='$sales_id' and type='DR' ");
        $row = $result->fetch_array();
        return $row[0];
    }

    public function remove_payments(){

        $Settings = new Settings();
        $setting_row = $Settings->view();
        
        $access_code = $this->inputs['access_code'];

        if ($setting_row['module_remove_online_payment'] == $access_code) {
            $Sales = new Sales;
            $reference_number = $this->inputs['reference_number'];
            $param = "reference_number='$reference_number'";
            $sales_id = $Sales->getID($param);
            
            $rows = array();
            $result = $this->select($this->table_detail, $this->pk, "ref_id='$sales_id' AND type='DR'");
            while($row = $result->fetch_array()){
                $rows[] = $row[0];
            }

            $ids = implode(',',$rows);

            if(sizeof($rows) > 0){
                return $result = $this->delete($this->table,  "cp_id IN($ids)");
            }else{
                return 1;
            }
        }else{
            return -2;
        }
        
    }

    public function finishCustomerPaymentOfDRPOS($sales_id){
        $rows = array();
        $result = $this->select($this->table_detail, $this->pk, "ref_id='$sales_id' AND type='DR'");
        while($row = $result->fetch_array()){
            $rows[] = $row[0];
        }

        if(sizeof($rows) > 0){
            $form = array(
                'status' => 'F'
            );
            return $this->update($this->table, $form, "cp_id IN(" . implode(',',$rows) . ")");
        }else{
            return 1;
        }
    }

    public function total_saved($primary_id,$ref_id,$type){
        $result = $this->select($this->table_detail, 'sum(amount)', "$this->pk = '$primary_id' AND ref_id = '$ref_id' AND type='$type'");
        $row = $result->fetch_array();
        return $row[0];
    }

    
    public function getHeader()
    {
        $Customers = new Customers;
        $id = $_POST['id'];
        $result = $this->select($this->table, "*", "$this->pk='$id'");
        $row = $result->fetch_assoc();
        $row['payment_date_mod'] = date("F j, Y", strtotime($row['payment_date']));
        $row['customer_name'] = $Customers->name($row['customer_id']);
        $rows[] = $row;
        return $rows;
    }
    public function getPrintDetails()
    {
        $id = $_POST['id'];
        $Sales = new Sales;
        $BeginningBalance = new BeginningBalance;
        $rows = array();
        $result = $this->select($this->table_detail, "*", "$this->pk='$id'");
        while ($row = $result->fetch_assoc()) {
            $row['reference'] = $row['type'] == "DR" ? $Sales->name($row['ref_id']) : $BeginningBalance->name($row['ref_id']);
            $rows[] = $row;
        }
        return $rows;
    }
}
