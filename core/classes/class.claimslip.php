<?php
class ClaimSlip extends Connection
{
    private $table = 'tbl_claim_slips';
    public $pk = 'claim_slip_id';
    public $name = 'reference_number';

    public $module_name = "Claim Slip";
    public $inputs = [];

    public function add()
    {
        $form = array(
            $this->name     => $this->clean($this->inputs[$this->name]),
            'sales_id'      => $this->inputs['sales_id'],
            'total_amount'  => $this->inputs['total_amount'],
            'status'        => 'S'
        );
        return $this->insertIfNotExist($this->table, $form, '', 'Y');
    }

    public function finish()
    {
        $sales_id = $this->inputs['sales_id'];
        $form = array(
            'status' => 'F',
        );
        return $this->update($this->table, $form, "sales_id = '$sales_id'");
    }

    public function generate()
    {
        $fetch = $this->select($this->table, "max(claim_slip_id) + 1 as max_id");
        $row = $fetch->fetch_assoc();
        if($row['max_id'] > 0)
            return sprintf("%'.06d", $row['max_id']);
        return sprintf("%'.06d", 1);
    }

    public function show()
    {
        $Customers = new Customers;
        $Sales = new Sales;
        $param = isset($this->inputs['param']) ? $this->inputs['param'] : null;
        $rows = array();
        $result = $this->select($this->table, '*', $param);
        while ($row = $result->fetch_assoc()) {

            $sales_id = $row['sales_id'];
            $fetch_sales = $this->select("tbl_sales", "*", "sales_id = '$sales_id'");
            $sales_row = $fetch_sales->fetch_array();

            $customer_name = $sales_row['customer_id'] > 0 ? $Customers->name($sales_row['customer_id']) : 'Walk-in';
            $row['customer'] = $customer_name;
            // $total = $Sales->total($sales_id);
            $total = $row['total_amount'];
            $row['total'] = number_format($total, 2);
            $row['total_nonformat'] = $total;
            $row['reference_number'] = $row['reference_number'];
            $row['sales_num'] = $sales_row['reference_number'];
            $row['sales_type'] = $sales_row['sales_type'];
            $row['customer_id'] = $sales_row['customer_id'];
            $row['for_pick_up'] = $sales_row['for_pick_up'];

            $rows[] = $row;
        }
        return $rows;
    }

    public function get_claimslip()
    {
        $sales_id = $this->inputs['sales_id'];
        $result = $this->select($this->table, $this->name, "sales_id = '$sales_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function runner()
    {
        $Sales = new Sales;
        $result = $this->select("tbl_sales", "sales_id", "withdrawal_status = '1'");
        while($row = $result->fetch_assoc()){
            $reference_number = $this->generate();
            $sales_id = $row['sales_id'];
            $total_amount = $Sales->total($sales_id);
            $this->inputs['reference_number'] = $reference_number;
            $this->inputs['sales_id'] = $sales_id;
            $this->inputs['total_amount'] = $total_amount;
            $this->add();            
        }
    }
}