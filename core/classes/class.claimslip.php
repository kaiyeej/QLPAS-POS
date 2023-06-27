<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

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
            'withdrawal_id' => 0,
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

    public function finish_stock_releasal()
    {
        $primary_id = $this->inputs[$this->pk];
        $encoded_by = $this->inputs['encoded_by'];
        $form = array(
            'status' => 'F',
            'checked_by' => $encoded_by
        );
        return $this->update($this->table, $form, "$this->pk = '$primary_id'");
    }

    public function cancel_stock_releasal()
    {
        $primary_id = $this->inputs[$this->pk];
        $encoded_by = $this->inputs['encoded_by'];
        $access_code = $this->inputs['access_code'];

        $Settings = new Settings();
        $setting_row = $Settings->view();
        if ($setting_row['module_cancel'] == $access_code) {
            $fetch = $this->select($this->table, "withdrawal_id", "$this->pk = '$primary_id'");
            $withdrawal_id = $fetch->fetch_array();

            $form = array(
                'status' => 'S',
                'checked_by' => 0,
                'withdrawal_id' => 0
            );
            $result = $this->update($this->table, $form, "$this->pk = '$primary_id'");

            if ($result == 1) {
                // delete stock withdrawal
                return $this->delete("tbl_stock_withdrawal", "withdrawal_id='$withdrawal_id[0]'");
            } else {
                return 0;
            }
        } else {
            return -2;
        }
    }

    public function generate()
    {
        $fetch = $this->select($this->table, "max(claim_slip_id) + 1 as max_id");
        $row = $fetch->fetch_assoc();
        if ($row['max_id'] > 0)
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
            $row['claim_sales_type'] = $sales_row['sales_type'] == "C" ? "Cash" : "Charge";
            $row['customer_id'] = $sales_row['customer_id'];
            $row['for_pick_up'] = $sales_row['for_pick_up'];

            $rows[] = $row;
        }
        return $rows;
    }

    public function update_claim_slip()
    {
        $form = array(
            'withdrawal_id'   => $this->inputs['withdrawal_id'],
            'status' => 'F'
        );
        $primary_id = $this->inputs[$this->pk];
        $result = $this->update($this->table, $form, "$this->pk = '$primary_id'");
        return $result;
    }

    public function get_claimslip()
    {
        $sales_id = $this->inputs['sales_id'];
        $withdrawal_id = $this->inputs['withdrawal_id'];
        $result = $this->select($this->table, $this->name, "sales_id = '$sales_id' AND withdrawal_id = '$withdrawal_id'");
        $row = $result->fetch_assoc();
        return $row[$this->name];
    }

    public function get_row($primary_id, $field)
    {
        $result = $this->select($this->table, $field, "$this->pk = '$primary_id'");
        $row = $result->fetch_assoc();
        return $row[$field];
    }

    public function runner()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $response['slips'] = [];
        $Sales = new Sales;
        $result = $this->select("tbl_sales", "sales_id", "for_pick_up = '1'");
        while ($row = $result->fetch_assoc()) {

            $ClaimSlip = new ClaimSlip;
            $reference_number = $ClaimSlip->generate();
            $sales_id = $row['sales_id'];
            $total_amount = $Sales->total($sales_id);

            if ($this->status_checker($sales_id) == 'S') {
                $ClaimSlip->inputs['reference_number'] = $reference_number;
                $ClaimSlip->inputs['sales_id'] = $sales_id;
                $ClaimSlip->inputs['total_amount'] = $total_amount;
                $ClaimSlip->add();

                array_push($response['slips'], $ClaimSlip->inputs);
            }
        }
        return $response;
    }

    public function status_checker($sales_id)
    {
        $StockWithdrawal = new StockWithdrawal;
        $result2 = $this->select("tbl_sales_details", 'sales_detail_id', "sales_id = '$sales_id'");
        $remain_qty = 0;
        while ($row2 = $result2->fetch_assoc()) {
            $remain_qty += $StockWithdrawal->remaining_qty($row2['sales_detail_id']);
        }

        return $remain_qty > 0 ? "S" : "F";
    }

    public function finish_checker()
    {
        $StockWithdrawal = new StockWithdrawal;

        $result = $this->select($this->table, "*", "status = 'S'");
        while ($row = $result->fetch_assoc()) {

            $sales_id = $row['sales_id'];
            $result2 = $this->select("tbl_sales_details", 'sales_detail_id', "sales_id = '$sales_id'");
            $remain_qty = 0;
            while ($row2 = $result2->fetch_assoc()) {
                $remain_qty += $StockWithdrawal->remaining_qty($row2['sales_detail_id']);
            }

            if ($remain_qty == 0) {
                $this->update($this->table, ["status" => "F"], "sales_id = '$sales_id'");
            }
        }
        return 1;
    }

    public function count_new_claim_slip(){
        $result = $this->select($this->table, "count(claim_slip_id)", "status = 'F' and withdrawal_id > 0 and checked_by = 0");
        $row = $result->fetch_array();
        return $row[0];
    }

    public function get_new_claim_slip(){
        $count = $this->count_new_claim_slip();
        echo "data: " . $count . "\n\n";
        flush();
        sleep(2);
        if(connection_aborted()) exit();
    }
}
